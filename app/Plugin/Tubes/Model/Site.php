<?php

App::uses('TubesAppModel', 'Tubes.Model');

class Site extends TubesAppModel {

/**
 * Validation
 *
 * @var array
 */
	var $validate = array(
		'last_updated_videoid' => array(
			'numeric' => array(
				'rule'     => 'numeric',
				'required' => false,
			),			
		),    
		'mrss_parts' => array(
			'numeric' => array(
				'rule'     => 'numeric',
				'required' => false,
			),		
		),
		'last_mrss_part_parsed' => array(
			'numeric' => array(
				'rule'     => 'numeric',
				'required' => false,
			),		
		),
		'next_deleted_to_parse' => array(
			'numeric' => array(
				'rule'     => 'numeric',
				'required' => false,
			),		
		),
	);
		
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