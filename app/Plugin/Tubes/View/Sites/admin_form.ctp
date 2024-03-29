<?php

$isEditing = $this->action == 'admin_edit';
if($isEditing) {
	$crumbLabel = 'Edit';
} else {
	$crumbLabel = 'Add';
}

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->addCrumb(__d('croogo', 'External Sites'), array('plugin' => 'tubes', 'controller' => 'sites', 'action' => 'index'))
	->addCrumb($crumbLabel, $this->here);

echo $this->Form->create('Site');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Site'), '#site-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="site-main" class="tab-pane">
			<?php				
				echo $this->Form->input('id');													
				$this->Form->inputDefaults(array(
						'class' => 'span10',
					));				
				echo $this->Form->input('name', array(
					'label' => __d('croogo', 'Name'),
				));
				echo $this->Form->input('embed_url', array(
					'label' => __d('croogo', 'Embed Url'),
				));
				echo $this->Form->input('feed_url', array(
					'label' => __d('croogo', 'Feed Url'),
				));
				echo $this->Form->input('feed_filename', array(
					'label' => __d('croogo', 'Feed Filename'),
				));
				echo $this->Form->input('deleted_feed_url', array(
					'label' => __d('croogo', 'Deleted Feed Url'),
				));
				echo $this->Form->input('deleted_feed_filename', array(
					'label' => __d('croogo', 'Deleted Feed Filename'),
				));				
				echo $this->Form->input('max_video_insert', array(
					'label' => __d('croogo', 'Max Videos to Insert'),
				));
				echo $this->Form->input('max_video_update', array(
					'label' => __d('croogo', 'Max Videos to Update'),
				));				
				echo $this->Form->input('days_from', array(
					'label' => __d('croogo', 'Days to Parse From'),
				));
				echo $this->Form->input('days_to', array(
					'label' => __d('croogo', 'Days to Parse To'),
				));				
				
				if($isEditing) {
					echo $this->Form->input('last_updated_videoid', array(
						'label' => __d('croogo', 'Last Updated Video Id'),
						'readonly' => 'readonly',					
					));				
					echo $this->Form->input('mrss_parts', array(
						'label' => __d('croogo', 'Mrss Parts'),
						'readonly' => 'readonly',

					));				
					echo $this->Form->input('last_mrss_part_parsed', array(
						'label' => __d('croogo', 'Last Mrss Part Parsed'),
						'readonly' => 'readonly',

					));							
					echo $this->Form->input('next_deleted_to_parse', array(
						'label' => __d('croogo', 'Next Deleted Part To Parse'),
						'readonly' => 'readonly',

					));
				}
				
				echo $this->Form->input('created', array(
					'label' => __d('croogo', 'Created'),
				));					
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
			echo $this->Html->beginBox(__d('croogo', 'Actions')) .
				$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
				$this->Html->link(
					__d('croogo', 'Cancel'),
					array('action' => 'index'),
					array('button' => 'danger')
				) .				
				$this->Html->endBox();		

			echo $this->Croogo->adminBoxes();
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
