<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.truendo.com
 * @since      1.0.0
 *
 * @package    Truendo
 * @subpackage Truendo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Truendo
 * @subpackage Truendo/admin
 * @author     Truendo Team <info@truendo.com>
 */
class Truendo_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function truendo_admin_enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Truendo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Truendo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/truendo-admin.css', array(), $this->version, 'all');
	}

	public function truendo_admin_add_settings()
	{

		register_setting('truendo_settings', 'truendo_enabled', array('type' => 'boolean', 'default' => false));
		register_setting('truendo_settings', 'truendo_site_id', array('type' => 'string'));
		register_setting('truendo_settings', 'truendo_language', array('type' => 'string', 'default' => 'auto'));

		register_setting('truendo_settings', 'tru_stat_truendo_header_scripts_json', array('type' => 'string', 'default' => ''));
		register_setting('truendo_settings', 'tru_mark_truendo_header_scripts_json', array('type' => 'string', 'default' => ''));
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function truendo_admin_enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Truendo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Truendo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/truendo-admin.js', array('jquery'), $this->version, true);

		// Localize the script with new data
		$object = array(
			'tru_stat_header_scripts' => get_option('tru_stat_truendo_header_scripts_json'),
			'tru_mark_header_scripts' => get_option('tru_mark_truendo_header_scripts_json'),
		);
		wp_localize_script($this->plugin_name, 'truendo_local', $object);

		// Enqueued script with localized data.
		wp_enqueue_script($this->plugin_name);
	}

	public function truendo_admin_add_action_links($links)
	{
		$settings_link = array(
			'<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
			'<a href="https://docs.truendo.com/" target="_blank">' . __('How To', $this->plugin_name) . '</a>',
		);

		if (get_option('truendo_site_id') != false && get_option('truendo_site_id') != '' && get_option('truendo_enabled')) {
			$settings_link[] = '<a href="https://console.truendo.com' . '" target="_blank">' . __('Truendo Dashboard') . '</a>';
		}

		return array_merge($settings_link, $links);
	}

	// public function truendo_admin_display_admin_page()
	// {
	// 	return array($this, 'truendo_admin_render_admin_page');
	// }

	public function truendo_admin_display_admin_page()
	{
		add_menu_page(
			__('TRUENDO Settings', 'truendo'),
			__('TRUENDO', 'truendo'),
			'manage_options',
			$this->plugin_name,
			array($this, 'truendo_admin_render_admin_page'),
			'https://uploads-ssl.webflow.com/6102a77c4733362012bd355d/631096558e12aaa60e02baa4_truendokey.svg',
			80
		);
	}


	function my_plugin_menu()
	{
		add_submenu_page(
			'options-general.php',
			'TRUENDO settings',
			'TRUENDO settings',
			'manage_options',
			'truendo_wordpress',
			null
		);
	}


	//   options-general.php?page=truendo_wordpress


	public function truendo_admin_render_admin_page()
	{

		include_once 'partials/truendo-admin-display.php';
	}

	
	public function truendo_check_page_builder()
	{
		// breakdance, divi and oxygen builders
		$queries = ["?breakdance", '?et_fb', '&et_fb', '?ct_builder'];

		$isOkay = true;
		foreach ($queries as $s) {
			if (str_contains($_SERVER['REQUEST_URI'], $s)) {
				$isOkay = false;
			}
		}
		return $isOkay;
	}
	public function add_truendo_script()
	{
		if ($this->truendo_check_page_builder()) {
			if (get_option('truendo_enabled')) {
				if (get_option('truendo_site_id') != '') { ?> <script async>
						var s = document.createElement("script");
						s.async = !0;
						s.id = "truendoAutoBlock";
						s.type = "text/javascript";
						s.src = "https://cdn.priv.center/pc/truendo_cmp.pid.js";
						s.dataset.siteid = "<?php echo get_option('truendo_site_id') ?>";
						document.querySelector("head").prepend(s);
					</script> <?php }
						}
					}
				}
			}
