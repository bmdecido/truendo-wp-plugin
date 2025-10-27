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

		// Google Consent Mode v2 settings
		register_setting('truendo_settings', 'truendo_google_consent_enabled', array(
			'type' => 'boolean',
			'default' => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'description' => 'Enable Google Consent Mode v2 integration'
		));

		register_setting('truendo_settings', 'truendo_google_consent_default_states', array(
			'type' => 'array',
			'default' => array(),
			'sanitize_callback' => array($this, 'truendo_sanitize_consent_states'),
			'description' => 'Default consent states for Google categories'
		));

		register_setting('truendo_settings', 'truendo_google_consent_wait_time', array(
			'type' => 'integer',
			'default' => 500,
			'sanitize_callback' => array($this, 'truendo_sanitize_wait_time'),
			'description' => 'Milliseconds to wait for user consent before applying defaults'
		));

		// WordPress Consent API settings
		register_setting('truendo_settings', 'truendo_wp_consent_enabled', array(
			'type' => 'boolean',
			'default' => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'description' => 'Enable WordPress Consent API integration'
		));

		register_setting('truendo_settings', 'truendo_wp_consent_default_states', array(
			'type' => 'array',
			'default' => array(),
			'sanitize_callback' => array($this, 'truendo_sanitize_wp_consent_states'),
			'description' => 'Default consent states for WordPress Consent API categories'
		));

		// TruSettings window object configuration
		register_setting('truendo_settings', 'truendo_trusettings_nofont', array(
			'type' => 'boolean',
			'default' => false,
			'sanitize_callback' => 'rest_sanitize_boolean'
		));

		register_setting('truendo_settings', 'truendo_trusettings_transparency', array(
			'type' => 'boolean',
			'default' => true,
			'sanitize_callback' => 'rest_sanitize_boolean'
		));

		register_setting('truendo_settings', 'truendo_trusettings_accessibility', array(
			'type' => 'boolean',
			'default' => false,
			'sanitize_callback' => 'rest_sanitize_boolean'
		));

		register_setting('truendo_settings', 'truendo_trusettings_accessibility_border_color', array(
			'type' => 'string',
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		));

		register_setting('truendo_settings', 'truendo_trusettings_lang', array(
			'type' => 'string',
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		));

		register_setting('truendo_settings', 'truendo_trusettings_popup_delay', array(
			'type' => 'integer',
			'default' => 0,
			'sanitize_callback' => 'absint'
		));

		register_setting('truendo_settings', 'truendo_trusettings_autoblocking_disabled', array(
			'type' => 'boolean',
			'default' => false,
			'sanitize_callback' => 'rest_sanitize_boolean'
		));
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

	/**
	 * Add Google Consent Mode v2 script injection
	 *
	 * @since    1.0.0
	 */
	public function add_google_consent_mode_script()
	{
		try {
			// Check page builder compatibility (same as TRUENDO script)
			if (!$this->truendo_check_page_builder()) {
				return;
			}

			// Verify all dependencies are enabled
			if (!$this->is_google_consent_mode_active()) {
				return;
			}

			// Get configuration and output script with error handling
			$config = $this->get_consent_mode_config();

			if (!$config || !is_array($config)) {
				// Log error but don't break page
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('TRUENDO: Invalid Google Consent Mode configuration in admin');
				}
				return;
			}

			$this->output_consent_mode_script($config);

		} catch (Exception $e) {
			// Don't break page rendering on error
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('TRUENDO Google Consent Mode admin injection error: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Add WordPress Consent API script injection
	 *
	 * @since    1.0.0
	 */
	public function add_wp_consent_api_script()
	{
		try {
			// Check page builder compatibility (same as TRUENDO script)
			if (!$this->truendo_check_page_builder()) {
				return;
			}

			// Verify all dependencies are enabled
			if (!$this->is_wp_consent_mode_active()) {
				return;
			}

			// Get configuration and output script with error handling
			$config = $this->get_wp_consent_mode_config();

			if (!$config || !is_array($config)) {
				// Log error but don't break page
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('TRUENDO: Invalid WordPress Consent API configuration in admin');
				}
				return;
			}

			$this->output_wp_consent_script($config);

		} catch (Exception $e) {
			// Don't break page rendering on error
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('TRUENDO WordPress Consent API admin injection error: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Check if Google Consent Mode is active and properly configured
	 *
	 * @since    1.0.0
	 * @return   bool    Whether Google Consent Mode should be active
	 */
	private function is_google_consent_mode_active()
	{
		return get_option('truendo_enabled') &&
			   get_option('truendo_google_consent_enabled') &&
			   !empty(get_option('truendo_site_id'));
	}

	/**
	 * Get Google Consent Mode configuration with fallbacks
	 *
	 * @since    1.0.0
	 * @return   array    Configuration array with default_states and wait_time
	 */
	private function get_consent_mode_config()
	{
		$default_states = get_option('truendo_google_consent_default_states', array());

		// Provide fallback if no states configured
		if (empty($default_states)) {
			$default_states = array(
				'ad_storage' => 'denied',
				'ad_user_data' => 'denied',
				'ad_personalization' => 'denied',
				'analytics_storage' => 'denied',
				'preferences' => 'denied',
				'social_content' => 'denied',
				'social_sharing' => 'denied',
				'personalization_storage' => 'denied',
				'functionality_storage' => 'denied'
			);
		}

		return array(
			'default_states' => $default_states,
			'wait_time' => (int) get_option('truendo_google_consent_wait_time', 500)
		);
	}

	/**
	 * Output the Google Consent Mode script with configuration
	 *
	 * @since    1.0.0
	 * @param    array    $config    Configuration array from get_consent_mode_config()
	 */
	private function output_consent_mode_script($config)
	{
		// Validate and sanitize the configuration
		$safe_states = array();
		$valid_categories = array(
			'ad_storage', 'ad_user_data', 'ad_personalization',
			'analytics_storage', 'preferences', 'social_content',
			'social_sharing', 'personalization_storage', 'functionality_storage'
		);

		foreach ($config['default_states'] as $key => $value) {
			$clean_key = sanitize_key($key);
			$clean_value = sanitize_text_field($value);

			if (in_array($clean_key, $valid_categories) && in_array($clean_value, array('granted', 'denied'))) {
				$safe_states[$clean_key] = $clean_value;
			}
		}

		$safe_wait_time = absint($config['wait_time']);

		// Convert 'granted'/'denied' strings to boolean equivalents for the script template
		$consent_mode_bools = array();
		foreach ($safe_states as $category => $state) {
			$consent_mode_bools[$category] = ($state === 'granted');
		}

		// Prepare WordPress Consent API defaults (if enabled)
		$wp_consent_enabled = get_option('truendo_wp_consent_enabled');
		$wp_safe_states = array();
		if ($wp_consent_enabled) {
			$wp_config = $this->get_wp_consent_mode_config();
			$wp_valid_categories = array('statistics', 'statistics-anonymous', 'marketing', 'functional', 'preferences');

			foreach ($wp_config['default_states'] as $key => $value) {
				// Don't use sanitize_key() as it converts hyphens to underscores
				$clean_key = sanitize_text_field($key);
				$clean_value = sanitize_text_field($value);
				if (in_array($clean_key, $wp_valid_categories, true) && in_array($clean_value, array('allow', 'deny'), true)) {
					$wp_safe_states[$clean_key] = $clean_value;
				}
			}
		}
		?>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() {
			dataLayer.push(arguments);
		}

		// Set default consent states using user configuration
		gtag("consent", "default", {
			ad_storage: "<?php echo $consent_mode_bools['ad_storage'] ? 'granted' : 'denied'; ?>",
			ad_user_data: "<?php echo $consent_mode_bools['ad_user_data'] ? 'granted' : 'denied'; ?>",
			ad_personalization: "<?php echo $consent_mode_bools['ad_personalization'] ? 'granted' : 'denied'; ?>",
			analytics_storage: "<?php echo $consent_mode_bools['analytics_storage'] ? 'granted' : 'denied'; ?>",
			preferences: "<?php echo $consent_mode_bools['preferences'] ? 'granted' : 'denied'; ?>",
			social_content: "<?php echo $consent_mode_bools['social_content'] ? 'granted' : 'denied'; ?>",
			social_sharing: "<?php echo $consent_mode_bools['social_sharing'] ? 'granted' : 'denied'; ?>",
			personalization_storage: "<?php echo $consent_mode_bools['personalization_storage'] ? 'granted' : 'denied'; ?>",
			functionality_storage: "<?php echo $consent_mode_bools['functionality_storage'] ? 'granted' : 'denied'; ?>",
			wait_for_update: <?php echo $safe_wait_time; ?> // milliseconds to wait for update
		});

		// Enable ads data redaction by default [optional]
		gtag("set", "ads_data_redaction", true);

		// Set the developer id
		gtag("set", "developer_id.dMjBiZm", true);

		window.addEventListener('TruendoCookieControl', function(event) {
			var cookieSettings = event.detail;
			if (cookieSettings.preferences) {
				gtag("consent", "update", {
					preferences: "granted",
				});
			} else {
				gtag("consent", "update", {
					preferences: "denied",
				});
			}
			if (cookieSettings.marketing) {
				gtag("consent", "update", {
					ad_storage: "granted",
					ad_personalization: "granted",
					ad_user_data: "granted",
				});
			} else {
				gtag("consent", "update", {
					ad_storage: "denied",
					ad_personalization: "denied",
					ad_user_data: "denied",
				});
			}
			if (cookieSettings.add_features) {
				gtag("consent", "update", {
					functionality_storage: "granted",
					personalization_storage: "granted",
				});
			} else {
				gtag("consent", "update", {
					functionality_storage: "denied",
					personalization_storage: "denied",
				});
			}
			if (cookieSettings.statistics) {
				gtag("consent", "update", {
					analytics_storage: "granted",
				});
			} else {
				gtag("consent", "update", {
					analytics_storage: "denied",
				});
			}
			if (cookieSettings.social_content) {
				gtag("consent", "update", {
					social_content: "granted",
				});
			} else {
				gtag("consent", "update", {
					social_content: "denied",
				});
			}
			if (cookieSettings.social_sharing) {
				gtag("consent", "update", {
					social_sharing: "granted",
				});
			} else {
				gtag("consent", "update", {
					social_sharing: "denied",
				});
			}

			// WordPress Consent API updates using TRUENDO mapping
			<?php if (get_option('truendo_wp_consent_enabled')): ?>
			if (typeof wp_set_consent === 'function') {
				// preferences -> preferences
				wp_set_consent('preferences', cookieSettings.preferences ? 'allow' : 'deny');

				// marketing -> marketing
				wp_set_consent('marketing', cookieSettings.marketing ? 'allow' : 'deny');

				// statistics -> statistics, statistics-anonymous
				wp_set_consent('statistics', cookieSettings.statistics ? 'allow' : 'deny');
				wp_set_consent('statistics-anonymous', cookieSettings.statistics ? 'allow' : 'deny');

				// necessary -> functional (always allowed)
				wp_set_consent('functional', 'allow');
			}
			<?php endif; ?>
		});

		<?php if ($wp_consent_enabled): ?>
		// WordPress Consent API - Set default states immediately
		if (typeof wp_set_consent === 'function') {
			wp_set_consent('preferences', '<?php echo esc_js($wp_safe_states['preferences'] ?? 'deny'); ?>');
			wp_set_consent('marketing', '<?php echo esc_js($wp_safe_states['marketing'] ?? 'deny'); ?>');
			wp_set_consent('statistics', '<?php echo esc_js($wp_safe_states['statistics'] ?? 'deny'); ?>');
			wp_set_consent('statistics-anonymous', '<?php echo esc_js($wp_safe_states['statistics-anonymous'] ?? 'deny'); ?>');
			wp_set_consent('functional', 'allow');
		} else {
			console.warn('TRUENDO: wp_set_consent function not found. Please install a WordPress Consent API plugin.');
		}
		<?php endif; ?>
		</script>
		<?php
	}

	/**
	 * Check if WordPress Consent API is active and properly configured
	 *
	 * @since    1.0.0
	 * @return   bool    Whether WordPress Consent API should be active
	 */
	private function is_wp_consent_mode_active()
	{
		return get_option('truendo_enabled') &&
			   get_option('truendo_wp_consent_enabled') &&
			   !empty(get_option('truendo_site_id'));
	}

	/**
	 * Get WordPress Consent API configuration with fallbacks
	 *
	 * @since    1.0.0
	 * @return   array    Configuration array with default_states
	 */
	private function get_wp_consent_mode_config()
	{
		$default_states = get_option('truendo_wp_consent_default_states', array());

		// Provide fallback if no states configured
		if (empty($default_states)) {
			$default_states = array(
				'statistics' => 'deny',
				'statistics-anonymous' => 'deny',
				'marketing' => 'deny',
				'functional' => 'allow', // necessary cookies always allowed
				'preferences' => 'deny'
			);
		}

		return array(
			'default_states' => $default_states
		);
	}

	/**
	 * Output the WordPress Consent API script with configuration
	 *
	 * @since    1.0.0
	 * @param    array    $config    Configuration array from get_wp_consent_mode_config()
	 */
	private function output_wp_consent_script($config)
	{
		// Validate and sanitize the configuration
		$safe_states = array();
		$valid_categories = array(
			'statistics', 'statistics-anonymous', 'marketing', 'functional', 'preferences'
		);

		foreach ($config['default_states'] as $key => $value) {
			// Don't use sanitize_key() as it converts hyphens to underscores
			$clean_key = sanitize_text_field($key);
			$clean_value = sanitize_text_field($value);

			if (in_array($clean_key, $valid_categories, true) && in_array($clean_value, array('allow', 'deny'), true)) {
				$safe_states[$clean_key] = $clean_value;
			}
		}

		?>
		<script>
		// Initialize WordPress Consent API
		window.wp_consent_type = 'optin';

		// Dispatch event when consent type is defined
		let wpConsentEvent = new CustomEvent('wp_consent_type_defined');
		document.dispatchEvent(wpConsentEvent);

		// Set default WordPress Consent API states based on admin configuration
		if (typeof wp_set_consent === 'function') {
			// Set defaults for each WP category based on admin settings
			wp_set_consent('preferences', '<?php echo esc_js($safe_states['preferences'] ?? 'deny'); ?>');
			wp_set_consent('marketing', '<?php echo esc_js($safe_states['marketing'] ?? 'deny'); ?>');
			wp_set_consent('statistics', '<?php echo esc_js($safe_states['statistics'] ?? 'deny'); ?>');
			wp_set_consent('statistics-anonymous', '<?php echo esc_js($safe_states['statistics-anonymous'] ?? 'deny'); ?>');
			wp_set_consent('functional', 'allow'); // necessary cookies always allowed
		}
		</script>
		<?php
	}

	/**
	 * Sanitize consent states array for Google Consent Mode v2
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw consent states input
	 * @return   array              Sanitized consent states
	 */
	public function truendo_sanitize_consent_states($input)
	{
		$valid_categories = array(
			'ad_storage', 'ad_user_data', 'ad_personalization',
			'analytics_storage', 'preferences', 'social_content',
			'social_sharing', 'personalization_storage', 'functionality_storage'
		);

		$valid_states = array('granted', 'denied');
		$sanitized = array();

		if (is_array($input)) {
			foreach ($input as $category => $state) {
				$clean_category = sanitize_text_field($category);
				$clean_state = sanitize_text_field($state);

				if (in_array($clean_category, $valid_categories) &&
					in_array($clean_state, $valid_states)) {
					$sanitized[$clean_category] = $clean_state;
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize wait time value for Google Consent Mode v2
	 *
	 * @since    1.0.0
	 * @param    int      $input    Raw wait time input
	 * @return   int                Sanitized wait time (500-5000ms)
	 */
	public function truendo_sanitize_wait_time($input)
	{
		$wait_time = absint($input);

		// Enforce bounds from functional requirements
		if ($wait_time < 500) {
			$wait_time = 500;
		} elseif ($wait_time > 5000) {
			$wait_time = 5000;
		}

		return $wait_time;
	}

	/**
	 * Sanitize consent states array for WordPress Consent API
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw consent states input
	 * @return   array              Sanitized consent states
	 */
	public function truendo_sanitize_wp_consent_states($input)
	{
		$valid_categories = array(
			'statistics', 'statistics-anonymous', 'marketing', 'functional', 'preferences'
		);

		$valid_states = array('allow', 'deny');
		$sanitized = array();

		if (is_array($input)) {
			foreach ($input as $category => $state) {
				$clean_category = sanitize_text_field($category);
				$clean_state = sanitize_text_field($state);

				if (in_array($clean_category, $valid_categories) &&
					in_array($clean_state, $valid_states)) {
					$sanitized[$clean_category] = $clean_state;
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Static utility method to get Google Consent Mode configuration
	 * Can be used by other parts of the plugin
	 *
	 * @since    1.0.0
	 * @return   array|false    Configuration array or false if not active
	 */
	public static function truendo_get_google_consent_config()
	{
		// Check if Google Consent Mode is active
		if (!get_option('truendo_enabled') ||
			!get_option('truendo_google_consent_enabled') ||
			empty(get_option('truendo_site_id'))) {
			return false;
		}

		$default_states = get_option('truendo_google_consent_default_states', array());

		// Provide fallback if no states configured
		if (empty($default_states)) {
			$default_states = array(
				'ad_storage' => 'denied',
				'ad_user_data' => 'denied',
				'ad_personalization' => 'denied',
				'analytics_storage' => 'denied',
				'preferences' => 'denied',
				'social_content' => 'denied',
				'social_sharing' => 'denied',
				'personalization_storage' => 'denied',
				'functionality_storage' => 'denied'
			);
		}

		return array(
			'enabled' => true,
			'default_states' => $default_states,
			'wait_time' => (int) get_option('truendo_google_consent_wait_time', 500),
			'truendo_site_id' => get_option('truendo_site_id'),
			'truendo_enabled' => get_option('truendo_enabled')
		);
	}

	/**
	 * Static utility method to check if Google Consent Mode should be active
	 * Can be used by other parts of the plugin or themes
	 *
	 * @since    1.0.0
	 * @return   bool    Whether Google Consent Mode is active
	 */
	public static function truendo_is_google_consent_mode_active()
	{
		return get_option('truendo_enabled') &&
			   get_option('truendo_google_consent_enabled') &&
			   !empty(get_option('truendo_site_id'));
	}

	/**
	 * Static utility method to get sanitized default consent states
	 * Returns properly validated consent states for external use
	 *
	 * @since    1.0.0
	 * @return   array    Validated consent states array
	 */
	public static function truendo_get_sanitized_consent_states()
	{
		$config = self::truendo_get_google_consent_config();

		if (!$config) {
			return array();
		}

		$safe_states = array();
		$valid_categories = array(
			'ad_storage', 'ad_user_data', 'ad_personalization',
			'analytics_storage', 'preferences', 'social_content',
			'social_sharing', 'personalization_storage', 'functionality_storage'
		);

		foreach ($config['default_states'] as $key => $value) {
			$clean_key = sanitize_key($key);
			$clean_value = sanitize_text_field($value);

			if (in_array($clean_key, $valid_categories) && in_array($clean_value, array('granted', 'denied'))) {
				$safe_states[$clean_key] = $clean_value;
			}
		}

		// Ensure all required categories are present
		foreach ($valid_categories as $category) {
			if (!isset($safe_states[$category])) {
				$safe_states[$category] = 'denied'; // Safe default
			}
		}

		return $safe_states;
	}

	/**
	 * Static utility method to get WordPress Consent API configuration
	 * Can be used by other parts of the plugin
	 *
	 * @since    1.0.0
	 * @return   array|false    Configuration array or false if not active
	 */
	public static function truendo_get_wp_consent_config()
	{
		// Check if WP Consent API is active
		if (!get_option('truendo_enabled') ||
			!get_option('truendo_wp_consent_enabled') ||
			empty(get_option('truendo_site_id'))) {
			return false;
		}

		$default_states = get_option('truendo_wp_consent_default_states', array());

		// Provide fallback if no states configured
		if (empty($default_states)) {
			$default_states = array(
				'statistics' => 'deny',
				'statistics-anonymous' => 'deny',
				'marketing' => 'deny',
				'functional' => 'allow', // necessary cookies always allowed
				'preferences' => 'deny'
			);
		}

		return array(
			'enabled' => true,
			'default_states' => $default_states,
			'truendo_site_id' => get_option('truendo_site_id'),
			'truendo_enabled' => get_option('truendo_enabled')
		);
	}

	/**
	 * Static utility method to check if WordPress Consent API should be active
	 * Can be used by other parts of the plugin or themes
	 *
	 * @since    1.0.0
	 * @return   bool    Whether WordPress Consent API is active
	 */
	public static function truendo_is_wp_consent_mode_active()
	{
		return get_option('truendo_enabled') &&
			   get_option('truendo_wp_consent_enabled') &&
			   !empty(get_option('truendo_site_id'));
	}

	/**
	 * Static utility method to get sanitized default WP consent states
	 * Returns properly validated consent states for external use
	 *
	 * @since    1.0.0
	 * @return   array    Validated consent states array
	 */
	public static function truendo_get_sanitized_wp_consent_states()
	{
		$config = self::truendo_get_wp_consent_config();

		if (!$config) {
			return array();
		}

		$safe_states = array();
		$valid_categories = array(
			'statistics', 'statistics-anonymous', 'marketing', 'functional', 'preferences'
		);

		foreach ($config['default_states'] as $key => $value) {
			// Don't use sanitize_key() as it converts hyphens to underscores
			$clean_key = sanitize_text_field($key);
			$clean_value = sanitize_text_field($value);

			if (in_array($clean_key, $valid_categories, true) && in_array($clean_value, array('allow', 'deny'), true)) {
				$safe_states[$clean_key] = $clean_value;
			}
		}

		// Ensure all required categories are present
		foreach ($valid_categories as $category) {
			if (!isset($safe_states[$category])) {
				// functional should always be 'allow', others default to 'deny'
				$safe_states[$category] = ($category === 'functional') ? 'allow' : 'deny';
			}
		}

		return $safe_states;
	}

	/**
	 * Check if WP Consent API plugin is active (dependency check).
	 *
	 * @since    2.4.0
	 * @return   bool    True if WP Consent API is active, false otherwise.
	 */
	public static function is_wp_consent_api_active()
	{
		// Check if the function from WP Consent API exists
		if (function_exists('wp_has_consent')) {
			return true;
		}

		// Fallback: Check if plugin is active (works for both single and multisite)
		if (!function_exists('is_plugin_active')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active('wp-consent-api/wp-consent-api.php');
	}

	/**
	 * Display admin notice if WP Consent API dependency is missing.
	 *
	 * @since    2.4.0
	 */
	public function truendo_dependency_admin_notice()
	{
		if (!self::is_wp_consent_api_active()) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e('TRUENDO Plugin Dependency Missing', 'truendo'); ?></strong>
				</p>
				<p>
					<?php esc_html_e('The TRUENDO plugin requires the WP Consent API plugin to be installed and activated. Please install and activate WP Consent API to use TRUENDO.', 'truendo'); ?>
				</p>
				<p>
					<a href="<?php echo esc_url(admin_url('plugin-install.php?s=wp-consent-api&tab=search&type=term')); ?>" class="button button-primary">
						<?php esc_html_e('Install WP Consent API', 'truendo'); ?>
					</a>
					<a href="<?php echo esc_url(admin_url('plugins.php')); ?>" class="button">
						<?php esc_html_e('Manage Plugins', 'truendo'); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

}
