<?php

class Kodex_Posts_Likes_Public {

	private $plugin_title;
	private $plugin_name;
	private $version;
	
	public $options;

	public function __construct( $plugin_title, $plugin_name, $version ) {

		$this->plugin_title = $plugin_title;
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->set_options();
	}

	public function set_options(){
		$this->defaults = Kodex_Posts_Likes::get_defaults();
		$defaults = array();
		foreach($this->defaults as $k=>$v){
			$defaults[$k] = $v['value'];
		}
		$this->options = array_merge($defaults, (array) get_option($this->plugin_name, $defaults));
	}

	public function get_option($name){
		return (isset($this->options[$name])) ? $this->options[$name] : false;
	}

	public function get_user_identifier(){
		if( is_user_logged_in() ){
			$code = get_current_user_id();
		}else{
			$code = $_SERVER['REMOTE_ADDR'];
		}
		return md5($code);
	}

	public function enqueue_styles() {
		if( $this->get_option('include_css') ){
	    	wp_enqueue_style('dashicons');
	    	wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kodex-posts-likes-public.css', array(), $this->version, 'all' );
	    }
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kodex-posts-likes-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'kodex_posts_likes', array('ajaxurl'=>admin_url('admin-ajax.php')) );
	}

	public function shortcode_buttons($atts=array()){
		//$post_id = get_the_ID();
		//$post_type = get_post_type($post_id);
		$a = shortcode_atts(array(
			'postid'      => get_the_ID(),
			'liketext'    => false,
			'disliketext' => false
		), $atts);

		$post_id = $a['postid'];
		$post_type = get_post_type($post_id);

		$custom_liketext    = $a['liketext'];
		$custom_disliketext = $a['disliketext'];

		$html = '';
		//debug($this->get_option('post_types'));
		if(in_array($post_type, $this->get_option('post_types'))){
			$html .= '<div class="kodex_buttons" style="text-align:'.$this->get_option('alignement').';">';
			$html .= $this->buttons($post_id, $custom_liketext, $custom_disliketext);
			$html .= '</div>';
		}
		return $html;
	}

	public function shortcode_count($atts=array()){
		//$post_id = get_the_ID();
		//$count = (int) get_post_meta($post_id, 'kodex_post_likes_count', true);
		$a = shortcode_atts(array(
			'postid' => get_the_ID(),
			'format' => 'html'
		), $atts);

		$post_id = $a['postid'];
		$format  = $a['format'];
		$count   = (int) get_post_meta($post_id, 'kodex_post_likes_count', true);

		if($format=='number'){
			return $count;
		}else{
			$html = '';
			if($count>0){
				$html = '<div class="kodex_likes_count"><span>'.$count.'</span></div>';
			}
			return $html;
		}
	}

	private function buttons($post_id, $custom_liketext=false, $custom_disliketext=false){
		$html = '';
		$ident       = $this->get_user_identifier();

		$pm_likes    = get_post_meta($post_id, 'kodex_post_likes', true);
		$pm_likes_a  = (empty($pm_likes)) ? array() : $pm_likes;
		$like_active = (isset($pm_likes_a[$ident])) ? ' kodex_button_active' : '';
		$like_count  = count($pm_likes_a);
		$like_text   = ($custom_liketext) ? $custom_liketext : $this->get_option('like_text');
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
			$dislike_text   = ($custom_disliketext) ? $custom_disliketext : $this->get_option('dislike_text');
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
				$before = $this->shortcode_buttons();
			}
			if( $buttons_position=='bottom' || $buttons_position=='both' ){
				$after = $this->shortcode_buttons();
			}
		}
		$fullcontent = $before.$content.$after;
		return $fullcontent;
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
