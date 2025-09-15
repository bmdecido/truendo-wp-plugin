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
		// Check page builder compatibility (same as TRUENDO script)
		if (!$this->truendo_check_page_builder()) {
			return;
		}

		// Verify all dependencies are enabled
		if (!$this->is_google_consent_mode_active()) {
			return;
		}

		// Get configuration and output script
		$config = $this->get_consent_mode_config();
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
		$script .= 'functionality_storage: "' . ($consent_mode_bools['functionality_storage'] ? 'granted' : 'denied') . '",';
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
		$script .= '}';
		$script .= '</script>';

		return $script;
	}
}
