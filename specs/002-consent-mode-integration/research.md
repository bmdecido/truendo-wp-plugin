# Research: Google Consent Mode v2 Integration

**Date**: 2025-09-15
**Status**: Complete
**Source**: Analysis of existing TRUENDO WordPress plugin architecture

## WordPress Settings API Approach

### Decision: Use WordPress Settings API with Custom Sanitization
**Rationale**: Existing plugin uses WordPress Settings API exclusively. Following established patterns ensures consistency and leverages WordPress security features.
**Alternatives considered**:
- Custom option handling (rejected - doesn't follow WordPress standards)
- Direct database manipulation (rejected - bypasses WordPress validation)

### Implementation Pattern
```php
// Registration pattern to follow
register_setting('truendo_settings', 'truendo_google_consent_enabled', array(
    'type' => 'boolean',
    'default' => false,
    'sanitize_callback' => 'rest_sanitize_boolean'
));

register_setting('truendo_settings', 'truendo_google_consent_default_states', array(
    'type' => 'array',
    'default' => array(),
    'sanitize_callback' => 'truendo_sanitize_consent_states'
));
```

## Admin Interface Design

### Decision: Follow Existing CSS/JS Conditional Display Pattern
**Rationale**: Plugin already implements conditional field visibility using CSS classes and jQuery. Reusing this pattern maintains UI consistency.
**Alternatives considered**:
- React/Vue components (rejected - adds unnecessary complexity)
- Server-side conditional rendering (rejected - worse UX)

### Existing Pattern Analysis
From `admin/css/truendo-admin.css`:
```css
.truendo_show_when_active {
    display: none;
}
.truendo_show_when_active.active {
    display: block !important;
}
```

From `admin/js/truendo-admin.js`:
```javascript
$('.truendo_enabled').change(function () {
    if (this.checked) {
        $('.truendo_show_when_active').addClass('active');
    } else {
        $('.truendo_show_when_active').removeClass('active');
    }
});
```

## Script Injection Strategy

### Decision: Mirror Existing TRUENDO CMP Script Injection Pattern
**Rationale**: Plugin uses dual injection points (admin and public classes) with page builder compatibility checks. Google Consent Mode script should follow identical pattern for consistency.
**Alternatives considered**:
- Single injection point (rejected - doesn't match existing architecture)
- WordPress wp_enqueue_script (rejected - current plugin uses direct HTML injection)

### Current TRUENDO Script Injection Pattern
```php
// Pattern from admin/class-truendo-admin.php
public function add_truendo_script() {
    if ($this->truendo_check_page_builder()) {
        if (get_option('truendo_enabled')) {
            if (get_option('truendo_site_id') != '') {
                // Script injection code
            }
        }
    }
}
```

### Required Adaptation for Google Consent Mode
```php
// New method to add alongside existing TRUENDO script injection
public function add_google_consent_mode_script() {
    if ($this->truendo_check_page_builder()) {
        if (get_option('truendo_enabled') && get_option('truendo_google_consent_enabled')) {
            // Google Consent Mode script injection with default states
        }
    }
}
```

## Consent Categories Configuration

### Decision: Use Array-based Storage with Grouped Checkboxes
**Rationale**: WordPress Settings API handles arrays well, and existing plugin has patterns for complex configuration storage.
**Alternatives considered**:
- Individual boolean options for each category (rejected - creates option table bloat)
- JSON string storage (rejected - harder to validate and sanitize)

### Required Consent Categories
Based on Google Consent Mode v2 specification:
- `ad_storage`: Enables storage related to advertising
- `ad_user_data`: Sets consent for sending user data to Google for advertising purposes
- `ad_personalization`: Sets consent for personalized advertising
- `analytics_storage`: Enables storage related to analytics
- `preferences`: Enables storage related to preferences
- `social_content`: Enables storage related to social content
- `social_sharing`: Enables storage related to social sharing
- `personalization_storage`: Enables storage related to personalization
- `functionality_storage`: Enables storage related to functionality

## Input Validation Requirements

### Decision: Multi-layer Validation (Client-side + Server-side + WordPress Sanitization)
**Rationale**: Follows WordPress security best practices and existing plugin patterns.

### Validation Rules
1. **Google Consent Mode Toggle**: Boolean validation via `rest_sanitize_boolean`
2. **Consent Categories**: Array validation against whitelist of valid Google categories
3. **Wait Time**: Integer validation with min/max bounds (500-5000 seconds)

```php
function truendo_sanitize_consent_states($input) {
    $valid_categories = array(
        'ad_storage', 'ad_user_data', 'ad_personalization',
        'analytics_storage', 'preferences', 'social_content',
        'social_sharing', 'personalization_storage', 'functionality_storage'
    );

    $sanitized = array();
    if (is_array($input)) {
        foreach ($input as $category => $state) {
            if (in_array($category, $valid_categories) && in_array($state, array('granted', 'denied'))) {
                $sanitized[sanitize_text_field($category)] = sanitize_text_field($state);
            }
        }
    }
    return $sanitized;
}
```

## Page Builder Compatibility

### Decision: Reuse Existing Page Builder Detection Logic
**Rationale**: Plugin already handles Breakdance, Divi, and Oxygen page builders. Google Consent Mode script should follow same compatibility checks.

### Existing Detection Method
```php
public function truendo_check_page_builder() {
    $queries = ["?breakdance", "&breakdance", '?et_fb', '&et_fb', '?ct_builder', '&ct_builder'];
    $isOkay = true;
    foreach ($queries as $s) {
        if (str_contains($_SERVER['REQUEST_URI'], $s)) {
            $isOkay = false;
        }
    }
    return $isOkay;
}
```

## Hook Integration Strategy

### Decision: Follow Existing Hook Registration Pattern
**Rationale**: Plugin uses centralized hook loader class. New Google Consent Mode hooks should integrate into this system.

### Required New Hooks
1. `add_action('wp_head', $plugin_admin, 'add_google_consent_mode_script', 5)` - Inject before TRUENDO CMP script
2. `add_action('admin_init', $plugin_admin, 'register_google_consent_mode_settings')` - Register new settings

## Technical Dependencies

### Decision: No Additional Dependencies Required
**Rationale**: Google Consent Mode v2 integration can be implemented using existing WordPress APIs and JavaScript patterns already in use.

### Existing Dependencies (Sufficient)
- WordPress Core (Settings API, Options API, Admin functions)
- jQuery (already enqueued by existing admin interface)
- Existing TRUENDO plugin architecture and classes

## Performance Considerations

### Decision: Minimal Additional Overhead
**Rationale**: Following existing patterns ensures performance impact is negligible.

### Performance Goals
- Add 1-2 additional database option reads (minimal impact)
- JavaScript injection should be <1KB additional code
- No additional HTTP requests required (script will be inline)
- Leverage existing page builder compatibility checks

## Security Analysis

### Decision: Follow WordPress Security Standards
**Rationale**: Existing plugin follows WordPress security practices. Google Consent Mode implementation must maintain same standards.

### Security Requirements
1. All user input sanitized via WordPress functions
2. All output escaped via `esc_attr()`, `esc_html()`, etc.
3. Nonce verification for admin form submissions (following existing pattern)
4. Capability checks for admin access (following existing `manage_options` pattern)

## Unknowns Resolved

All technical approaches have been researched and documented. No [NEEDS CLARIFICATION] markers remain:

- ✅ WordPress Settings API implementation pattern determined
- ✅ Script injection strategy defined (mirror existing TRUENDO pattern)
- ✅ Admin interface approach selected (reuse CSS/JS patterns)
- ✅ Validation and security approach documented
- ✅ Integration points with existing plugin identified
- ✅ Performance and compatibility requirements addressed

## Implementation Readiness

All research complete. Ready to proceed to Phase 1 (Design & Contracts).