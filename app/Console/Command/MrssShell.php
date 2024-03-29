<?php
// Call me like "cake mrss run Pornhub storeFS"
App::import('Vendor', 'Tubes.MrssParser');
class MrssShell extends AppShell {
    public $uses = array('Tubes.Site', 'Nodes.Node', 'Taxonomy.Taxonomy','Taxonomy.Term', 'Taxonomy.Vocabulary');
    public $site;
	
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
    }
	    
	/*
	 * Mrss Dispatcher
	 */
    public function run() {        							
		switch ($this->args[0]) {
			case 'Pornhub':				
				require_once("importers/PornhubMrss.php");				
				$this->site = new PornhubMrss($this->__getSettings(), $this);												
				break;

			default:
				throw new Exception('Unknown source');	
		}
		
		/*$canRun = call_user_func_array(array($this->site, 'canRun'), array('functionName' => $this->args[1]));*/
		$canRun = true;	
		if($canRun) {
			//Callback setStatus
			//call_user_func_array(array($this->site, 'setStatus'), array($this->args[1]));
			//Calls the process requested by the Cron Module
			call_user_func_array(array($this->site, $this->args[1]), array());
			//Callback removeStatus
			//call_user_func_array(array($this->site, 'removeStatus'), array($this->args[1]));
		} else {
			return FALSE;
		}								
    }
    
    public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addArgument('site', array(
			'help' => 'site name',
			'required' => true
		))->addArgument('action', array(
			'help' => 'Action to Execute in Site',
			'required' => true
		))->description(__('Mrss Video Importer'));
		return $parser;
    }
    
    private function __getSettings () {
		$this->Site->unbindModel(
			array('hasMany' => array('Video'))
		);
				
		$feedFolder = ROOT . DS . "feeds". DS .$this->args[0].DS;
		$feedMrssFolder = $feedFolder."mrss" . DS;
		$partsFolder = $feedMrssFolder . "parts" . DS;		
		$storageFolder = $feedFolder . "items" . DS;		
		$deletedFolder = $feedFolder.'deleted' . DS;
		$deletedPartsFolder = $deletedFolder . "parts" . DS;
		$deletedFolderItems = $deletedFolder.'items' . DS;				
		$partPrefix = "mrss_part_";	
		
		$calculatedSettings = array(
			'feedFolder' => $feedFolder,
			'feedMrssFolder' => $feedMrssFolder,
			'partsFolder' => $partsFolder,
			'storageFolder' => $storageFolder,
			'deletedFolder' => $deletedFolder,
			'deletedPartsFolder' => $deletedPartsFolder,
			'deletedFolderItems' => $deletedFolderItems,
			'partPrefix' => $partPrefix
		);
						
		$settings = array_merge($this->Site->findByName($this->args[0]), $calculatedSettings);
		
		return $settings;
    }

}
