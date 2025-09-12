<?php

/**
 * @wordpress-plugin
 * Plugin Name:       TRUENDO | Your all-in-one website privacy solution
 * Plugin URI:         https://truendo.com/docs/how-to-add-privacy-policy/wordpress/
 * Description:       For quick and easy GDPR & Cookie compliance add Truendo to your website 
 * Version:           2.3.2
 * Author:            TRUENDO
 * Author URI:        https://www.truendo.com
 * License:           Apache-2.0
 * License URI:       https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain:       truendo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TRUENDO_WORDPRESS_PLUGIN', '2.3.2' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-truendo-activator.php
 */
function activate_truendo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-truendo-activator.php';
	Truendo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-truendo-deactivator.php
 */
function deactivate_truendo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-truendo-deactivator.php';
	Truendo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_truendo' );
register_deactivation_hook( __FILE__, 'deactivate_truendo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-truendo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_truendo() {
	$plugin = new Truendo();
	$plugin->truendo_run();
}
run_truendo();
