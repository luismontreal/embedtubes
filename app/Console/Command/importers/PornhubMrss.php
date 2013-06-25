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
		$partSize = '15M';
		//These vars are used in treatxml.sh
		putenv("feed_part_size=$partSize");
		putenv("feed_url=$this->settings['Site']['feed_url']");
		putenv("feed_origin_site=$this->settings['Site']['name']");
		putenv("feed_file_name=$this->settings['Site']['feed_filename']");
		putenv("feed_path_mrss=$this->settings['feedMrssFolder']");
		putenv("feed_path_parts=$this->settings['partsFolder']");
		putenv("feed_mrss_prefix=$this->settings['partPrefix']");

		//shell_exec("bash " . __DIR__ . "treatxml.sh");		
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
