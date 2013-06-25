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
		//debug($this->shell->Node->saveNode($data, $typeAlias = self::DEFAULT_TYPE));
		exit;
		$partsToParse = 3; //@TODO must come from the DB
		$partsToParseLimit = 0; //@TODO must come from the DB
		
		for($partsToParse; $partsToParse > $partsToParseLimit; $partsToParse--) {
			$mrssPartName = $this->settings['partPrefix'].sprintf("%02d", $numberOfParts - $partsToParse);				
			$mrss = new MrssReader($this->settings['partsFolder'].$mrssPartName);
			
			foreach($mrss as $key => $item) {

				if($this->meetsCondition($item)) {
					//map to local structure
					$mappedItem = $this->mapItem($item);
					//store in filesystem			
					$subdir = date("Y/m/d", $mappedItem["video_embedded"]["pub_date"]);					
					$itemFileName = md5($mappedItem['video_embedded']['link']);
					
					$path = $this->settings['storageFolder'] . $subdir . '/';
					$mappedItem['video_embedded']['local_pathname'] = $path;
					$mappedItem['video_embedded']['local_filename'] = $itemFileName;
					$mappedItem['video_embedded']['mrss_part'] = $mrssPartName;
					LibFE_Helpers::makeDir($path);
					file_put_contents($path.$itemFileName, json_encode($mappedItem));
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
				preg_match('/\b(' . 'gay|tranny|transexual' . ')\b/i', $item["media:category"]) == 1 ||
				empty($item["media:keywords"]) ||
				preg_match('/\b(' . 'gay|tranny|transexual' . ')\b/i', $item["media:keywords"]) == 1 ||
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
		
		//Map Categories and Tags from data keywords
		//categories
		$itemCats = str_replace(',', '","', $item["media:category"]);
		$itemCats = '"' . $itemCats . '"';		
		
		//tags
		$itemTags = str_replace(',', '","', $item["media:keywords"]);
		$itemTags = '"' . $itemTags . '"';		
		// Map according to notesforcroogo.txt	
		/*$mappedItem = array(
			"title" => $item["media:title"]["$"],
			"description" => "",
			"keywords" => $item["media:keywords"],
			"duration" => $item["media:content"]["@duration"],			
			"status" => "active",
			"time_approved_on" => time(),
			"time_last_viewed" => "",
			"times_viewed" => "",
			"time_deleted" => 0,
			"video_bitrate" => 0,
			"extensions" => "",
			"is_mobile_compatible" => $isMobileCompatible,
			"site_id" => 0,
			"title_slug" => $titleSlug,
			"user_reviewed" => "reviewed",
			"thumb" =>  $thumbInfo['filename'],
			"media_path" => "",
			"exclude" => 0,
			"no_api" => 1,
			"meta_title" => "",
			"meta_description" => "",
			"isSpam" => $item["phn:video_spam"],
			"casting_closed" => 0,
			"video_embedded" => array(
				"rating" => $item["bing:ratings"]["@average"],
				"votes" => $item["bing:ratings"]["@count"],
				"views" => $item["bing:views"]["@count"],
				"n_favorites" => $n_favorites,
				"pub_date" => strtotime($item["pubDate"]),
				"origin_site" => $this->originSite,
				"link" => $item["link"],
				"thumb" => $item["media:thumbnail"]["@url"],
				"thumb_width" => $item["media:thumbnail"]["@width"],
				"thumb_height" => $item["media:thumbnail"]["@height"],
				"local_pathname" => "", //defined in storeFS Function
				"local_filename" => "", //defined in storeFS Function
				"mrss_part" => "", //defined in storeFS Function
			),
			"actors" => $item["phn:actors"], //defined in meetsCondition Function
			"categories" => $categories,
			"tags" => $tags,
				
			);
			*/
			return $mappedItem;
			
	}

}

