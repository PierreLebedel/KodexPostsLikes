<?php

class Kodex_Posts_Likes_Admin {

	private $plugin_title;
	private $plugin_name;
	private $version;
	private $message;
	private $settings_url;
	private $ws;
	
	private $defaults;
	public $options;

	public function __construct( $plugin_title, $plugin_name, $version) {
		$this->plugin_title = $plugin_title;
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->message      = '';
		$this->settings_url = add_query_arg(array('page'=>$this->plugin_name), admin_url('options-general.php'));
		$this->ws           = 'http://kodex.pierros.fr/ws/';

		//$this->set_admin_columns();
	}

	public function debug($var, $info=''){
		echo '<div style="padding:5px 10px; margin-bottom:8px; font-size:13px; background:#FACFD3; color:#8E0E12; line-height:16px; border:1px solid #8E0E12; text-transform:none; overflow:auto;">';
			echo (!empty($info)) ? '<h3 style="color:#8E0E12; font-size:16px; padding:5px 0;">'.$info.'</h3>' : '';
			echo '<pre style="white-space:pre-wrap;">'.print_r($var,true).'</pre>
		</div>';
	}

	public function set_options(){
		$this->defaults = Kodex_Posts_Likes::get_defaults();
		$defaults = array();
		foreach($this->defaults as $k=>$v){
			$defaults[$k] = $v['value'];
		}
		$this->options = array_merge($defaults, get_option($this->plugin_name, $defaults));
		return $this->options;
	}

	public function get_option($name){
		return (isset($this->options[$name])) ? $this->options[$name] : false;
	}

	private function set_admin_columns(){
		if($post_types = $this->get_option('post_types')){
			//$this->debug($post_types);
			if(!empty($post_types)){
				foreach($post_types as $p){
					add_filter('manage_'.$p.'_posts_columns', array($this, 'admin_columns'));
					add_action('manage_'.$p.'_posts_custom_column', array($this, 'admin_custom_columns'), 10, 2);

					add_filter('manage_edit-'.$p.'_sortable_columns', array($this, 'admin_columns_sortable'), 10, 2);
					
				}
				add_filter('request', array($this, 'admin_columns_sorting'));
			}
		}	
	}

	public function admin_columns($columns){
		if(isset($columns['date'])){
			unset($columns['date']);
			$date = true;
		}
		$columns['kodex_posts_likes'] = '<span class="dashicons dashicons-thumbs-up"></span>';
		if( $this->options['show_dislike'] ){
			$columns['kodex_posts_dislikes'] = '<span class="dashicons dashicons-thumbs-down"></span>';
		}
		if($date){
			$columns['date'] = __('Date');
		}
    	return $columns;
	}

	public function admin_columns_sortable($columns){
		$columns['kodex_posts_likes'] = 'kodex_posts_likes';
		if( $this->options['show_dislike'] ){
			$columns['kodex_posts_dislikes'] = 'kodex_posts_dislikes';
		}
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
				$dislikes   = get_post_meta($post_id, 'kodex_post_dislikes_count', true);
				$dislikes_i = ($dislikes) ? $dislikes : 0;
	            echo '<b>'.$dislikes_i.'</b>';
	            break;
	    }
	}

	public function admin_columns_sorting($vars){
		if( isset($vars['orderby']) && $vars['orderby']=='kodex_posts_likes' ){
	        $vars = array_merge($vars, array(
				'meta_key' => 'kodex_post_likes_count',
				'orderby'  => 'meta_value_num'
			));
	    }
    	return $vars;
	}

	public function set_message($msg){
		$this->message = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.$msg.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
	}

	public function admin_init(){
		if( isset($_POST[$this->plugin_name]) ){
			update_option($this->plugin_name, $_POST[$this->plugin_name]);
			$this->set_message(__('The options are saved', 'kodex'));
		}
		$this->set_options();
		$this->set_admin_columns();

	}

	public function plugin_action_links($links, $file){
		if( basename(str_replace('.php','',$file))==$this->plugin_name ){
			$settings_link = '<a href="'.$this->settings_url.'">'.__("Settings", 'kodex').'</a>';
			$links = array_merge($links, array($settings_link));
		}
		return $links;
	}

	public function admin_menu(){
		add_options_page($this->plugin_title, $this->plugin_title, 'manage_options', $this->plugin_name, array($this, 'admin_page') );
	}

	public function admin_enqueue_styles() {
		wp_enqueue_style('dashicons');
	    wp_enqueue_style( $this->plugin_name.'_front', plugin_dir_url(dirname(__FILE__)).'public/css/kodex-posts-likes-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kodex-posts-likes-admin.css', array(), $this->version, 'all' );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kodex-posts-likes-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'kodex_posts_likes', array('settings'=>$this->settings_url, 'ws'=>$this->ws));
	}

	public function admin_page(){
		require(plugin_dir_path( __FILE__ ).'partials/settings.php');
	}

	public function add_meta_boxes(){
		if($pt=$this->get_option('post_types')){
			if(!empty($pt)){
				foreach($pt as $p){
					add_meta_box($this->plugin_name, $this->plugin_title, array($this,"meta_box_markup"), $p, "side", "default", null);
				}
			}
		}
		
	}

	public function meta_box_markup($object){
		$post_id  = $object->ID;
		require(plugin_dir_path( __FILE__ ).'partials/metabox.php');
	}

	public function save_post($post_id){
		// on définit le compteur à zéro si le post_meta n'existe pas pour pouvoir faire des requêtes avec un orderby même s'il n'y a pas de meta
		//$count = get_post_meta($post_id, 'kodex_post_likes_count', true);
		$likes = get_post_meta($post_id, 'kodex_post_likes', true);
		$likes_count = ($likes) ? count($likes) : 0;
		update_post_meta($post_id, 'kodex_post_likes_count', $likes_count);
		
		//$count = get_post_meta($post_id, 'kodex_post_dislikes_count', true);
		$dislikes = get_post_meta($post_id, 'kodex_post_dislikes', true);
		$dislikes_count = ($dislikes) ? count($dislikes) : 0;
		update_post_meta($post_id, 'kodex_post_dislikes_count', $dislikes_count);
	}

}
