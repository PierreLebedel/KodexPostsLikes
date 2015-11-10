<?php

//$this->debug($this);

$tabs = array('form', 'doc');
$tab = (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) ? $_GET['tab'] : 'form';

?><div class="wrap">
	<h2><?php echo $this->plugin_title; ?></h2>

	<?php echo $this->message; ?>

	<h2 class="nav-tab-wrapper">     
        <a href="<?php echo add_query_arg(array('tab'=>'form')); ?>" class="nav-tab <?php echo($tab=='form')?'nav-tab-active':''; ?> dashicons-before dashicons-admin-generic"> <?php _e("Settings", 'kodex'); ?></a>     
        <a href="<?php echo add_query_arg(array('tab'=>'doc')); ?>" class="nav-tab <?php echo($tab=='doc')?'nav-tab-active':''; ?> dashicons-before dashicons-editor-code"> <?php _e("Documentation", 'kodex'); ?></a>
    </h2>

	<div id="poststuff">
		<div id="kodex_posts_likes_sidebar">
			
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Like this plugin?", 'kodex'); ?></span></h3>
				<div class="inside">
					<p><?php _e("Do not hesitate to click on Like, it will make me happy!", 'kodex'); ?></p>
					<div id="kodex_demo_buttons" class="kodex_buttons">
						<button type="button" class="kodex_like_button"><span class="icon"></span><span class="text">Like</span><span class="counter"></span></button>
						<button type="button" class="kodex_dislike_button"><span class="icon"></span><span class="counter"></span></button>
					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Hey!", 'kodex'); ?></span></h3>
				<div class="inside">
					<p><?php _e("For more ressources and snippets, vitit us at", 'kodex'); ?> <a href="http://kodex.pierros.fr" target="_blank">Kodex.Pierros.fr</a></p>
				</div>
			</div>
		</div>

		<?php require('settings-'.$tab.'.php'); ?>


	</div>



</div>