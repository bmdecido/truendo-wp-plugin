<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.truendo.com
 * @since      1.0.0
 *
 * @package    Truendo
 * @subpackage Truendo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Truendo
 * @subpackage Truendo/public
 * @author     Truendo Team <info@truendo.com>
 */
class Truendo_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function truendo_public_enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/truendo-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function truendo_public_enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/truendo-public.js', array('jquery'), $this->version, false);
	}

	public function truendo_check_page_builder()
	{
		// breakdance, divi and oxygen builders
		$queries = ["?breakdance", "&breakdance", '?et_fb', '&et_fb', '?ct_builder', '&ct_builder'];
		$isOkay = true;
		foreach ($queries as $s) {
			if (str_contains($_SERVER['REQUEST_URI'], $s)) {
				$isOkay = false;
			}
		}
		return $isOkay;
	}

	public function add_truendo_script(){
		if ($this->truendo_check_page_builder() && get_option('truendo_enabled')) {
			echo '<script id="truendoAutoBlock" type="text/javascript" src="https://cdn.priv.center/pc/truendo_cmp.pid.js" data-siteid="' . get_option("truendo_site_id") . '"></script>';
		}
	}

	/**
	 * Add Google Consent Mode v2 script injection for public-facing pages
	 *
	 * @since    1.0.0
	 */
	public function add_google_consent_mode_script()
	{
		// Use comprehensive validation helper
		if (!$this->should_load_consent_mode_script()) {
			return;
		}

		// Get safe configuration with error handling
		$config = $this->get_safe_consent_config();

		if (!$config) {
			// Configuration invalid, don't break page
			return;
		}

		// Output script
		echo $this->build_consent_mode_script_html($config);
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
	 * Build the Google Consent Mode script HTML with configuration
	 *
	 * @since    1.0.0
	 * @param    array    $config    Configuration array from get_consent_mode_config()
	 * @return   string             HTML script tag with Google Consent Mode initialization
	 */
	private function build_consent_mode_script_html($config)
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

		// Build complete consent mode script HTML
		$script = '<script>';
		$script .= 'window.dataLayer = window.dataLayer || [];';
		$script .= 'function gtag() { dataLayer.push(arguments); }';

		// Set default consent states using user configuration
		$script .= 'gtag("consent", "default", {';
		$script .= 'ad_storage: "' . ($consent_mode_bools['ad_storage'] ? 'granted' : 'denied') . '",';
		$script .= 'ad_user_data: "' . ($consent_mode_bools['ad_user_data'] ? 'granted' : 'denied') . '",';
		$script .= 'ad_personalization: "' . ($consent_mode_bools['ad_personalization'] ? 'granted' : 'denied') . '",';
		$script .= 'analytics_storage: "' . ($consent_mode_bools['analytics_storage'] ? 'granted' : 'denied') . '",';
		$script .= 'preferences: "' . ($consent_mode_bools['preferences'] ? 'granted' : 'denied') . '",';
		$script .= 'social_content: "' . ($consent_mode_bools['social_content'] ? 'granted' : 'denied') . '",';
		$script .= 'social_sharing: "' . ($consent_mode_bools['social_sharing'] ? 'granted' : 'denied') . '",';
		$script .= 'personalization_storage: "' . ($consent_mode_bools['personalization_storage'] ? 'granted' : 'denied') . '",';
		$script .= 'functionality_storage: "granted",';
		$script .= 'wait_for_update: ' . $safe_wait_time . '});';

		// Enable ads data redaction by default [optional]
		$script .= 'gtag("set", "ads_data_redaction", true);';

		// Set the developer id
		$script .= 'gtag("set", "developer_id.dMjBiZm", true);';

		// TRUENDO callback function for consent updates
		$script .= 'function TruendoCookieControlCallback(cookieObj) {';
		$script .= 'if (cookieObj.preferences) {';
		$script .= 'gtag("consent", "update", { preferences: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { preferences: "denied" });';
		$script .= '}';
		$script .= 'if (cookieObj.marketing) {';
		$script .= 'gtag("consent", "update", { ad_storage: "granted", ad_personalization: "granted", ad_user_data: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { ad_storage: "denied", ad_personalization: "denied", ad_user_data: "denied" });';
		$script .= '}';
		$script .= 'if (cookieObj.add_features) {';
		$script .= 'gtag("consent", "update", { functionality_storage: "granted", personalization_storage: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { functionality_storage: "denied", personalization_storage: "denied" });';
		$script .= '}';
		$script .= 'if (cookieObj.statistics) {';
		$script .= 'gtag("consent", "update", { analytics_storage: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { analytics_storage: "denied" });';
		$script .= '}';
		$script .= 'if (cookieObj.social_content) {';
		$script .= 'gtag("consent", "update", { social_content: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { social_content: "denied" });';
		$script .= '}';
		$script .= 'if (cookieObj.social_sharing) {';
		$script .= 'gtag("consent", "update", { social_sharing: "granted" });';
		$script .= '} else {';
		$script .= 'gtag("consent", "update", { social_sharing: "denied" });';
		$script .= '}';

		// WordPress Consent API updates using TRUENDO mapping
		if (get_option('truendo_wp_consent_enabled')) {
			$script .= 'if (typeof wp_set_consent === "function") {';
			$script .= 'wp_set_consent("preferences", cookieObj.preferences ? "allow" : "deny");';
			$script .= 'wp_set_consent("marketing", cookieObj.marketing ? "allow" : "deny");';
			$script .= 'wp_set_consent("statistics", cookieObj.statistics ? "allow" : "deny");';
			$script .= 'wp_set_consent("statistics-anonymous", cookieObj.statistics ? "allow" : "deny");';
			$script .= 'wp_set_consent("functional", "allow");';
			$script .= '}';
		}

		$script .= '}';
		$script .= '</script>';

		return $script;
	}

	/**
	 * Static utility method for external access to consent mode status
	 * Can be used by themes or other plugins
	 *
	 * @since    1.0.0
	 * @return   bool    Whether Google Consent Mode is active on frontend
	 */
	public static function is_consent_mode_enabled()
	{
		return get_option('truendo_enabled') &&
			   get_option('truendo_google_consent_enabled') &&
			   !empty(get_option('truendo_site_id'));
	}

	/**
	 * Helper method for safe configuration retrieval with error handling
	 * Prevents breaking page rendering if configuration is invalid
	 *
	 * @since    1.0.0
	 * @return   array|false    Safe configuration or false on error
	 */
	public function get_safe_consent_config()
	{
		try {
			if (!$this->is_google_consent_mode_active()) {
				return false;
			}

			$config = $this->get_consent_mode_config();

			// Validate config structure
			if (!is_array($config) ||
				!isset($config['default_states']) ||
				!isset($config['wait_time']) ||
				!is_array($config['default_states'])) {

				// Log error if WordPress debug is enabled
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('TRUENDO: Invalid Google Consent Mode configuration structure');
				}
				return false;
			}

			return $config;

		} catch (Exception $e) {
			// Log error but don't break page
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('TRUENDO Google Consent Mode error: ' . $e->getMessage());
			}
			return false;
		}
	}

	/**
	 * Helper method to validate consent mode script should load
	 * Includes additional checks for performance and compatibility
	 *
	 * @since    1.0.0
	 * @return   bool    Whether script should load
	 */
	public function should_load_consent_mode_script()
	{
		// Basic activation check
		if (!$this->is_google_consent_mode_active()) {
			return false;
		}

		// Page builder compatibility check
		if (!$this->truendo_check_page_builder()) {
			return false;
		}

		// Additional checks for specific contexts where script shouldn't load

		// Don't load in admin area
		if (is_admin()) {
			return false;
		}

		// Don't load for REST API requests
		if (defined('REST_REQUEST') && REST_REQUEST) {
			return false;
		}

		// Don't load for AJAX requests (unless frontend AJAX)
		if (defined('DOING_AJAX') && DOING_AJAX && is_admin()) {
			return false;
		}

		// Don't load for cron jobs
		if (defined('DOING_CRON') && DOING_CRON) {
			return false;
		}

		return true;
	}

	/**
	 * Add WordPress Consent API script injection for public-facing pages
	 *
	 * @since    1.0.0
	 */
	public function add_wp_consent_api_script()
	{
		// Use comprehensive validation helper
		if (!$this->should_load_wp_consent_script()) {
			return;
		}

		// Get safe configuration with error handling
		$config = $this->get_safe_wp_consent_config();

		if (!$config) {
			// Configuration invalid, don't break page
			return;
		}

		// Output script
		echo $this->build_wp_consent_script_html($config);
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
	 * Build the WordPress Consent API script HTML with configuration
	 *
	 * @since    1.0.0
	 * @param    array    $config    Configuration array from get_wp_consent_mode_config()
	 * @return   string             HTML script tag with WordPress Consent API initialization
	 */
	private function build_wp_consent_script_html($config)
	{
		// Validate and sanitize the configuration
		$safe_states = array();
		$valid_categories = array(
			'statistics', 'statistics-anonymous', 'marketing', 'functional', 'preferences'
		);

		foreach ($config['default_states'] as $key => $value) {
			$clean_key = sanitize_key($key);
			$clean_value = sanitize_text_field($value);

			if (in_array($clean_key, $valid_categories) && in_array($clean_value, array('allow', 'deny'))) {
				$safe_states[$clean_key] = $clean_value;
			}
		}

		// Build complete WP Consent API script HTML
		$script = '<script>';

		// Initialize WordPress Consent API
		$script .= 'window.wp_consent_type = "optin";';
		$script .= 'let wpConsentEvent = new CustomEvent("wp_consent_type_defined");';
		$script .= 'document.dispatchEvent(wpConsentEvent);';

		// Set default WordPress Consent API states based on admin configuration
		$script .= 'if (typeof wp_set_consent === "function") {';
		$script .= 'wp_set_consent("preferences", "' . esc_js($safe_states['preferences'] ?? 'deny') . '");';
		$script .= 'wp_set_consent("marketing", "' . esc_js($safe_states['marketing'] ?? 'deny') . '");';
		$script .= 'wp_set_consent("statistics", "' . esc_js($safe_states['statistics'] ?? 'deny') . '");';
		$script .= 'wp_set_consent("statistics-anonymous", "' . esc_js($safe_states['statistics-anonymous'] ?? 'deny') . '");';
		$script .= 'wp_set_consent("functional", "allow");'; // necessary cookies always allowed
		$script .= '}';

		$script .= '</script>';

		return $script;
	}

	/**
	 * Helper method for safe WP Consent API configuration retrieval with error handling
	 * Prevents breaking page rendering if configuration is invalid
	 *
	 * @since    1.0.0
	 * @return   array|false    Safe configuration or false on error
	 */
	public function get_safe_wp_consent_config()
	{
		try {
			if (!$this->is_wp_consent_mode_active()) {
				return false;
			}

			$config = $this->get_wp_consent_mode_config();

			// Validate config structure
			if (!is_array($config) ||
				!isset($config['default_states']) ||
				!is_array($config['default_states'])) {

				// Log error if WordPress debug is enabled
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('TRUENDO: Invalid WordPress Consent API configuration structure');
				}
				return false;
			}

			return $config;

		} catch (Exception $e) {
			// Log error but don't break page
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('TRUENDO WordPress Consent API error: ' . $e->getMessage());
			}
			return false;
		}
	}

	/**
	 * Helper method to validate WP consent script should load
	 * Includes additional checks for performance and compatibility
	 *
	 * @since    1.0.0
	 * @return   bool    Whether script should load
	 */
	public function should_load_wp_consent_script()
	{
		// Basic activation check
		if (!$this->is_wp_consent_mode_active()) {
			return false;
		}

		// Page builder compatibility check
		if (!$this->truendo_check_page_builder()) {
			return false;
		}

		// Additional checks for specific contexts where script shouldn't load

		// Don't load in admin area
		if (is_admin()) {
			return false;
		}

		// Don't load for REST API requests
		if (defined('REST_REQUEST') && REST_REQUEST) {
			return false;
		}

		// Don't load for AJAX requests (unless frontend AJAX)
		if (defined('DOING_AJAX') && DOING_AJAX && is_admin()) {
			return false;
		}

		// Don't load for cron jobs
		if (defined('DOING_CRON') && DOING_CRON) {
			return false;
		}

		return true;
	}
}
