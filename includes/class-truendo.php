<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.truendo.com
 * @since      1.0.0
 *
 * @package    Truendo
 * @subpackage Truendo/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Truendo
 * @subpackage Truendo/includes
 * @author     Truendo Team <info@truendo.com>
 */
class Truendo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Truendo_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TRUENDO_WORDPRESS_PLUGIN' ) ) {
			$this->version = TRUENDO_WORDPRESS_PLUGIN;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'truendo_wordpress';

		$this->truendo_load_dependencies();
		$this->truendo_set_locale();
		$this->truendo_define_admin_hooks();
		$this->truendo_define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Truendo_Loader. Orchestrates the hooks of the plugin.
	 * - Truendo_i18n. Defines internationalization functionality.
	 * - Truendo_Admin. Defines all hooks for the admin area.
	 * - Truendo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function truendo_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-truendo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-truendo-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-truendo-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-truendo-public.php';

		$this->loader = new Truendo_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Truendo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function truendo_set_locale() {
		$plugin_i18n = new Truendo_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function truendo_define_admin_hooks() {
		$plugin_admin = new Truendo_Admin( $this->truendo_get_plugin_name(), $this->truendo_get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'truendo_admin_display_admin_page' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'my_plugin_menu' );
		// Add Settings link to the plugin
		$this->loader->add_filter( 'plugin_action_links_'. plugin_basename( plugin_dir_path( __DIR__ ) . 'truendo.php' ), $plugin_admin, 'truendo_admin_add_action_links' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'truendo_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'truendo_admin_enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'truendo_admin_add_settings' );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function truendo_define_public_hooks() {
		$plugin_public = new Truendo_Public( $this->truendo_get_plugin_name(), $this->truendo_get_version());
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'truendo_public_enqueue_scripts' );

		// TRUENDO CMP script injection (priority 1 - loads FIRST for immediate consent panel display)
		$this->loader->add_action( 'wp_head', $plugin_public, 'add_truendo_script', 1);

		// Google Consent Mode v2 script injection (priority 1 - loads second, before any Google tags)
		// CRITICAL: Must load before SiteKit and other Google tag plugins to set consent defaults
		$this->loader->add_action( 'wp_head', $plugin_public, 'add_google_consent_mode_script', 1 );

		// WordPress Consent API script injection (priority 1 - loads third, standalone mode only)
		$this->loader->add_action( 'wp_head', $plugin_public, 'add_wp_consent_api_script', 1 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function truendo_run() {
		$this->loader->truendo_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function truendo_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Truendo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function truendo_get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function truendo_get_version() {
		return $this->version;
	}
}
