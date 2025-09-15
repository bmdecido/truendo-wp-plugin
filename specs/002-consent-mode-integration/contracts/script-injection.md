# Script Injection Contract

**Feature**: Google Consent Mode v2 Integration
**Date**: 2025-09-15
**Type**: Frontend Script Injection Specification

## Script Injection Requirements

### Loading Order Contract
```
1. Google Consent Mode script (NEW) - wp_head priority 5
2. TRUENDO CMP script (EXISTING) - wp_head priority 10
3. Other tracking scripts - wp_head priority 15+
```

**Rationale**: Google Consent Mode must load before TRUENDO CMP to establish default consent states before any tracking occurs.

## Script Injection Pattern Contract

### Admin Class Injection
```php
// Contract: Must follow existing TRUENDO script pattern in admin/class-truendo-admin.php
class Truendo_Admin {

    public function add_google_consent_mode_script() {
        // MUST check page builder compatibility
        if (!$this->truendo_check_page_builder()) {
            return;
        }

        // MUST verify all dependencies enabled
        if (!$this->is_google_consent_mode_active()) {
            return;
        }

        // MUST inject inline script with configuration
        $config = $this->get_consent_mode_config();
        $this->output_consent_mode_script($config);
    }

    private function is_google_consent_mode_active() {
        return get_option('truendo_enabled') &&
               get_option('truendo_google_consent_enabled') &&
               !empty(get_option('truendo_site_id'));
    }

    private function get_consent_mode_config() {
        $default_states = get_option('truendo_google_consent_default_states', array());

        // MUST provide fallback if no states configured
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

    private function output_consent_mode_script($config) {
        ?>
        <script>
        // Google Consent Mode v2 Configuration
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        // Set default consent states
        gtag('consent', 'default', <?php echo wp_json_encode($config['default_states']); ?>);

        // Wait for consent configuration
        gtag('config', 'consent_wait_time', <?php echo (int) $config['wait_time']; ?>);
        </script>
        <?php
    }
}
```

### Public Class Injection
```php
// Contract: Must mirror pattern in public/class-truendo-public.php
class Truendo_Public {

    public function add_google_consent_mode_script() {
        // MUST use same validation pattern
        if (!$this->truendo_check_page_builder()) {
            return;
        }

        if (!$this->is_google_consent_mode_active()) {
            return;
        }

        // MUST output same script structure
        $config = $this->get_consent_mode_config();
        echo $this->build_consent_mode_script_html($config);
    }

    private function is_google_consent_mode_active() {
        return get_option('truendo_enabled') &&
               get_option('truendo_google_consent_enabled') &&
               !empty(get_option('truendo_site_id'));
    }

    private function build_consent_mode_script_html($config) {
        $script = '<script>';
        $script .= 'window.dataLayer = window.dataLayer || [];';
        $script .= 'function gtag(){dataLayer.push(arguments);}';
        $script .= "gtag('consent', 'default', " . wp_json_encode($config['default_states']) . ");";
        $script .= "gtag('config', 'consent_wait_time', " . (int) $config['wait_time'] . ");";
        $script .= '</script>';

        return $script;
    }
}
```

## Page Builder Compatibility Contract

### Required Compatibility Check
```php
// Contract: MUST use existing page builder detection logic
public function truendo_check_page_builder() {
    // MUST check same page builders as existing TRUENDO script
    $queries = [
        "?breakdance", "&breakdance",  // Breakdance
        '?et_fb', '&et_fb',            // Divi
        '?ct_builder', '&ct_builder'   // Oxygen
    ];

    $isOkay = true;
    foreach ($queries as $query_param) {
        if (str_contains($_SERVER['REQUEST_URI'], $query_param)) {
            $isOkay = false;
            break;
        }
    }

    return $isOkay;
}
```

## Hook Registration Contract

### WordPress Hook Integration
```php
// Contract: Must integrate into existing hook loader pattern
// File: includes/class-truendo.php

private function truendo_define_admin_hooks() {
    $plugin_admin = new Truendo_Admin($this->truendo_get_plugin_name(), $this->truendo_get_version());

    // EXISTING hooks (preserve)
    $this->loader->add_action('admin_menu', $plugin_admin, 'truendo_admin_display_admin_page');
    $this->loader->add_action('admin_init', $plugin_admin, 'truendo_admin_add_settings');
    $this->loader->add_action('wp_head', $plugin_admin, 'add_truendo_script', 10);

    // NEW hook for Google Consent Mode (higher priority = earlier execution)
    $this->loader->add_action('wp_head', $plugin_admin, 'add_google_consent_mode_script', 5);
}

private function truendo_define_public_hooks() {
    $plugin_public = new Truendo_Public($this->truendo_get_plugin_name(), $this->truendo_get_version());

    // EXISTING hooks (preserve)
    $this->loader->add_action('wp_head', $plugin_public, 'add_truendo_script', 10);

    // NEW hook for Google Consent Mode (higher priority = earlier execution)
    $this->loader->add_action('wp_head', $plugin_public, 'add_google_consent_mode_script', 5);
}
```

## Script Content Contract

### Google Consent Mode v2 Script Structure
```javascript
// Contract: Must output valid Google Consent Mode v2 initialization
<script>
// Initialize dataLayer if not exists
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

// Set default consent states (MUST be first gtag call)
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'preferences': 'granted',
    'social_content': 'denied',
    'social_sharing': 'denied',
    'personalization_storage': 'denied',
    'functionality_storage': 'granted'
});

// Configure consent wait time
gtag('config', 'consent_wait_time', 500);
</script>
```

### Dynamic Configuration Injection
```php
// Contract: Configuration must be dynamically generated from WordPress options
function truendo_build_consent_config_js($config) {
    $js_config = array();

    // MUST validate each category before output
    $valid_categories = array(
        'ad_storage', 'ad_user_data', 'ad_personalization',
        'analytics_storage', 'preferences', 'social_content',
        'social_sharing', 'personalization_storage', 'functionality_storage'
    );

    foreach ($config['default_states'] as $category => $state) {
        if (in_array($category, $valid_categories) && in_array($state, array('granted', 'denied'))) {
            $js_config[$category] = $state;
        }
    }

    // MUST ensure all required categories are present
    foreach ($valid_categories as $category) {
        if (!isset($js_config[$category])) {
            $js_config[$category] = 'denied'; // Safe default
        }
    }

    return $js_config;
}
```

## Integration with TRUENDO CMP Script

### Callback Interface Contract
```javascript
// Contract: Google Consent Mode script must provide callback interface for TRUENDO CMP
// This will be handled by the external consent mode script (to be provided later)
// The WordPress plugin only needs to:
// 1. Inject Google Consent Mode script with defaults
// 2. Ensure it loads before TRUENDO CMP script
// 3. Pass configuration parameters
```

### Script Loading Sequence Validation
```php
// Contract: Must ensure proper loading order
function truendo_validate_script_order() {
    // This function would be used in tests to verify:
    // 1. Google Consent Mode script loads first (priority 5)
    // 2. TRUENDO CMP script loads second (priority 10)
    // 3. Both scripts only load when conditions met
}
```

## Error Handling Contract

### Graceful Degradation
```php
// Contract: Script injection must handle errors gracefully
public function add_google_consent_mode_script() {
    try {
        if (!$this->truendo_check_page_builder()) {
            return;
        }

        if (!$this->is_google_consent_mode_active()) {
            return;
        }

        $config = $this->get_consent_mode_config();

        // MUST validate config before output
        if (empty($config) || !is_array($config)) {
            // Log error but don't break page
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('TRUENDO: Invalid Google Consent Mode configuration');
            }
            return;
        }

        $this->output_consent_mode_script($config);

    } catch (Exception $e) {
        // MUST NOT break page rendering
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('TRUENDO Google Consent Mode injection error: ' . $e->getMessage());
        }
    }
}
```

### Fallback Configuration
```php
// Contract: Must provide safe fallback if configuration invalid
private function get_safe_fallback_config() {
    return array(
        'default_states' => array(
            'ad_storage' => 'denied',
            'ad_user_data' => 'denied',
            'ad_personalization' => 'denied',
            'analytics_storage' => 'denied',
            'preferences' => 'denied',
            'social_content' => 'denied',
            'social_sharing' => 'denied',
            'personalization_storage' => 'denied',
            'functionality_storage' => 'denied'
        ),
        'wait_time' => 500
    );
}
```

## Security Contract

### XSS Prevention
```php
// Contract: All dynamic content must be properly escaped
private function output_consent_mode_script($config) {
    // MUST use wp_json_encode for JavaScript data
    // MUST validate integer values
    // MUST NOT output user-controlled strings directly

    $safe_states = array();
    foreach ($config['default_states'] as $key => $value) {
        $safe_states[sanitize_key($key)] = sanitize_text_field($value);
    }

    $safe_wait_time = absint($config['wait_time']);

    ?>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', <?php echo wp_json_encode($safe_states); ?>);
    gtag('config', 'consent_wait_time', <?php echo $safe_wait_time; ?>);
    </script>
    <?php
}
```

## Testing Contract

### Required Test Coverage
```php
// Contract: Must test all injection conditions
class Test_Script_Injection extends WP_UnitTestCase {

    public function test_script_only_loads_when_enabled() {
        // Test: Script doesn't load when main plugin disabled
        // Test: Script doesn't load when consent mode disabled
        // Test: Script doesn't load when site ID missing
        // Test: Script loads when all conditions met
    }

    public function test_page_builder_compatibility() {
        // Test: Script doesn't load in Breakdance editor
        // Test: Script doesn't load in Divi editor
        // Test: Script doesn't load in Oxygen editor
        // Test: Script loads on normal pages
    }

    public function test_script_content_validity() {
        // Test: Valid JavaScript syntax
        // Test: Valid Google Consent Mode structure
        // Test: All categories present with valid states
        // Test: Wait time within bounds
    }

    public function test_loading_order() {
        // Test: Google Consent Mode script loads before TRUENDO CMP
        // Test: Priority 5 vs priority 10 execution order
    }
}
```

This contract ensures the Google Consent Mode script injection follows existing WordPress plugin patterns while meeting Google Consent Mode v2 requirements.