<?php
/*Unused stuff*/
if (!$this->request->is('ajax') && isset($this->request->params['admin'])):
	$this->Html->script('Comments.admin', array('inline' => false));
endif;

//Extending Plugin/Croogo/View/Common
$this->extend('/Common/admin_index');

//BreadCrumbs
$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->addCrumb(__d('croogo', 'External Sites'), $this->here)
	->addCrumb(__d('croogo', 'List'), $this->here);

$this->viewVars['title_for_layout'] = __d('croogo', 'External Sites: List');


/*echo $this->element('admin/modal', array(
	'id' => 'site-modal',
	'class' => 'hide',
));*/

?>

<?php echo $this->Form->create('Site', array('url' => array('controller' => 'sites', 'action' => 'process'))); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),		
		$this->Paginator->sort('name'),
		$this->Paginator->sort('feed_url'),
		$this->Paginator->sort('feed_filename'),		
		$this->Paginator->sort('created'),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>
<?php

	$rows = array();
	foreach ($sites as $site) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($site['Site']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $site['Site']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Comment' . $site['Site']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
			__d('croogo', 'Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$title = empty($site['Site']['name']) ? 'Site' : $site['Site']['name'];
		$rows[] = array(
			$this->Form->checkbox('Site.' . $site['Site']['id'] . '.id'),
			$site['Site']['id'],
			$site['Site']['name'],
			$site['Site']['feed_url'],
			$site['Site']['feed_filename'],			
			$site['Site']['created'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
?>

</table>
<div class="row-fluid">
	<div id="bulk-action" class="control-group">
		<?php
			echo $this->Form->input('Site.action', array(
				'label' => false,
				'div' => 'input inline',
				'options' => array(					
					'delete' => __d('croogo', 'Delete'),
				),
				'empty' => true,
			));
		?>
		<div class="controls">
		<?php echo $this->Form->end(__d('croogo', 'Submit')); ?>
		</div>
	</div>
</div>
