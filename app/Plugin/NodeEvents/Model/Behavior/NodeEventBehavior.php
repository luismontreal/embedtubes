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
class NodeEventBehavior extends ModelBehavior {

/**
 * Setup
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (is_string($config)) {
			$config = array($config);
		}
		$this->settings[$model->alias] = $config;
		
		$model->hasOne['NodeEvent'] = array(
	    'className' => 'NodeEvents.NodeEvent',
	    'foreignKey' => 'node_id',
	    'conditions' => array(),
	    'dependent' => true
	  );
	}

}
