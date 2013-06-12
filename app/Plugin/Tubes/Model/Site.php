<?php

App::uses('TubesAppModel', 'Tubes.Model');

class Site extends TubesAppModel {

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
	public $hasMany = array(
		'Video' => array(
			'className' => 'Tubes.Video',
			'foreignKey' => 'site_id',
			'conditions' => array(),
			'fields' => '',
			'order' => '',
		),
	);

}