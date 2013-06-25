<?php
App::import('Vendor', 'Tubes.MrssParser');
class MrssShell extends AppShell {
    public $uses = array('Tubes.Site');
    public $settings;
	
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
	parent::__construct($stdout, $stderr, $stdin);
    }
	
    public function main() {
        $this->out('No action shosen.');
    }
    
    public function run() {
        $this->out('No action shosen.');
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
    
    private function _getSettings () {
	$this->Site->unbindModel(
	    array('hasMany' => array('Video'))
	);
	return $this->Site->findByName($this->args[0]);
    }

}
