<?php
	
/**
 * Behavior
 */
 	Croogo::hookBehavior('Node', 'NodeEvents.NodeEvent', array());
  
/**
 * Admin tab
 */
  Croogo::hookAdminTab('Nodes/admin_add', 'Event', 'NodeEvents.admin_tab_node', array('type' => array('events')));
  Croogo::hookAdminTab('Nodes/admin_edit', 'Event', 'NodeEvents.admin_tab_node', array('type' => array('events')));