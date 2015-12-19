<?php

/**
 * Plugin Name:       Kodex Posts likes
 * Plugin URI:        http://kodex.pierros.fr/
 * Description:       A simple AJaX based WordPress Plugin which allows your visitors to like or dislike posts, pages and cutom post types. 
 * Version:           2.4.1
 * Author:            Pierre Lebedel
 * Author URI:        http://www.pierros.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kodex
 * Domain Path:       /languages
 */

if(!defined('WPINC')) die;

register_activation_hook( __FILE__, 'activate_kodex_posts_likes' );
function activate_kodex_posts_likes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kodex-posts-likes-activator.php';
	Kodex_Posts_Likes_Activator::activate();
}

register_deactivation_hook( __FILE__, 'deactivate_kodex_posts_likes' );
function deactivate_kodex_posts_likes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kodex-posts-likes-deactivator.php';
	Kodex_Posts_Likes_Deactivator::deactivate();
}

if(!function_exists('debug')){
	function debug($var, $info=''){
		echo '<div style="padding:5px 10px; margin-bottom:8px; font-size:13px; background:#FACFD3; color:#8E0E12; line-height:16px; border:1px solid #8E0E12; text-transform:none; overflow:auto; text-align:left;">';
			echo (!empty($info)) ? '<h3 style="color:#8E0E12; font-size:16px; padding:5px 0;">'.$info.'</h3>' : '';
			echo '<pre style="white-space:pre-wrap;">'.print_r($var,true).'</pre>
		</div>';
	}
}

require plugin_dir_path( __FILE__ ) . 'includes/class-kodex-posts-likes.php';
$plugin = new Kodex_Posts_Likes();
