<?php

class Safe_Media_Delete {

	
	protected $loader;

	
	protected $plugin_name;

	
	protected $version;


	public function __construct() {
		if ( defined( 'SAFE_MEDIA_DELETE_VERSION' ) ) {
			$this->version = SAFE_MEDIA_DELETE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'safe-media-delete';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-safe-media-delete-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-safe-media-delete-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-safe-media-delete-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-safe-media-delete-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-safe-media-delete-endpoints.php';

		$this->loader = new Safe_Media_Delete_Loader();

		require_once plugin_dir_path( dirname( __FILE__ ) ). 'includes/cmb2/init.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ). 'includes/class-find-media-attachment.php';

	}


	private function set_locale() {

		$plugin_i18n = new Safe_Media_Delete_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new Safe_Media_Delete_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_media = new Find_Media_Attachment();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'cmb2_sample_metaboxes' );

		$this->loader->add_action( 'manage_media_custom_column', $plugin_admin_media, 'manage_media_custom_column' , 10, 2 );
		$this->loader->add_filter( 'manage_media_columns', $plugin_admin_media,  'manage_media_columns',  10, 1 );
		
		$this->loader->add_action( 'delete_attachment', $plugin_admin, 'delete_attachment_func'); 
	}


	private function define_public_hooks() {

		$plugin_public = new Safe_Media_Delete_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_endpoints = new Safe_Media_Delete_Endpoints();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action('rest_api_init', $plugin_endpoints, 'register_soft_media_delete');

	}

	public function run() {
		$this->loader->run();
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

}
