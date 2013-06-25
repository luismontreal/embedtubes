<?php
class PornhubMrss  {
    public $settings;
	
    public function __construct($settings) {
	$this->settings = $settings;
    }
	
    /*
    * Downloads Video Feed
    */
    public function downloadSource() {
	set_time_limit(0);
	$settings = $this->_getSettings();
	
	$partSize = '15M';
	//These vars are used in treatxml.sh
	putenv("feed_part_size=$partSize");
	putenv("feed_url=$this->feedUrl");
	putenv("feed_origin_site=$this->originSite");
	putenv("feed_file_name=$this->feedFileName");
	putenv("feed_path_mrss=$this->feedMrssFolder");
	putenv("feed_path_parts=$this->partsFolder");
	putenv("feed_mrss_prefix=$this->partPrefix");
			
	shell_exec("bash " . $this->treatXmlScript);
	$this->out('Hey there ' . $this->args[0]);
    }
    
    /*
    * Downloads Video Feed
    */
    public function downloadDeleted() {
	$settings = $this->_getSettings();
	debug($settings);
	$this->out('Hey there ' . $this->args[0]);
    }
	
    /*
    * Stores in FileSystem Video Feed and Deleted
    */
    public function storeFS() {
	$this->out('Hey there ' . $this->args[0]);
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

}
