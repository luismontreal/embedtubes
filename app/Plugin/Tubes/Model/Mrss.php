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
        public function downloadSource($settings) {
	    debug($settings);
            return;
        }
	public function downloadDeleted(){
            return;
        }		
        
        //Storing in filesystem functions
	public function storeFS(){
            return;
        }
	public function storeDeletedFS(){
            return;
        }	
	
	//Storing in DB functions
	public function storeDB(){
            return;
        }
	public function updateDB(){
            return;
        }
	
	//criteria on which it is decided if a video qualifies to be inserted in importing site
	public function meetsCondition(&$param){
            return;
        }
	
	//Maps rss structure to a json file 
	public function mapItem($param){
            return;
        }	
	
	//Checks if the process can safely run
	public function canRun($param){
            return;
        }
	
	//Informes about what process are running
	public function setStatus($param){
            return;
        }
	public function removeStatus($param){
            return;
        }


}