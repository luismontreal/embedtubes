<?php

App::uses('TubesAppModel', 'Tubes.Model');

class Video extends TubesAppModel {

/**
 * Validation
 *
 * @var array
 */
  /*var $validate = array(
    'event_date' => array(
        'notEmpty' => array(
        'rule' => 'notEmpty',
        'message' => 'This field cannot be left blank.',
        'last' => true,
      ),
    ),
    'event_time' => array(
        'notEmpty' => array(
        'rule' => 'notEmpty',
        'message' => 'This field cannot be left blank.',
        'last' => true,
      ),
    ),
	);*/
		
/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Node' => array(
			'className' => 'Node',
			'foreignKey' => 'node_id',
			'conditions' => array('Node.type' => 'video'),
			'fields' => '',
			'order' => '',
		),
		'Site' => array(
			'className' => 'Tubes.Site',
			'foreignKey' => 'site_id',
			'conditions' => array(),
			'fields' => '',
			'order' => '',
		),
	);

}