<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.truendo.com
 * @since      1.0.0
 *
 * @package    Truendo
 * @subpackage Truendo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Truendo
 * @subpackage Truendo/includes
 * @author     Truendo Team <info@truendo.com>
 */
class Truendo_Activator {
	/**
	 * Check for required dependencies and activate the plugin.
	 *
	 * Verifies that the WP Consent API plugin is installed and active.
	 * If not, deactivates this plugin and displays an error message.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Check if WP Consent API plugin is active
		if ( ! self::is_wp_consent_api_active() ) {
			// Deactivate this plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// Display error message
			wp_die(
				sprintf(
					'<h1>%s</h1><p>%s</p><p><a href="%s">%s</a> | <a href="%s">%s</a></p>',
					esc_html__( 'Plugin Dependency Required', 'truendo' ),
					esc_html__( 'The TRUENDO plugin requires the WP Consent API plugin to be installed and activated. Please install and activate WP Consent API before activating TRUENDO.', 'truendo' ),
					esc_url( admin_url( 'plugin-install.php?s=wp-consent-api&tab=search&type=term' ) ),
					esc_html__( 'Install WP Consent API', 'truendo' ),
					esc_url( admin_url( 'plugins.php' ) ),
					esc_html__( 'Return to Plugins', 'truendo' )
				),
				esc_html__( 'Plugin Dependency Required', 'truendo' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Check if WP Consent API plugin is active.
	 *
	 * @since    2.4.0
	 * @return   bool    True if WP Consent API is active, false otherwise.
	 */
	private static function is_wp_consent_api_active() {
		// Check if the function from WP Consent API exists
		if ( function_exists( 'wp_has_consent' ) ) {
			return true;
		}

		// Fallback: Check if plugin is active (works for both single and multisite)
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'wp-consent-api/wp-consent-api.php' );
	}
}
