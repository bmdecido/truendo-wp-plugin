# WordPress Settings API Contract

**Feature**: Google Consent Mode v2 Integration
**Date**: 2025-09-15
**Type**: WordPress Settings API Specification

## Settings Registration Contract

### New Settings Schema
```php
// Contract: All settings must be registered in admin_init hook
add_action('admin_init', 'register_google_consent_mode_settings');

function register_google_consent_mode_settings() {
    // Contract: Master toggle setting
    register_setting('truendo_settings', 'truendo_google_consent_enabled', array(
        'type' => 'boolean',
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'description' => 'Enable Google Consent Mode v2 integration'
    ));

    // Contract: Default consent states setting
    register_setting('truendo_settings', 'truendo_google_consent_default_states', array(
        'type' => 'array',
        'default' => array(),
        'sanitize_callback' => 'truendo_sanitize_consent_states',
        'description' => 'Default consent states for Google categories'
    ));

    // Contract: Wait timeout setting
    register_setting('truendo_settings', 'truendo_google_consent_wait_time', array(
        'type' => 'integer',
        'default' => 500,
        'sanitize_callback' => 'truendo_sanitize_wait_time',
        'description' => 'Milliseconds to wait for user consent before applying defaults'
    ));
}
```

### Sanitization Callbacks Contract
```php
// Contract: Boolean sanitization must use WordPress core function
function truendo_sanitize_boolean($input) {
    return rest_sanitize_boolean($input);
}

// Contract: Consent states validation against whitelist
function truendo_sanitize_consent_states($input) {
    // MUST validate against Google Consent Mode v2 categories
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

// Contract: Wait time bounds validation
function truendo_sanitize_wait_time($input) {
    $wait_time = absint($input);

    // MUST enforce functional requirement bounds
    if ($wait_time < 500) {
        return 500;
    } elseif ($wait_time > 5000) {
        return 5000;
    }

    return $wait_time;
}
```

## Form Rendering Contract

### HTML Output Requirements
```php
// Contract: Main toggle checkbox
echo '<input type="checkbox" name="truendo_google_consent_enabled" value="1" ';
echo checked(1, get_option('truendo_google_consent_enabled', false), false);
echo ' class="truendo_google_consent_enabled" />';

// Contract: Consent category checkboxes (grouped)
$categories = array(
    'ad_storage' => __('Advertising Storage', 'truendo'),
    'ad_user_data' => __('Advertising User Data', 'truendo'),
    'ad_personalization' => __('Ad Personalization', 'truendo'),
    'analytics_storage' => __('Analytics Storage', 'truendo'),
    'preferences' => __('Preferences', 'truendo'),
    'social_content' => __('Social Content', 'truendo'),
    'social_sharing' => __('Social Sharing', 'truendo'),
    'personalization_storage' => __('Personalization Storage', 'truendo'),
    'functionality_storage' => __('Functionality Storage', 'truendo')
);

$default_states = get_option('truendo_google_consent_default_states', array());

foreach ($categories as $category_key => $category_label) {
    $current_state = isset($default_states[$category_key]) ? $default_states[$category_key] : 'denied';

    echo '<fieldset>';
    echo '<legend>' . esc_html($category_label) . '</legend>';

    // Granted radio button
    echo '<label>';
    echo '<input type="radio" name="truendo_google_consent_default_states[' . esc_attr($category_key) . ']" value="granted" ';
    echo checked('granted', $current_state, false);
    echo ' /> ' . __('Granted', 'truendo');
    echo '</label>';

    // Denied radio button
    echo '<label>';
    echo '<input type="radio" name="truendo_google_consent_default_states[' . esc_attr($category_key) . ']" value="denied" ';
    echo checked('denied', $current_state, false);
    echo ' /> ' . __('Denied', 'truendo');
    echo '</label>';

    echo '</fieldset>';
}

// Contract: Wait time numeric input
echo '<input type="number" name="truendo_google_consent_wait_time" ';
echo 'value="' . esc_attr(get_option('truendo_google_consent_wait_time', 500)) . '" ';
echo 'min="500" max="5000" step="1" />';
```

### Conditional Display Contract
```php
// Contract: Conditional fields must follow existing plugin pattern
<div class="truendo_show_when_active truendo_google_consent_fields <?php echo get_option('truendo_google_consent_enabled') ? 'active' : ''; ?>">
    <!-- Google Consent Mode fields only visible when toggle is enabled -->
</div>
```

## JavaScript Integration Contract

### jQuery Event Handling
```javascript
// Contract: Toggle event handling must follow existing pattern
$(document).ready(function() {
    $('.truendo_google_consent_enabled').change(function() {
        if (this.checked) {
            $('.truendo_google_consent_fields').addClass('active');
        } else {
            $('.truendo_google_consent_fields').removeClass('active');
        }
    });
});
```

### CSS Requirements
```css
/* Contract: Conditional display classes */
.truendo_google_consent_fields {
    display: none;
}
.truendo_google_consent_fields.active {
    display: block !important;
}
```

## Data Retrieval Contract

### Option Access Pattern
```php
// Contract: Standardized option retrieval with defaults
function truendo_get_google_consent_config() {
    return array(
        'enabled' => (bool) get_option('truendo_google_consent_enabled', false),
        'default_states' => get_option('truendo_google_consent_default_states', array()),
        'wait_time' => (int) get_option('truendo_google_consent_wait_time', 500)
    );
}

// Contract: Validation before use
function truendo_google_consent_mode_active() {
    $main_enabled = get_option('truendo_enabled');
    $consent_enabled = get_option('truendo_google_consent_enabled');
    $site_id = get_option('truendo_site_id');

    return $main_enabled && $consent_enabled && !empty($site_id);
}
```

## Security Requirements

### Input Validation
```php
// Contract: All user input MUST be sanitized
// Contract: All output MUST be escaped
// Contract: Capability checks MUST be enforced

function truendo_admin_save_google_consent_settings() {
    // MUST check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // MUST verify nonce
    if (!isset($_POST['truendo_nonce']) || !wp_verify_nonce($_POST['truendo_nonce'], 'truendo_admin_settings')) {
        wp_die(__('Security check failed.'));
    }

    // MUST use WordPress sanitization functions
    $enabled = isset($_POST['truendo_google_consent_enabled']) ?
               rest_sanitize_boolean($_POST['truendo_google_consent_enabled']) : false;

    // MUST validate complex data structures
    $default_states = isset($_POST['truendo_google_consent_default_states']) ?
                      truendo_sanitize_consent_states($_POST['truendo_google_consent_default_states']) : array();

    $wait_time = isset($_POST['truendo_google_consent_wait_time']) ?
                 truendo_sanitize_wait_time($_POST['truendo_google_consent_wait_time']) : 500;

    // MUST use WordPress update functions
    update_option('truendo_google_consent_enabled', $enabled);
    update_option('truendo_google_consent_default_states', $default_states);
    update_option('truendo_google_consent_wait_time', $wait_time);
}
```

## Backwards Compatibility Contract

### Existing Settings Preservation
```php
// Contract: MUST NOT modify existing settings structure
// Contract: MUST maintain existing option names
// Contract: MUST preserve existing functionality

// FORBIDDEN: Modifying these existing options
// - truendo_enabled
// - truendo_site_id
// - truendo_language
// - tru_stat_truendo_header_scripts_json
// - tru_mark_truendo_header_scripts_json

// ALLOWED: Adding new options with 'truendo_google_consent_' prefix
```

## Performance Contract

### Database Query Optimization
```php
// Contract: Minimize database queries
function truendo_get_all_consent_options() {
    // Single query for multiple related options
    $option_names = array(
        'truendo_enabled',
        'truendo_google_consent_enabled',
        'truendo_google_consent_default_states',
        'truendo_google_consent_wait_time',
        'truendo_site_id'
    );

    $options = array();
    foreach ($option_names as $name) {
        $options[$name] = get_option($name);
    }

    return $options;
}
```

## Error Handling Contract

### Graceful Degradation
```php
// Contract: Feature must degrade gracefully if dependencies missing
function truendo_render_google_consent_fields() {
    try {
        // MUST check dependencies before rendering
        if (!function_exists('get_option')) {
            throw new Exception('WordPress functions not available');
        }

        // MUST provide fallback for missing data
        $config = truendo_get_google_consent_config();
        if (empty($config)) {
            $config = truendo_get_default_consent_config();
        }

        // Render fields...

    } catch (Exception $e) {
        // MUST log errors appropriately
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('TRUENDO Google Consent Mode: ' . $e->getMessage());
        }

        // MUST provide user-friendly fallback
        echo '<p class="notice notice-warning">' .
             esc_html__('Google Consent Mode settings temporarily unavailable.', 'truendo') .
             '</p>';
    }
}
```

This contract defines the exact WordPress Settings API integration requirements for the Google Consent Mode v2 feature.