<?php
	
/**
 * Behavior
 */
 Croogo::hookBehavior('Node', 'Tubes.Tubes', array());
 
 /**
 * Component
 *
 */
Croogo::hookComponent('Nodes', 'Tubes.Tubes');

  
/**
 * Admin tab
 */
  Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Tubes.admin_tab_node_video', array('type' => array('video')));
  Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Tubes.admin_tab_node_video', array('type' => array('video')));    
  
  /**
 * Admin menu (navigation)
 */
CroogoNav::add('settings.children.sites', array(
	'title' => 'External Sites',
	'url' => '#',
	'children' => array(
		'list' => array(
			'title' => 'List',
			'url' => array(
				'admin' => true,
				'plugin' => 'tubes',
				'controller' => 'sites',
				'action' => 'index',
			),
		),
		'add' => array(
			'title' => 'Add',
			'url' => array(
				'admin' => true,
				'plugin' => 'tubes',
				'controller' => 'sites',
				'action' => 'add',
			),
		),				
	),
));