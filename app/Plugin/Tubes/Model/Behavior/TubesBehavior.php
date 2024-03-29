<?php
App::uses('ModelBehavior', 'Model');

/**
 * NodeEvents: NodeEvent Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo
 * @author   Paul Gardner <paul@webbedit.co.uk>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TubesBehavior extends ModelBehavior {

/**
 * Setup
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {		
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = (array)$config;
        }
						
        $model->hasOne['Video'] = array(
			'className' => 'Tubes.Video',
			'foreignKey' => 'node_id',
			'conditions' => array(),
			'dependent' => true
        );           
	}
	
	/*
	 * 
	 */
	/*public function beforeFind(\Model $model, $query) {
		parent::beforeFind($model, $query);
		
		if($model->type == 'video') {
			$query['contain'][] = 'Video';			
			return $query;
		}				
	}*/
}
