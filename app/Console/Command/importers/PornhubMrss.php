<?php
// Folders to create in ROOT
// mkdir -p feeds/Pornhub/items
// mkdir -p feeds/Pornhub/mrss/parts
// mkdir -p feeds/Pornhub/deleted/items
// mkdir -p feeds/Pornhub/deleted/parts

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class PornhubMrss  {
	const CATEGORIES = 1;
	const TAGS = 2;
	const SEGMENTS = 3;
	
    public $settings;
	public $shell;
	
    public function __construct($settings, $shell) {
		$this->settings = $settings;
		$this->shell = $shell;
    }
	
    /*
    * Downloads Video Feed
	* from ROOT: cake mrss run Pornhub downloadSource
    */
    public function downloadSource() {		
		$partSize = '15M';
		//These vars are used in treatxml.sh		
		putenv("feed_part_size=$partSize");
		putenv("feed_url=" . $this->settings['Site']['feed_url'] );
		putenv("feed_origin_site=" . $this->settings['Site']['name']);
		putenv("feed_file_name=" . $this->settings['Site']['feed_filename']);
		putenv("feed_path_mrss=" . $this->settings['feedMrssFolder']);
		putenv("feed_path_parts=" . $this->settings['partsFolder']);
		putenv("feed_mrss_prefix=" . $this->settings['partPrefix']);

		shell_exec("bash " . __DIR__ . "/treatxml.sh");		
    }
    
    /*
    * Downloads Video Feed
	 * cake mrss run Pornhub downloadDeleted
    */
    public function downloadDeleted () {
		$partSize = '2M';
		//These vars are used in treatxml.sh		
		putenv("feed_part_size=$partSize");
		putenv("feed_url=" . $this->settings['Site']['deleted_feed_url'] );
		putenv("feed_origin_site=" . $this->settings['Site']['name']);
		putenv("feed_file_name=" . $this->settings['Site']['deleted_feed_filename']);
		putenv("feed_path_mrss=" . $this->settings['deletedFolder']);
		putenv("feed_path_parts=" . $this->settings['deletedPartsFolder']);
		putenv("feed_mrss_prefix=" . $this->settings['partPrefix']);

		shell_exec("bash " . __DIR__ . "/treatxml.sh");		
    }
	
    /*
    * Stores in FileSystem Video Feed and Deleted
     * cake mrss run Pornhub storeFS
    */
    public function storeFS() {
		ini_set('memory_limit', '65536M');
		
		//@TODO Use cakephp instead of shell_exec
		$numberOfParts = shell_exec('ls -lR '.$this->settings['partsFolder'].$this->settings['partPrefix'].'* | grep ^- | wc -l');
		//debug($this->settings);exit;
		$partsToParse = $this->settings['Site']['last_mrss_part_parsed'];
		$partsToParseLimit = $this->settings['Site']['last_mrss_part_parsed'] -1;
		
		//check if the last mrss part to parse has reach the limit
		if($partsToParseLimit < 0 ) {
			$partsToParseLimit = 0;
		}
		//Get the next mrss part to start with
		$next_mrss_parts_to_parse_from = $partsToParseLimit;
		if($next_mrss_parts_to_parse_from <= 0) {
			$next_mrss_parts_to_parse_from = $numberOfParts;
		}
		
		for($partsToParse; $partsToParse > $partsToParseLimit; $partsToParse--) {
			$mrssPartName = $this->settings['partPrefix'].sprintf("%02d", $numberOfParts - $partsToParse);				
			$mrss = new MrssParser($this->settings['partsFolder'].$mrssPartName);
			
			foreach($mrss as $key => $item) {
				if($this->__meetsCondition($item)) {
					//map to local structure
					$mappedItem = $this->__mapItem($item);					
					
					//get subdirectory structure
					$subdir = date("Y/m/d", $mappedItem["Video"]["pub_date"]);					
					$itemFileName = md5($mappedItem['Video']['url']);
					
					$path = $this->settings['storageFolder'] . $subdir . DS;
					$mappedItem['Video']['path'] = $path;
					$mappedItem['Video']['filename'] = $itemFileName;					
					//$mappedItem['Video']['mrss_part'] = $mrssPartName;					
					
					//Creating directory and saving					
					new Folder($path, true, 0775);					
					file_put_contents($path.$itemFileName, json_encode($mappedItem));					
				}			
			}
		}
		
		$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
		$this->shell->Node->Video->Site->saveField('last_mrss_part_parsed', $next_mrss_parts_to_parse_from);
    }
	
    /*
    * Stores videos in DB
	* cake mrss run Pornhub storeDB
    */
    public function storeDB() {
		$maxInserts = $this->settings['Site']['max_video_insert'];
		$readAttemps = 100;
		$folderToRead = empty($this->settings['Site']['last_video_folder_inserted']) ? '2007/10/16' : $this->settings['Site']['last_video_folder_inserted'];
		
		while ($readAttemps > 0) {
			$readAttemps --;			
			$filesToRead = $this->__getFilesToRead( $this->settings['storageFolder'].$folderToRead);
					
			foreach($filesToRead as $fileName) {
				$videoFile = $this->settings['storageFolder'].$folderToRead.DS.$fileName;
				$videoData = json_decode(file_get_contents($videoFile), TRUE);		
				
				$videoExists = $this->shell->Node->Video->findByUrl($videoData['Video']['url']);
				if(empty($videoExists)) {
					$videoData['Video']['file_md5'] = md5_file($videoData['Video']['path'].$videoData['Video']['filename']);
					$this->shell->Node->create();
					$this->shell->Node->saveNode($videoData, 'video');
					$maxInserts--;
					
					if($maxInserts <= 0) {
						$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
						$this->shell->Node->Video->Site->saveField('last_video_folder_inserted', $folderToRead);
						return TRUE;
					}
				} else {
					//Check if video json file has changed location due to a re-release in PH			
					if($videoExists['Video']['path'] != $videoData['Video']['path']) {
						$this->shell->Node->Video->id = $videoExists['Video']['id'];
						$this->shell->Node->Video->saveField('path', $videoData['Video']['path']);
					}
				}		
			}
			
			//do not let the script to get to today's folder
			$today = date('Y/m/d', time());
			$nextDay = date('Y/m/d', strtotime($folderToRead . ' + 1 day'));
			if ($today != $nextDay) {
				$folderToRead = date('Y/m/d', strtotime($folderToRead . ' + 1 day'));
			} else {
				break;
			}
		}
		$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
		$this->shell->Node->Video->Site->saveField('last_video_folder_inserted', $folderToRead);
			
		return TRUE;
    }
	
	/*
	 * Updates DB
	 * cake mrss run Pornhub updateDB
	 */
    public function updateDB() {
		
		$videosToUpdate = $this->shell->Node->Video->find('all', 
				array(
					'conditions' => array('Video.id >' => $this->settings['Site']['last_updated_videoid'], 'Video.status' => 'active'),
					'fields' => array('Video.id', 'Video.path', 'Video.filename', 'Video.file_md5', 'Video.node_id'),
					'limit' => $this->settings['Site']['max_video_update'],
				)
		);
		
		if(empty($videosToUpdate)) {
			$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
			$this->shell->Node->Video->Site->saveField('last_updated_videoid', 0);
		} else {
			foreach($videosToUpdate as $v) {
				$video = $v['Video'];
				//First check if video is reported as deleted
				$path = $this->__getDeletedSubdir($video['filename']);
				if(file_exists($path.$video['filename'])) {
					$this->shell->Node->Video->id = $video['id'];
					$this->shell->Node->Video->saveField('status', 'deleted');
					continue;
				}
				//Update only if the jsonfile has changed
				$videoFile = $video['path'] . $video['filename'];
				if($video['file_md5'] != md5_file($videoFile)) {
					//getting json data
					$videoData = json_decode(file_get_contents($videoFile), TRUE);
					$videoData['Node']['id'] = $video['node_id'];
					$videoData['Video']['id'] = $video['id'];
					//setting id for cakephp save
					$videoData['Video']['file_md5'] = md5_file($videoFile);
					$videoData['Video']['node_id'] = $video['node_id'];
					//unsetting values we don`t want to reset (local values)
					unset($videoData['Video']['local_votes']);
					unset($videoData['Video']['local_views']);
					unset($videoData['Video']['local_rating']);
					
					$this->shell->Node->id = $video['node_id'];
					$this->shell->Node->saveNode($videoData, 'video');
				}
			}
			
			//Sets last video id checked for updates
			$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
			$this->shell->Node->Video->Site->saveField('last_updated_videoid',  $video['id']);
		}
    }
	
	/*
	 * Stores deleted files in file system
	 * cake mrss run Pornhub storeDeletedFS
	 */
	public function storeDeletedFS() {	
		$mrssPartName = $this->settings['deletedPartsFolder'].$this->settings['partPrefix'].sprintf("%02d", $this->settings['Site']['next_deleted_to_parse']);
		$this->shell->Node->Video->Site->id = $this->settings['Site']['id'];
		
		if(file_exists($mrssPartName)) {
			$mrss = new MrssParser($mrssPartName);
			foreach($mrss as $key => $item) {
				$deletedFileName = md5($item['url']);
				$path = $this->__getDeletedSubdir($deletedFileName);
				
				if(!file_exists($path.$deletedFileName)) {
					new Folder($path, true, 0775);
					touch($path.$deletedFileName);
				}				
			}
			
			$this->shell->Node->Video->Site->saveField('next_deleted_to_parse',  $this->settings['Site']['next_deleted_to_parse'] + 1);
		} else {
			$this->shell->Node->Video->Site->saveField('next_deleted_to_parse',  0);
		}		
		
	}
	
	private function __getDeletedSubdir($filename) {		
		return $this->settings['deletedFolderItems'] . substr($filename, 0,2) . '/' . substr($filename, 2,1) . '/';
	}
	
	private function __meetsCondition(&$item) {		
		
		if (empty($item["media:title"]["$"]) ||
				$item["phn:video_spam"] == 1 ||
				//$item["phn:community"] == 0 ||
				empty($item["media:category"]) ||
				empty($item["media:keywords"]) ||
				(preg_match('/\b(' . 'big-dick|bigdick|big dick' . ')\b/i', $item["media:category"]) != 1 && preg_match('/\b(' . 'big-dick|bigdick|big dick' . ')\b/i', $item["media:keywords"]) != 1 ) ||				
				($item["bing:ratings"]["@average"] < 0.7)
		) {
			return FALSE;
		}
				
		return TRUE;		
						
	}
	
	/*
	 * @param Array item
	 */
	private function __mapItem($item) {
		$isMobileCompatible = isset($item["phn:mobile"]) ? $item["phn:mobile"] : 0;
		$titleSlug = strtolower(Inflector::slug($item["media:title"]["$"], '-'));
		$n_favorites = isset($item["phn:favorite_count"]) ? $item["phn:favorite_count"] : 0;
		$thumbInfo = pathinfo($item["media:thumbnail"]["@url"]);
		$embedLink = parse_url($item["link"]);		
		// we get $viewkey from this following line
		parse_str($embedLink['query']);								
		
		if(preg_match('/\b(' . 'gay' . ')\b/i', $item["media:category"]) == 1) {
			$segment = 'gay';
		} elseif (preg_match('/\b(' . 'tranny|transexual' . ')\b/i', $item["media:category"]) == 1) {
			$segment = 'transexual';
		} else {
			$segment = 'straight';
		}
		
		$terms = array(
			self::CATEGORIES => $item["media:category"],
			self::TAGS => $item["media:keywords"],
			self::SEGMENTS => $segment,
		);		
				
		//Map Taxonomies (Categories, Tags and Segment)		
		$itemTaxonomies = $this->__processTaxonomies($terms);
						
		// Map according to notesforcroogo.txt	
		$mappedItem = array(
			'Node' => array(
				'parent_id' => '',
				'id' => '',
				'title' => $item["media:title"]["$"],
				'slug' => $titleSlug,
				'excerpt' => '',
				'body' => '',
				'comment_status' => '2',
				'status' => '1',
				'promote' => '0',
				'user_id' => '1',
				'created' => ''
			),
			'Role' => array(
				'Role' => ''
			),
			'Video' => array(
				'id' => '',
				'externalId' => $viewkey,
				'site_id' => '1',
				'status' => 'active',
				'url' => $item["link"],
				'duration' => $item["media:content"]["@duration"],
				'votes' => $item["bing:ratings"]["@count"],
				'local_votes' => '0',
				'views' => $item["bing:views"]["@count"],
				'local_views' => '0',
				'rating' => $item["bing:ratings"]["@average"],
				'local_rating' => '0',
				'pub_date' => strtotime($item["pubDate"]),
				'thumb_path' => $item["media:thumbnail"]["@url"],
				'thumb_width' => $item["media:thumbnail"]["@width"],
				'thumb_height' => $item["media:thumbnail"]["@height"],
				'mobile_compatible' => $isMobileCompatible,
				'path' => '', //defined in storeFS Function
				'filename' => '', //defined in storeFS Function
				'node_id' => '' // automatically set to new create node
			),
			// Vocabularies 1:Categories 2:Tags 3:Segments
			'TaxonomyData' => $itemTaxonomies
		);										
		return $mappedItem;		
	}
	/*
	 * Creates new terms if needed then return Taxonomy Arrar format to be used with the Node
	 */
	private function __processTaxonomies ($terms) {
		$taxonomies = array();				
		// Process terms
		foreach($terms as $vid => $t) {
			if(!empty($t)) {
				$words = explode(',', $t);

				foreach($words as $w) {
					if(empty($w)) continue;
					
					// Find Term in Model
					$term = $this->shell->Term->find('first', array(
						'conditions' => array('Term.slug' => Inflector::slug($w, '-')),
						'fields' => array('Term.id'),
						'contain' => array(),
					)); 

					//If term exists
					if(!empty($term['Term']['id'])) {
						$tid = $term['Term']['id'];
						//check if it belongs to Vocabulary Categories			
						
						if($this->shell->Taxonomy->termInVocabulary($tid,$vid) == false) {
							//Add it to vocabulary
							$this->__addTermToVocabulary($tid, $vid);
						}					
					} else {
						//create term and add it to vocabulary
						$tid = $this->__createTerm($w, $vid);
					}

					//Prepare taxonomies array
					if($tid != false) $taxonomies[$vid][] = $tid;					
				}				
			}
		}
		return $taxonomies;
	}
	
	private function __createTerm($termName, $vid) {
		$data = array(
			'title' => $termName,
			'slug' => Inflector::slug($termName, '-'),
			'description' => 'Created from Pornhub'
		);
		
		$termId = $this->shell->Term->saveAndGetId($data);				
		
		if($termId != false) $this->__addTermToVocabulary($termId, $vid);
		
		return $termId;
	}
	
	private function __addTermToVocabulary($termId, $vid) {				
		$this->shell->Term->Taxonomy->Behaviors->load('Tree', array(
			'scope' => array(
				'Taxonomy.vocabulary_id' => (int) $vid,
			),
		));				
		
		$taxonomy = array(
			'parent_id' => null,
			'term_id' => (int) $termId,
			'vocabulary_id' => $vid,
		);
		$this->shell->Term->Taxonomy->create();
		$this->shell->Term->Taxonomy->save($taxonomy);				
	}
	
	//limit1 one must have a higher value than limit2
	private function __getFoldersToRead($limit1, $limit2){
		$folders = array();
		$day = 24 * 60 * 60;				
		
		for($limit1; $limit1 >= $limit2; $limit1--) {
			$folders[] = $this->settings['storageFolder'] . date('Y/m/d', time() - ($limit1 * $day)) .'/'; //one day ago			
		}
		
		return $folders;
		
	}
	/*
	 * Get json files to read
	 */
	private function __getFilesToRead ($folder) {
		if(is_dir($folder)) {
			$allFiles = scandir($folder);
			$filesToExclude = array('.', '..');
			return array_diff($allFiles, $filesToExclude);
		}
		
		return array();
	}
}

