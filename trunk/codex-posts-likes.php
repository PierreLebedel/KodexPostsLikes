<?php
/*
Plugin Name: Kodex Posts likes
Plugin URI: https://wordpress.org/plugins/kodex-posts-likes/
Description: A simple AJaX based WordPress Plugin which allows your visitors to like or dislike posts, pages and cutom post types. 
Version: 1.1.0
Author: Pierre Lebedel
Author URI: http://kodex.pierros.fr/
Text Domain: kodex
Domain Path: /languages/
*/



if( !class_exists('Kodex_Posts_Likes') ):
class Kodex_Posts_Likes{

	var $plugin_title,
		$plugin_slug,
		$plugin_url,
		$plugin_settings,
		$capability,
		$message,
		$options,
		$ws;

	public function __construct(){
		$this->plugin_title    = 'Kodex Posts likes';
		$this->plugin_slug     = 'kodex-posts-likes';
		$this->plugin_url      = plugins_url('/', __FILE__);
		$this->plugin_settings = add_query_arg(array('page'=>$this->plugin_slug), admin_url('options-general.php'));
		$this->capability      = 'manage_options';
		$this->ws              = 'http://kodex.pierros.fr/ws/';
	}

	public function init(){
		$this->message         = '';
		$this->options         = $this->get_options();

		if( is_admin() ){
			add_action('admin_init', array($this,'admin_init'));
			add_action('admin_menu', array($this,'admin_menu'));
			add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
		}

		add_shortcode('kodex_post_like_buttons', array($this, 'shortcode'));
		add_shortcode('kodex_post_like_count', array($this, 'shortcode_count'));

		add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action("add_meta_boxes", array($this, 'add_meta_boxes'));
		//add_action('load_textdomain', array($this, 'load_textdomain'));
		add_filter('the_content', array($this, 'before_after_content'));

		if($pt=$this->get_option('post_types')){
			if(!empty($pt)){
				foreach($pt as $p){
					add_filter('manage_'.$p.'_posts_columns', array($this, 'admin_columns'));
					add_action('manage_'.$p.'_posts_custom_column', array($this, 'admin_custom_columns'), 10, 2);
				}
			}
		}		

		add_action('wp_ajax_nopriv_kodex_posts_likes_ajax', array($this, 'ajax') );
		add_action('wp_ajax_kodex_posts_likes_ajax', array($this, 'ajax') );
	}

	public function get_options(){
		$this->default = array(
			'like_text' => array(
				'label' => __("Like button text", 'kodex'),
				'value' => 'Like',
				'type'  => 'text'
			),
			'dislike_text' => array(
				'label' => __("Dislike button text", 'kodex'),
				'value' => 'Dislike',
				'type'  => 'text'
			),
			'show_dislike' => array(
				'label' => __("Display the Dislike button", 'kodex'),
				'value' => true,
				'type'  => 'checkbox'
			),
			'hide_counter_0' => array(
				'label' => __("Hide the buttons counters if the number is zero", 'kodex'),
				'value' => true,
				'type'  => 'checkbox'
			),
			'hide_counter_total' => array(
				'label' => __("Hide the buttons counter", 'kodex'),
				'value' => false,
				'type'  => 'checkbox'
			),
			'post_types' => array(
				'label' => __("Enable for this post types", 'kodex'),
				'value' => array(),
				'maybe' => $this->post_types_maybe(),
				'type'  => 'array'
			),
			'include_in_post' => array(
				'label' => __("Show on the top of post", 'kodex'),
				'value' => 'nope',
				'type'  => 'select',
				'maybe' => array(
					'nope'   => __("Don't show automatically (I use shortcodes)", 'kodex'),
					'top'    => __("Show on the top of post", 'kodex'),
					'bottom' => __("Show on the bottom of post", 'kodex'),
					'both'   => __("Show on the top and on the bottom of post", 'kodex'),
				)
			),
			'alignement' => array(
				'label' => __("Alignment", 'kodex'),
				'value' => 'center',
				'type'  => 'select',
				'maybe' => array(
					'left'   => __("Left", 'kodex'),
					'center' => __("Center", 'kodex'),
					'right'  => __("Right", 'kodex'),
				)
			),
			'include_css' => array(
				'label' => __("Include default stylesheet", 'kodex'),
				'value' => true,
				'type'  => 'checkbox'
			),
		);
		$default = array();
		foreach($this->default as $k=>$v){
			$default[$k] = $v['value'];
		}
		$options = array_merge($default, get_option($this->plugin_slug, $default));
		return $options;
	}

	private function post_types_maybe(){
		global $wp_post_types;
		$types = array();
		foreach($wp_post_types as $k=>$v){
			if( !in_array($k, array('revision','nav_menu_item','acf')) ){
				$types[$k] = $k;
			}
		}
		//echo '<pre>'.print_r($types,true).'</pre>';
		return $types;
	}

	private function get_option($name){
		return $this->options[$name];
	}

	private function set_message($msg){
		$this->message = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.$msg.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
	}

	public function load_textdomain(){
		load_plugin_textdomain('kodex', false, dirname(plugin_basename(__FILE__)).'/languages'); 
	}

	public function admin_init(){
		if( isset($_POST[$this->plugin_slug]) ){
			update_option($this->plugin_slug, $_POST[$this->plugin_slug]);
			$this->options = $this->get_options();
			$this->set_message(__('The options are saved', 'kodex'));
		}
	}

	public function action_links($links, $file){
		if( basename($file)==basename(plugin_basename(__FILE__)) ){
			$settings_link = '<a href="'.$this->plugin_settings.'">'.__("Settings", 'kodex').'</a>';
			$links = array_merge($links, array($settings_link));
		}
		return $links;
    }

	public function admin_menu(){
		add_options_page( $this->plugin_title, $this->plugin_title, $this->capability, $this->plugin_slug, array($this,'admin_page') );
	}

	public function admin_page(){
		$this->admin_page_header(); 
		echo $this->message;

		//debug($this->options);

		?><div id="poststuff">
			<div id="kodex_posts_likes_doc">
				<div class="postbox">
					<h3 class="hndle"><span><?php _e("Documentation", 'kodex'); ?></span></h3>
					<div class="inside">
						<h4><?php _e("Display the counter", 'kodex'); ?></h4>
						<p><code>[kodex_post_like_count]</code></p>

						<h4><?php _e("Display the buttons", 'kodex'); ?></h4>
						<p><code>[kodex_post_like_buttons]</code></p>
					</div>
				</div>
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
						<p><?php _e("For more ressources and snippets, vitit us at", 'kodex'); ?> <a href="http://kodex.pierros.fr" target="_blank">Codex.Pierros.fr</a></p>
					</div>
				</div>
			</div>
			<form action="" method="post" id="kodex_posts_likes_form">
				<table class="widefat">
				<thead>
				    <tr>
				        <th colspan="2"><b>Options</b></th>
				    </tr>
				</thead>
				<tbody>
					<?php $i=0;
					//echo '<pre>'.print_r($this->options,true).'</pre>';
					foreach($this->options as $k=>$object):
						$i++;
						if(!isset($this->default[$k])) continue;
						$object = (object) $this->default[$k];
						$value = $this->get_option($k);
						$class = ($i%2) ? ' alternate' : ''; ?>
						<tr class="<?php echo $class; ?>">
							<td><?php echo $object->label; ?></td>
							<td>
								<?php if($object->type=='checkbox'): ?>
								<input type="hidden" name="<?php echo $this->plugin_slug; ?>[<?php echo $k; ?>]" value="0">
								<input type="checkbox" name="<?php echo $this->plugin_slug; ?>[<?php echo $k; ?>]" value="1" <?php echo($value)?'checked':''; ?>>
								
								<?php elseif($object->type=='text'): ?>
								<input type="text" name="<?php echo $this->plugin_slug; ?>[<?php echo $k; ?>]" value="<?php echo esc_attr($value); ?>">
								
								<?php elseif($object->type=='array'):
									foreach($object->maybe as $p): ?>
										<label>
											<input type="checkbox" name="<?php echo $this->plugin_slug; ?>[<?php echo $k; ?>][]" value="<?php echo esc_attr($p); ?>" <?php echo(in_array($p, $value))?'checked':''; ?>>
											<?php echo $p; ?>
										</label><br>
									<?php endforeach; ?>

								<?php elseif($object->type=='select'): ?>
									<select name="<?php echo $this->plugin_slug; ?>[<?php echo $k; ?>]">
									<?php foreach($object->maybe as $k=>$v): ?>
										<option value="<?php echo esc_attr($k); ?>" <?php echo($k==$value)?'selected':''; ?>><?php echo $v; ?></option>
										</label><br>
									<?php endforeach; ?>
									</select>
							
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				</table>

				<br><button type="submit" class="button button-primary"><?php _e("Save", 'kodex'); ?></button>
			</form>
		</div><?php 

		$this->admin_page_footer(); 
	}

	private function admin_page_header(){
		global $menu, $submenu;  
		$html = '<div class="wrap">';
		$html .= '<h2>'.$this->plugin_title.'</h2><br>';
		echo $html;
	}

	private function admin_page_footer(){
		$html = '</div><!-- /.wrap -->';
		echo $html;
	}

	public function add_meta_boxes(){
		if($pt=$this->get_option('post_types')){
			if(!empty($pt)){
				foreach($pt as $p){
					add_meta_box("kodex-posts-likes", "Kodex Likes", array($this,"meta_box_markup"), $p, "side", "default", null);
				}
			}
		}
	}

	public function meta_box_markup($object){
		$post_id  = $object->ID;
		
		$likes    = get_post_meta($post_id, 'kodex_post_likes_count', true);
		$likes_i  = ($likes) ? $likes : 0;
		
		$dislikes    = get_post_meta($post_id, 'kodex_post_dislikes_count', true);
		$dislikes_i  = ($dislikes) ? $dislikes : 0;

		$html = '<div style="line-height:22px">
			<table width="100%">
				<tr>
					<td width="50%" align="center">
						<span class="dashicons dashicons-thumbs-up"></span>
						<span>'.$this->get_option('like_text').'</span>
						<b>&nbsp;'.$likes_i.'</b>
					</td>
					<td width="50%" align="center">
						<span class="dashicons dashicons-thumbs-down"></span>
						<span>'.$this->get_option('dislike_text').'</span>
						<b>&nbsp;'.$dislikes_i.'</b>
					</td>
				</tr>
			</table>
		</div>';
		echo $html;
	}

	public function admin_columns($columns){
		$columns['kodex_posts_likes'] = '<span class="dashicons dashicons-thumbs-up"></span>';
		$columns['kodex_posts_dislikes'] = '<span class="dashicons dashicons-thumbs-down"></span>';
    	return $columns;
	}

	public function admin_custom_columns($name, $post_id){
		switch($name){
	        case 'kodex_posts_likes':
	        	$likes    = get_post_meta($post_id, 'kodex_post_likes_count', true);
				$likes_i  = ($likes) ? $likes : 0;
	            echo '<b>'.$likes_i.'</b>';
	            break;
	        case 'kodex_posts_dislikes':
	        	$dislikes    = get_post_meta($post_id, 'kodex_post_dislikes_count', true);
				$dislikes_i  = ($dislikes) ? $dislikes : 0;
	            echo '<b>'.$dislikes_i.'</b>';
	            break;
	    }
	}

	public function front_scripts(){
		wp_enqueue_script('kodex_posts_likes', $this->plugin_url.'front/kodex-posts-likes.js', array('jquery'));
	    wp_localize_script('kodex_posts_likes', 'kodex_posts_likes', array('ajaxurl'=>admin_url('admin-ajax.php')) );

	    if( $this->get_option('include_css') ){
	    	wp_enqueue_style('dashicons');
	    	wp_enqueue_style('kodex_posts_likes', $this->plugin_url.'front/kodex-posts-likes.css');
	    }
	}

	public function admin_scripts(){
		wp_enqueue_script('kodex_posts_likes', $this->plugin_url.'admin/kodex-posts-likes.js', array('jquery'));
	    wp_localize_script('kodex_posts_likes', 'kodex_posts_likes', array('settings'=>$this->plugin_settings, 'ws'=>$this->ws));
	    wp_enqueue_style('kodex_posts_likes', $this->plugin_url.'admin/kodex-posts-likes.css');
	    wp_enqueue_style('kodex_posts_likes_front', $this->plugin_url.'front/kodex-posts-likes.css');
	}

	public function get_user_identifier(){
		if( is_user_logged_in() ){
			$code = get_current_user_id();
		}else{
			$code = $_SERVER['REMOTE_ADDR'];
		}
		return md5($code);
	}

	private function buttons($post_id){
		$html = '';
		$ident       = $this->get_user_identifier();

		$pm_likes    = get_post_meta($post_id, 'kodex_post_likes', true);
		$pm_likes_a  = (empty($pm_likes)) ? array() : $pm_likes;
		$like_active = (isset($pm_likes_a[$ident])) ? ' kodex_button_active' : '';
		$like_count  = count($pm_likes_a);
		$like_text   = $this->get_option('like_text');
		$html .= '<button type="button" class="kodex_button kodex_like_button'.$like_active.'" data-id="'.$post_id.'" data-action="like">
			<span class="icon"></span>';
			$html .= (!empty($like_text)) ? '<span class="text">'.$like_text.'</span>' : '';
			if( ($this->get_option('hide_counter_0') && $like_count==0) || $this->get_option('hide_counter_total') ){
				// on n'affiche pas le compteur
			}else{
				$html .= '<span class="counter">'.$like_count.'</span>';
			}
		$html .= '</button>';

		if( $this->get_option('show_dislike') ){
			$pm_dislikes    = get_post_meta($post_id, 'kodex_post_dislikes', true);
			$pm_dislikes_a  = (empty($pm_dislikes)) ? array() : $pm_dislikes;
			$dislike_active = (isset($pm_dislikes_a[$ident])) ? ' kodex_button_active' : '';
			$dislike_count  = count($pm_dislikes_a);
			$dislike_text   = $this->get_option('dislike_text');
			$html .= '<button type="button" class="kodex_button kodex_dislike_button'.$dislike_active.'" data-id="'.$post_id.'" data-action="dislike">
				<span class="icon"></span>';
				$html .= (!empty($dislike_text)) ? '<span class="text">'.$dislike_text.'</span>' : '';
				if( ($this->get_option('hide_counter_0') && $dislike_count==0) || $this->get_option('hide_counter_total') ){
					// on n'affiche pas le compteur
				}else{
					$html .= '<span class="counter">'.$dislike_count.'</span>';
				}
			$html .= '</button>';
		}

		$html .= wp_nonce_field('kodex_posts_likes', 'nonce', true, false);
		
		return $html;
	}

	public function before_after_content($content){
		$post_id = get_the_ID();
		$post_type = get_post_type($post_id);

		$buttons_position = $this->get_option('include_in_post');
		$before = $after = '';

		if(in_array($post_type, $this->get_option('post_types'))){
			if( $buttons_position=='top' || $buttons_position=='both' ){
				$before = $this->shortcode();
			}
			if( $buttons_position=='bottom' || $buttons_position=='both' ){
				$after = $this->shortcode();
			}
		}
		$fullcontent = $before.$content.$after;
		return $fullcontent;
	}

	public function shortcode(){
		$post_id = get_the_ID();
		$post_type = get_post_type($post_id);

		$html = '';
		if(in_array($post_type, $this->get_option('post_types'))){
			$html .= '<div class="kodex_buttons" style="text-align:'.$this->get_option('alignement').';">';
			$html .= $this->buttons($post_id);
			$html .= '</div>';
		}
		return $html;
	}

	public function shortcode_count(){
		$post_id = get_the_ID();
		$count = (int) get_post_meta($post_id, 'kodex_post_likes_count', true);
		$html = '';
		if($count>0){
			$html = '<div class="kodex_likes_count"><span>'.$count.'</span></div>';
		}
		return $html;
	}

	public function ajax(){
		$post_id = $_REQUEST['post_id'];
		$action  = $_REQUEST['btn_action'];
		$nonce   = $_REQUEST['nonce'];
		
		$this->vote($post_id, $action, $nonce);

		// on recherche les boutons pour les injecter dans le html après la réponse ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			$html = $this->buttons($post_id);
			die($html);
		}
	}

	public function vote($post_id, $action, $nonce){
		$pm_likes    = get_post_meta($post_id, 'kodex_post_likes', true);
		if(!$pm_likes) $pm_likes = array();
		$pm_dislikes = get_post_meta($post_id, 'kodex_post_dislikes', true);
		if(!$pm_dislikes) $pm_dislikes = array();
		$ident       = $this->get_user_identifier();

		// likes
		if($action=='like'){
			if(isset($pm_likes[$ident])){
				unset($pm_likes[$ident]);
			}else{
				if(isset($pm_dislikes[$ident])){
					unset($pm_dislikes[$ident]);
				}
				$pm_likes[$ident] = time();
			}

		// dislikes
		}else{
			if(isset($pm_dislikes[$ident])){
				unset($pm_dislikes[$ident]);
			}else{
				if(isset($pm_likes[$ident])){
					unset($pm_likes[$ident]);
				}
				$pm_dislikes[$ident] = time();
			}
		}

		$test = wp_verify_nonce($nonce, 'kodex_posts_likes');
		if($test){
			update_post_meta($post_id, 'kodex_post_likes', $pm_likes);
			update_post_meta($post_id, 'kodex_post_dislikes', $pm_dislikes);

			update_post_meta($post_id, 'kodex_post_likes_count', count($pm_likes));
			update_post_meta($post_id, 'kodex_post_dislikes_count', count($pm_dislikes));
		}
	}

}
endif;


$Kodex_Posts_Likes = new Kodex_Posts_Likes();
add_action('registered_post_type', array($Kodex_Posts_Likes, 'init'));
add_action('plugins_loaded', array($Kodex_Posts_Likes, 'load_textdomain'));
