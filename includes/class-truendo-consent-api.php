<?php
/**
 * Truendo Consent API Functions
 * 
 * This file provides fallback implementations of WordPress Consent API functions
 * when the official WordPress Consent API plugin is not installed.
 * 
 * @package    Truendo
 * @subpackage Truendo/includes
 * @since      2.5.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class to handle consent API functionality when WP Consent API plugin is not available
 */
class Truendo_Consent_API {
    
    /**
     * Cookie prefix for consent cookies
     * @var string
     */
    private static $cookie_prefix = 'wp_consent';
    
    /**
     * Valid consent categories
     * @var array
     */
    private static $consent_categories = array(
        'functional',
        'preferences', 
        'statistics-anonymous',
        'statistics',
        'marketing'
    );
    
    /**
     * Valid consent values
     * @var array
     */
    private static $consent_values = array(
        'allow',
        'deny'
    );
    
    /**
     * Cookie expiration in days
     * @var int
     */
    private static $cookie_expiration_days = 365;
    
    /**
     * Initialize the consent API functions if they don't exist
     */
    public static function init() {
        // Only create these functions if the WP Consent API plugin is not active
        if ( ! function_exists( 'wp_set_consent' ) ) {
            /**
             * Set consent for a specific category
             * 
             * @param string $category The consent category
             * @param string $value    The consent value ('allow' or 'deny')
             */
            function wp_set_consent( $category, $value ) {
                // Validate category
                if ( ! in_array( $category, Truendo_Consent_API::get_consent_categories(), true ) ) {
                    return;
                }
                
                // Validate value
                if ( ! in_array( $value, Truendo_Consent_API::get_consent_values(), true ) ) {
                    return;
                }
                
                $prefix = Truendo_Consent_API::get_cookie_prefix();
                $expiration = Truendo_Consent_API::get_cookie_expiration() * DAY_IN_SECONDS;
                
                // Set the cookie
                setcookie( 
                    "{$prefix}_{$category}", 
                    $value, 
                    time() + $expiration, 
                    '/',
                    '',
                    is_ssl(),
                    false
                );
                
                // Also set in $_COOKIE for immediate availability
                $_COOKIE["{$prefix}_{$category}"] = $value;
            }
        }
        
        if ( ! function_exists( 'wp_has_consent' ) ) {
            /**
             * Check if user has given consent for a specific category
             * 
             * @param string      $category     The consent category
             * @param string|bool $requested_by Optional plugin identifier
             * @return bool
             */
            function wp_has_consent( $category, $requested_by = false ) {
                // Validate category
                if ( ! in_array( $category, Truendo_Consent_API::get_consent_categories(), true ) ) {
                    return false;
                }
                
                $prefix = Truendo_Consent_API::get_cookie_prefix();
                $cookie_name = "{$prefix}_{$category}";
                
                // Get consent type (default to optin if not set)
                $consent_type = apply_filters( 'wp_get_consent_type', 'optin' );
                
                if ( ! $consent_type ) {
                    // No consent management, allow all
                    $has_consent = true;
                } elseif ( strpos( $consent_type, 'optout' ) !== false && ! isset( $_COOKIE[ $cookie_name ] ) ) {
                    // Opt-out mode and no cookie set means consent is granted
                    $has_consent = true;
                } elseif ( isset( $_COOKIE[ $cookie_name ] ) && 'allow' === $_COOKIE[ $cookie_name ] ) {
                    // Cookie is set to allow
                    $has_consent = true;
                } else {
                    $has_consent = false;
                }
                
                return apply_filters( 'wp_has_consent', $has_consent, $category, $requested_by );
            }
        }
        
        if ( ! function_exists( 'wp_get_consent_type' ) ) {
            /**
             * Get the active consent type
             * 
             * @return string|bool
             */
            function wp_get_consent_type() {
                // Default to 'optin' for Truendo
                return apply_filters( 'wp_get_consent_type', 'optin' );
            }
        }
        
        // Add filter to set consent type for Truendo
        add_filter( 'wp_get_consent_type', array( 'Truendo_Consent_API', 'set_consent_type' ), 10 );
        
        // Enqueue the consent API JavaScript if needed
        add_action( 'wp_enqueue_scripts', array( 'Truendo_Consent_API', 'enqueue_consent_api_script' ), PHP_INT_MAX - 100 );
    }
    
    /**
     * Get consent categories
     * @return array
     */
    public static function get_consent_categories() {
        return self::$consent_categories;
    }
    
    /**
     * Get consent values
     * @return array
     */
    public static function get_consent_values() {
        return self::$consent_values;
    }
    
    /**
     * Get cookie prefix
     * @return string
     */
    public static function get_cookie_prefix() {
        return apply_filters( 'wp_consent_api_cookie_prefix', self::$cookie_prefix );
    }
    
    /**
     * Get cookie expiration in days
     * @return int
     */
    public static function get_cookie_expiration() {
        return apply_filters( 'wp_consent_api_cookie_expiration', self::$cookie_expiration_days );
    }
    
    /**
     * Set the consent type for Truendo
     * 
     * @param string|bool $type Current consent type
     * @return string
     */
    public static function set_consent_type( $type ) {
        // Truendo uses opt-in model
        return 'optin';
    }
    
    /**
     * Enqueue the consent API JavaScript
     */
    public static function enqueue_consent_api_script() {
        // Only enqueue if Truendo is enabled and WP Consent is enabled
        if ( ! get_option( 'truendo_enabled' ) || ! get_option( 'truendo_wp_consent_enabled' ) ) {
            return;
        }
        
        // Check if the official WP Consent API script is already enqueued
        if ( wp_script_is( 'wp-consent-api', 'enqueued' ) ) {
            return;
        }
        
        // Enqueue our fallback script
        $plugin_url = plugin_dir_url( dirname( __FILE__ ) );
        wp_enqueue_script( 
            'truendo-consent-api', 
            $plugin_url . 'assets/js/truendo-consent-api.js', 
            array(), 
            TRUENDO_WORDPRESS_PLUGIN, 
            true 
        );
        
        // Localize script with necessary data
        wp_localize_script(
            'truendo-consent-api',
            'consent_api',
            array(
                'consent_type'         => 'optin',
                'waitfor_consent_hook' => false,
                'cookie_expiration'    => self::get_cookie_expiration(),
                'cookie_prefix'        => self::get_cookie_prefix(),
            )
        );
    }
}

// Initialize the consent API fallback
add_action( 'init', array( 'Truendo_Consent_API', 'init' ), 5 );