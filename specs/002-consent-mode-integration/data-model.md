# Data Model: Google Consent Mode v2 Integration

**Date**: 2025-09-15
**Status**: Complete
**Source**: Feature specification requirements and research findings

## Entity Definitions

### Consent Mode Configuration
**Description**: Main configuration entity for Google Consent Mode v2 feature
**Storage**: WordPress options table (`wp_options`)

#### Fields
| Field | Type | Constraints | Default | Description |
|-------|------|-------------|---------|-------------|
| `truendo_google_consent_enabled` | boolean | required | `false` | Master toggle for Google Consent Mode v2 |
| `truendo_google_consent_default_states` | array | optional | `{}` | Default consent states for each category |
| `truendo_google_consent_wait_time` | integer | min: 500, max: 5000 | `500` | Milliseconds to wait for user consent |

#### Validation Rules
```php
// Boolean validation
$enabled = rest_sanitize_boolean($input);

// Wait time validation
$wait_time = absint($input);
if ($wait_time < 500) $wait_time = 500;
if ($wait_time > 5000) $wait_time = 5000;

// Default states validation
$valid_categories = array(
    'ad_storage', 'ad_user_data', 'ad_personalization',
    'analytics_storage', 'preferences', 'social_content',
    'social_sharing', 'personalization_storage', 'functionality_storage'
);
$valid_states = array('granted', 'denied');
```

### Consent Category
**Description**: Individual consent type with granted/denied state
**Storage**: Part of `truendo_google_consent_default_states` array

#### Google Consent Mode v2 Categories
| Category | Purpose | Default State |
|----------|---------|---------------|
| `ad_storage` | Enables storage related to advertising | `denied` |
| `ad_user_data` | Consent for sending user data to Google for advertising | `denied` |
| `ad_personalization` | Consent for personalized advertising | `denied` |
| `analytics_storage` | Enables storage related to analytics | `denied` |
| `preferences` | Enables storage related to preferences | `granted` |
| `social_content` | Enables storage related to social content | `denied` |
| `social_sharing` | Enables storage related to social sharing | `denied` |
| `personalization_storage` | Enables storage related to personalization | `denied` |
| `functionality_storage` | Enables storage related to functionality | `granted` |

#### Data Structure
```php
// Example of default states storage
$default_states = array(
    'ad_storage' => 'denied',
    'ad_user_data' => 'denied',
    'ad_personalization' => 'denied',
    'analytics_storage' => 'denied',
    'preferences' => 'granted',
    'social_content' => 'denied',
    'social_sharing' => 'denied',
    'personalization_storage' => 'denied',
    'functionality_storage' => 'granted'
);
```

### Wait Timeout Setting
**Description**: Configuration for how long to wait for user consent before applying defaults
**Storage**: `truendo_google_consent_wait_time` option

#### Specifications
- **Type**: Positive integer
- **Unit**: Milliseconds
- **Range**: 500ms to 5000ms (0.5 to 5 seconds)
- **Default**: 500ms
- **Business Logic**: If user doesn't interact with consent banner within this time, default states apply

## Database Schema Impact

### WordPress Options Table
New options to be added to `wp_options`:

```sql
-- New options that will be created
INSERT INTO wp_options (option_name, option_value, autoload) VALUES
('truendo_google_consent_enabled', '0', 'yes'),
('truendo_google_consent_default_states', '', 'yes'),
('truendo_google_consent_wait_time', '500', 'yes');
```

### Option Naming Convention
Following existing plugin pattern:
- Prefix: `truendo_`
- Feature identifier: `google_consent_`
- Setting name: descriptive lowercase with underscores

### Autoload Strategy
All Google Consent Mode options set to `autoload = 'yes'` because:
- Small data size (few hundred bytes total)
- Needed on every frontend page load
- Follows existing plugin pattern

## Data Relationships

### Dependency Chain
```
truendo_enabled (existing)
└── truendo_google_consent_enabled (new)
    ├── truendo_google_consent_default_states (new)
    └── truendo_google_consent_wait_time (new)
```

**Business Rules**:
1. Google Consent Mode can only be enabled if main TRUENDO plugin is enabled
2. Default states and wait time only apply when Google Consent Mode is enabled
3. If no default states configured, all categories default to 'denied'

### Integration with Existing Data
```php
// Data access pattern
function truendo_google_consent_mode_active() {
    return get_option('truendo_enabled') &&
           get_option('truendo_google_consent_enabled') &&
           !empty(get_option('truendo_site_id'));
}
```

## Data Validation Layer

### Input Sanitization
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
            $clean_category = sanitize_text_field($category);
            $clean_state = sanitize_text_field($state);

            if (in_array($clean_category, $valid_categories) &&
                in_array($clean_state, array('granted', 'denied'))) {
                $sanitized[$clean_category] = $clean_state;
            }
        }
    }

    return $sanitized;
}

function truendo_sanitize_wait_time($input) {
    $wait_time = absint($input);

    // Enforce bounds from functional requirements
    if ($wait_time < 500) {
        $wait_time = 500;
    } elseif ($wait_time > 5000) {
        $wait_time = 5000;
    }

    return $wait_time;
}
```

### Data Integrity Checks
```php
function truendo_validate_consent_configuration() {
    $errors = array();

    // Check if main plugin is enabled
    if (!get_option('truendo_enabled')) {
        $errors[] = 'TRUENDO plugin must be enabled';
    }

    // Check if site ID is configured
    if (empty(get_option('truendo_site_id'))) {
        $errors[] = 'TRUENDO site ID must be configured';
    }

    // Validate wait time
    $wait_time = get_option('truendo_google_consent_wait_time', 500);
    if ($wait_time < 500 || $wait_time > 5000) {
        $errors[] = 'Wait time must be between 500 and 5000 milliseconds';
    }

    return $errors;
}
```

## Migration Strategy

### New Installation
Default values automatically applied via `register_setting()` defaults:
```php
register_setting('truendo_settings', 'truendo_google_consent_enabled', array(
    'type' => 'boolean',
    'default' => false
));
```

### Existing Installation Upgrade
No migration required because:
- New options start with safe defaults (disabled state)
- No existing data structure changes
- Backward compatibility maintained

### Fallback Behavior
If consent mode script is enabled but no default states configured:
```php
$default_states = get_option('truendo_google_consent_default_states', array());
if (empty($default_states)) {
    // Fallback: all categories denied (most restrictive)
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
```

## Performance Considerations

### Database Impact
- **Additional queries**: +3 `get_option()` calls per page load
- **Cache friendly**: WordPress object cache handles option caching
- **Data size**: ~500 bytes total for all new options

### Optimization Strategy
```php
// Single query to get all consent mode options
$consent_options = array(
    'enabled' => get_option('truendo_google_consent_enabled', false),
    'states' => get_option('truendo_google_consent_default_states', array()),
    'wait_time' => get_option('truendo_google_consent_wait_time', 500)
);
```

This data model ensures type safety, validation, and integration with existing WordPress and TRUENDO plugin patterns.