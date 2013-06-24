<?php
App::import('Vendor', 'Tubes.MrssParser');
App::uses('TubesAppModel', 'Tubes.Model');

class Mrss extends TubesAppModel {
	public $useTable = false;
	
	//Possible process status
	const DOWNLOADING_MRSS = 'downloadingMrss';
	const DOWNLOADING_DELETED = 'downloadingDeleted';
	const STORING_FS = 'storingFS';
	const STORING_DELETED_FS = 'storingDeletedFS';
	const INSERTING_DB = 'insertingDB';
	const UPDATING_DB = 'updatingDB';
	
	//Downloading functions
	/*public function downloadSource();
	public function downloadDeleted();		
	
	//Storing in filesystem functions
	public function storeFS();
	public function storeDeletedFS();	
	
	//Storing in DB functions
	public function storeDB();
	public function updateDB();
	
	//criteria on which it is decided if a video qualifies to be inserted in importing site
	public function meetsCondition(&$param);
	
	//Maps rss structure to a json file 
	public function mapItem($param);	
	
	//Checks if the process can safely run
	public function canRun($param);
	
	//Informes about what process are running
	public function setStatus($param);
	public function removeStatus($param);
*/

}