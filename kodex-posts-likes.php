<?php

/**
 * Plugin Name:       Kodex Posts likes
 * Plugin URI:        http://kodex.pierros.fr/
 * Description:       A simple AJaX based WordPress Plugin which allows your visitors to like or dislike posts, pages and cutom post types. 
 * Version:           2.0.0
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

require plugin_dir_path( __FILE__ ) . 'includes/class-kodex-posts-likes.php';
$plugin = new Kodex_Posts_Likes();
