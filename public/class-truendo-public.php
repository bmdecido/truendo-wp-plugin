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
}
