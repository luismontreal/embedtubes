<?php
class MrssShell extends AppShell {
	public $uses = array('Tubes.Mrss');
	
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		/*GET SETTINGS HERE*/
	}
	
    public function main() {
        $this->out('No action shosen.');
    }
	
	/*
	 * Downloads Video Feed and Deleted
	 */
	public function download() {
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
	
	private function _getSettings () {
		return null;
	}

}
