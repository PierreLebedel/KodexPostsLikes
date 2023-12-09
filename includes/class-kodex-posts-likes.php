<?php

class Kodex_Posts_Likes {

	protected $loader;
	protected $plugin_title;
	protected $plugin_name;
	protected $plugin_file;
	protected $plugin_path;
	protected $domain;
	protected $version;

	public function __construct() {

		$this->plugin_path = dirname(dirname(__FILE__));
		$this->plugin_name = basename($this->plugin_path);
		$this->plugin_file = $this->plugin_path.DIRECTORY_SEPARATOR.$this->plugin_name.'.php';
		
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$pluginData = get_plugin_data($this->plugin_file);

		$this->plugin_title = $pluginData['Name'];
		$this->version      = $pluginData['Version'];
		$this->domain       = $pluginData['TextDomain'];

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->run();
	}

	public static function get_defaults(){
		$defaults = array(
			'like_text' => array(
				'label' => __("Like button text", 'kodex-posts-likes'),
				'value' => 'Like',
				'type'  => 'text'
			),
			'dislike_text' => array(
				'label' => __("Dislike button text", 'kodex-posts-likes'),
				'value' => 'Dislike',
				'type'  => 'text'
			),
			'show_dislike' => array(
				'label' => __("Display the Dislike button", 'kodex-posts-likes'),
				'value' => true,
				'type'  => 'checkbox'
			),
			'hide_counter_0' => array(
				'label' => __("Hide the buttons counters if the number is zero", 'kodex-posts-likes'),
				'value' => true,
				'type'  => 'checkbox'
			),
			'hide_counter_total' => array(
				'label' => __("Hide the buttons counter", 'kodex-posts-likes'),
				'value' => false,
				'type'  => 'checkbox'
			),
			'post_types' => array(
				'label' => __("Enable for this post types", 'kodex-posts-likes'),
				'value' => array(),
				'maybe' => self::get_all_post_types(),
				'type'  => 'array'
			),
			'include_in_post' => array(
				'label' => __("Show on the top of post", 'kodex-posts-likes'),
				'value' => 'nope',
				'type'  => 'select',
				'maybe' => array(
					'nope'   => __("Don't show automatically (I use shortcodes)", 'kodex-posts-likes'),
					'top'    => __("Show on the top of post", 'kodex-posts-likes'),
					'bottom' => __("Show on the bottom of post", 'kodex-posts-likes'),
					'both'   => __("Show on the top and on the bottom of post", 'kodex-posts-likes'),
				)
			),
			'alignement' => array(
				'label' => __("Alignment", 'kodex-posts-likes'),
				'value' => 'center',
				'type'  => 'select',
				'maybe' => array(
					'left'   => __("Left", 'kodex-posts-likes'),
					'center' => __("Center", 'kodex-posts-likes'),
					'right'  => __("Right", 'kodex-posts-likes'),
				)
			),
			'include_css' => array(
				'label' => __("Include default stylesheet", 'kodex-posts-likes'),
				'value' => true,
				'type'  => 'checkbox'
			),
			'dashboard_stats_days' => array(
				'label' => __("Number of days for the displayed stats on the admin dashboard", 'kodex-posts-likes'),
				'value' => '7',
				'type'  => 'number'
			),
		);
		return $defaults;
	}

	public static function get_all_post_types(){
		global $wp_post_types;
		$types = array();
		foreach($wp_post_types as $k=>$v){
			if( in_array($k, array(
				'revision',
				'nav_menu_item',
				'acf',
				'acf-field',
				'acf-field-group',
				'wpcf7_contact_form',
				'custom_css',
				'customize_changeset',
				'oembed_cache',
				'user_request',
				'wp_block',
				'wp_template',
				'wp_template_part',
				'wp_global_styles',
				'wp_navigation',
			)) ){
				continue;
			}

			$types[$k] = (isset($v->label)) ? $v->label : $k;
		}
		//$this->debug($types);
		return $types;
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kodex-posts-likes-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kodex-posts-likes-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kodex-posts-likes-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-kodex-posts-likes-public.php';

		$this->loader = new Kodex_Posts_Likes_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Kodex_Posts_Likes_i18n();
		$plugin_i18n->set_domain( $this->get_domain() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {
		$plugin_admin = new Kodex_Posts_Likes_Admin( $this->get_plugin_title(), $this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'plugin_action_links', 10, 2);
		$this->loader->add_action( "add_meta_boxes", $plugin_admin, 'add_meta_boxes');
		$this->loader->add_action( "wp_dashboard_setup", $plugin_admin, 'wp_dashboard_setup');
		$this->loader->add_action( "save_post", $plugin_admin, 'save_post', 20, 1);

		$this->loader->add_action( "wp_ajax_kodex_likes_dashboard_settings", $plugin_admin, 'kodex_likes_dashboard_settings');
	}

	private function define_public_hooks() {
		$plugin_public = new Kodex_Posts_Likes_Public( $this->get_plugin_title(), $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_shortcode('kodex_post_like_buttons', array($plugin_public, 'shortcode_buttons'));
		add_shortcode('kodex_post_like_count', array($plugin_public, 'shortcode_count'));
		$this->loader->add_filter('the_content', $plugin_public, 'before_after_content');

		$this->loader->add_action('wp_ajax_nopriv_kodex_posts_likes_ajax', $plugin_public, 'ajax' );
		$this->loader->add_action('wp_ajax_kodex_posts_likes_ajax', $plugin_public, 'ajax' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_title() {
		return $this->plugin_title;
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

	public function get_domain() {
		return $this->domain;
	}

}
