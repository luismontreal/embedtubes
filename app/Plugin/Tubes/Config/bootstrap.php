<?php
	
/**
 * Behavior
 */
 	Croogo::hookBehavior('Node', 'Tubes.Tubes', array());
  
/**
 * Admin tab
 */
  Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Tubes.admin_tab_node_video', array('type' => array('video')));
  Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Tubes.admin_tab_node_video', array('type' => array('video')));