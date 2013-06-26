<?php
// Folders to create in ROOT
// mkdir -p feeds/Pornhub/items
// mkdir -p feeds/Pornhub/mrss/parts
// mkdir -p feeds/Pornhub/deleted/items
// mkdir -p feeds/Pornhub/deleted/parts
class PornhubMrss  {
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
    */
    public function storeFS() {		
		$numberOfParts = shell_exec('ls -lR '.$this->settings['partsFolder'].$this->settings['partPrefix'].'* | grep ^- | wc -l');			
		$partsToParse = 3; //@TODO must come from the DB
		$partsToParseLimit = 0; //@TODO must come from the DB
		
		for($partsToParse; $partsToParse > $partsToParseLimit; $partsToParse--) {
			$mrssPartName = $this->settings['partPrefix'].sprintf("%02d", $numberOfParts - $partsToParse);				
			$mrss = new MrssParser($this->settings['partsFolder'].$mrssPartName);
			
			foreach($mrss as $key => $item) {

				if($this->__meetsCondition($item)) {
					//map to local structure
					$mappedItem = $this->__mapItem($item);
					debug($mappedItem);exit;
					//store in filesystem			
					/*$subdir = date("Y/m/d", $mappedItem["video_embedded"]["pub_date"]);					
					$itemFileName = md5($mappedItem['video_embedded']['link']);
					
					$path = $this->settings['storageFolder'] . $subdir . '/';
					$mappedItem['video_embedded']['local_pathname'] = $path;
					$mappedItem['video_embedded']['local_filename'] = $itemFileName;
					$mappedItem['video_embedded']['mrss_part'] = $mrssPartName;
					LibFE_Helpers::makeDir($path);
					file_put_contents($path.$itemFileName, json_encode($mappedItem));*/
				}			
			}
		}
    }
	
    /*
    * Stores in DB
    */
    public function storeDB() {
	$this->out('Hey there ' . $this->args[0]);
    }
	
	/*
	 * Stores in DB
	 */
    public function updateDB() {
	$this->out('Hey there ' . $this->args[0]);
    }
	
	private function __meetsCondition(&$item) {		
		
		if (empty($item["media:title"]["$"]) ||
				$item["phn:video_spam"] == 1 ||
				$item["phn:community"] == 0 ||
				empty($item["media:category"]) ||
				empty($item["media:keywords"]) ||				
				($item["bing:ratings"]["@average"] < 0.8)
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
		
		$segmentTax = $this->shell->Term->findBySlug($segment);
		debug($segmentTax);exit;
		//Map Categories and Tags from data keywords
		//categories
		$itemCats = str_replace(',', '","', $item["media:category"]);
		$itemCats = '"' . $itemCats . '"';		
		
		//tags
		$itemTags = str_replace(',', '","', $item["media:keywords"]);
		$itemTags = '"' . $itemTags . '"';		
		
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
				'segment' => $segment,
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
			/*'TaxonomyData' => array(
				
			),*/
		);					
			
			return $mappedItem;
			
	}

}

