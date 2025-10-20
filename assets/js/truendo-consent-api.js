/**
 * Truendo Consent API JavaScript
 * Fallback implementation for WordPress Consent API functionality
 * 
 * @package Truendo
 * @since   2.5.0
 */

(function() {
    'use strict';
    
    // Initialize consent API object if it doesn't exist
    window.consent_api = window.consent_api || {};
    
    // Set default values if not already set
    consent_api.consent_type = consent_api.consent_type || 'optin';
    consent_api.cookie_prefix = consent_api.cookie_prefix || 'wp_consent';
    consent_api.cookie_expiration = consent_api.cookie_expiration || 365;
    consent_api.waitfor_consent_hook = consent_api.waitfor_consent_hook || false;
    
    // Define consent categories
    var consentCategories = [
        'functional',
        'preferences',
        'statistics',
        'statistics-anonymous', 
        'marketing'
    ];
    
    // Cookie management functions
    var cookieManager = {
        /**
         * Set a cookie
         */
        set: function(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
        },
        
        /**
         * Get a cookie value
         */
        get: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    };
    
    /**
     * Set consent for a category
     * @param {string} category - The consent category
     * @param {string} value - 'allow' or 'deny'
     */
    window.wp_set_consent = window.wp_set_consent || function(category, value) {
        if (consentCategories.indexOf(category) === -1) {
            console.warn('Invalid consent category:', category);
            return;
        }
        
        if (value !== 'allow' && value !== 'deny') {
            console.warn('Invalid consent value:', value);
            return;
        }
        
        var cookieName = consent_api.cookie_prefix + '_' + category;
        cookieManager.set(cookieName, value, consent_api.cookie_expiration);
        
        // Fire event for other plugins to listen to
        var event = new CustomEvent('wp_set_consent', {
            detail: {
                category: category,
                value: value
            }
        });
        document.dispatchEvent(event);
    };
    
    /**
     * Check if consent is given for a category
     * @param {string} category - The consent category
     * @returns {boolean}
     */
    window.wp_has_consent = window.wp_has_consent || function(category) {
        if (consentCategories.indexOf(category) === -1) {
            console.warn('Invalid consent category:', category);
            return false;
        }
        
        var cookieName = consent_api.cookie_prefix + '_' + category;
        var cookieValue = cookieManager.get(cookieName);
        
        // Handle based on consent type
        if (!consent_api.consent_type) {
            // No consent management, allow all
            return true;
        } else if (consent_api.consent_type.indexOf('optout') !== -1 && !cookieValue) {
            // Opt-out mode and no cookie means consent granted
            return true;
        } else if (cookieValue === 'allow') {
            // Cookie explicitly allows
            return true;
        }
        
        return false;
    };
    
    /**
     * Fire event to notify that consent type is defined
     */
    if (!consent_api.waitfor_consent_hook) {
        var event = new CustomEvent('wp_consent_type_defined');
        document.dispatchEvent(event);
    }
    
    /**
     * Handle consent changes
     * Other plugins can hook into this
     */
    document.addEventListener('wp_set_consent', function(e) {
        var category = e.detail.category;
        var value = e.detail.value;
        
        // Fire category-specific event
        var categoryEvent = new CustomEvent('wp_consent_' + category, {
            detail: {
                value: value
            }
        });
        document.dispatchEvent(categoryEvent);
        
        // Fire general consent change event
        var changeEvent = new CustomEvent('wp_consent_change', {
            detail: {
                category: category,
                value: value
            }
        });
        document.dispatchEvent(changeEvent);
    });
    
})();