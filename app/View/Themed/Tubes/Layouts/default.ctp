<?php
/**
 * Default Theme for Croogo CMS
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://www.croogo.org
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title_for_layout; ?> &raquo; <?php echo Configure::read('Site.title'); ?></title>
	<?php
		echo $this->Meta->meta();
		echo $this->Layout->feed();
		echo $this->Html->css(array(
			'reset',
			'960',
			'theme',
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'jquery/jquery.min',
			'jquery/jquery.hoverIntent.minified',
			'jquery/superfish',
			'jquery/supersubs',
			'theme',
		));
		echo $this->Blocks->get('css');
		echo $this->Blocks->get('script');
	?>
</head>
<body>
	<div id="wrapper">
		<div id="header" class="container_16">			
			<div class="grid_16">
				<h1 class="site-title"><?php echo $this->Html->link(Configure::read('Site.title'), '/'); ?></h1>
				<span class="site-tagline"><?php echo Configure::read('Site.tagline'); ?></span>
				<?php echo $this->Regions->blocks('header'); ?>
			</div>			
		</div>

		<div id="nav">
			<div class="container_16">
				<?php echo $this->Menus->menu('main', array('dropdown' => true)); ?>
			</div>
		</div>

		<div id="main" class="container_16">
			<div id="content" class="grid_16">
			<?php
				echo $this->Layout->sessionFlash();
				echo $content_for_layout;
			?>
			</div>

			<!--div id="sidebar" class="grid_5">
			<?php //echo $this->Regions->blocks('right'); ?>
			</div-->

			<div class="clear"></div>
		</div>

		<div id="footer">
			&nbsp;
		</div>
	</div>
	<?php
		echo $this->Blocks->get('scriptBottom');
		echo $this->Js->writeBuffer();
	?>
	</body>
</html>