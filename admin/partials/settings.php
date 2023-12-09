<?php

//$this->debug($this);

$tabs = array('form', 'doc');
$tab = (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) ? $_GET['tab'] : 'form';

?><div class="wrap">
	<h2><?php echo $this->plugin_title; ?></h2>

	<?php echo $this->message; ?>

	<h2 class="nav-tab-wrapper">     
        <a href="<?php echo add_query_arg(array('tab'=>'form')); ?>" class="nav-tab <?php echo($tab=='form')?'nav-tab-active':''; ?> dashicons-before dashicons-admin-generic"> <?php _e("Settings", 'kodex-posts-likes'); ?></a>     
        <a href="<?php echo add_query_arg(array('tab'=>'doc')); ?>" class="nav-tab <?php echo($tab=='doc')?'nav-tab-active':''; ?> dashicons-before dashicons-editor-code"> <?php _e("Documentation", 'kodex-posts-likes'); ?></a>
    </h2>

	<div id="poststuff">
		<div id="kodex_posts_likes_sidebar">
			
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Like this plugin?", 'kodex-posts-likes'); ?></span></h3>
				<div class="inside">
					<p><?php _e("Do not hesitate to click on Like, it will make me happy!", 'kodex-posts-likes'); ?></p>
					<div id="kodex_demo_buttons" class="kodex_buttons">
						<button type="button" class="kodex_like_button"><span class="icon"></span><span class="text">Like</span><span class="counter"></span></button>
						<button type="button" class="kodex_dislike_button"><span class="icon"></span><span class="counter"></span></button>
					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("This plugin is open source!", 'kodex-posts-likes'); ?></span></h3>
				<div class="inside">
					<p><?php _e("You can access source code, and contribute to its improvement by proposing your modifications.", 'kodex-posts-likes'); ?></p>
					<p><?php _e("You can also use the Issues section to report your problems.", 'kodev-posts-likes'); ?></p>
					<ul>
						<li><a href="https://github.com/PierreLebedel/KodexPostsLikes" target="_blank"><?php _e("View on Github", 'kodex-posts-likes'); ?></a></li>
						<li><a href="https://github.com/PierreLebedel/KodexPostsLikes/issues" target="_blank"><?php _e("Submit an issue", 'kodex-posts-likes'); ?></a></li>
					</ul>
					<p><?php _e("Fell free to help with the transmlation of the plugin into your language:", 'kodex-posts-likes'); ?></p>
					<ul>
						<li><a href="https://translate.wordpress.org/projects/wp-plugins/kodex-posts-likes/" target="_blank"><?php _e("Translate.WordPress.org", 'kodex-posts-likes'); ?></a></li>
					</ul>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Hey!", 'kodex-posts-likes'); ?></span></h3>
				<div class="inside">
					<p><?php _e("For more ressources and snippets, vitit us at", 'kodex-posts-likes'); ?> <a href="https://kodex.pierrelebedel.fr" target="_blank">Kodex.PierreLebedel.fr</a></p>
				</div>
			</div>
		</div>

		<?php require('settings-'.$tab.'.php'); ?>


	</div>



</div>