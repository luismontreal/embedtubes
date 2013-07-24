<div class="nodes">
	<?php
		if (count($nodes) == 0) {
			echo __d('croogo', 'No items found.');
		}
	?>

	<?php
		foreach ($nodes as $node):			
			$this->Nodes->set($node);
			$thumb = $this->Nodes->node['Video']['thumb_path'];
	?>
	<div id="node-<?php echo $this->Nodes->field('id'); ?>" class="node node-type-<?php echo $this->Nodes->field('type'); ?>-index">
		
			<?php echo $this->Html->link(
				$this->Html->image($thumb, array('alt' => $this->Nodes->field('title'), 'class' => 'video_thumb'))
				. '<h2 class="crop">' . $this->Nodes->field('title') . '</h2>',
				$this->Nodes->field('url'), 
				array('escape' => false)); ?>
		<?php
			//echo $this->Nodes->info();
			//echo $this->Nodes->body();
			//echo $this->Nodes->moreInfo();
		?>
	</div>
	<?php
		endforeach;
	?>

	<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
</div>