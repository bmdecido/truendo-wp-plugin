# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin for TRUENDO, a GDPR-compliant cookie consent management platform. The plugin integrates the TRUENDO privacy panel into WordPress websites by injecting the required JavaScript and providing admin configuration options.

## Development Commands

This is a pure PHP WordPress plugin with no build system or package manager. Development involves:

- **Testing**: Install in a local WordPress environment via wp-admin or copy to `/wp-content/plugins/truendo/`
- **WordPress Standards**: Follow WordPress Coding Standards for PHP
- **No Build Process**: Direct PHP file editing, no compilation needed

## Architecture Overview

### Core Structure
- **Main Plugin File**: `truendo.php` - Entry point, defines plugin metadata and initializes the main class
- **Core Class**: `includes/class-truendo.php` - Central orchestrator that loads dependencies and defines hooks
- **Hook Loader**: `includes/class-truendo-loader.php` - Manages WordPress action/filter registration
- **Admin Functionality**: `admin/class-truendo-admin.php` - Handles admin interface, settings, and script injection
- **Public Functionality**: `public/class-truendo-public.php` - Manages frontend script integration

### Key Components

1. **Plugin Architecture Pattern**: Follows WordPress plugin boilerplate structure with separation of concerns
2. **Settings Management**: Uses WordPress Settings API to store configuration (site ID, enabled status, language)
3. **Script Injection**: Dynamically injects TRUENDO's JavaScript based on configuration and page builder compatibility
4. **Cookie Blocking Rules**: `rules.json` defines auto-blocking patterns for social media and analytics scripts

### Important Files

- `admin/partials/truendo-admin-display.php` - Admin settings page template
- `rules.json` - Cookie/script blocking rules configuration
- `uninstall.php` - Cleanup logic when plugin is deleted
- `includes/truendo_contactform.php` - Contact form integration helper

### Settings Stored in WordPress Options

#### Core TRUENDO Settings
- `truendo_enabled` - Boolean to enable/disable the plugin
- `truendo_site_id` - TRUENDO account site identifier (required)
- `truendo_language` - Language setting (defaults to 'auto')
- `tru_stat_truendo_header_scripts_json` - Statistics scripts configuration
- `tru_mark_truendo_header_scripts_json` - Marketing scripts configuration

#### Google Consent Mode v2 Settings
- `truendo_google_consent_enabled` - Boolean to enable/disable Google Consent Mode v2
- `truendo_google_consent_default_states` - Array of default consent states for Google categories
- `truendo_google_consent_wait_time` - Integer (500-5000ms) wait time before applying defaults

### Google Consent Mode v2 Integration

The plugin includes Google Consent Mode v2 support that:

1. **Script Injection**: Injects Google Consent Mode script before TRUENDO CMP script (priority 5 vs 10)
2. **Default States**: Configures default consent states for 9 Google categories (ad_storage, analytics_storage, etc.)
3. **Wait Time**: Configurable timeout (500-5000ms) before applying defaults
4. **Integration**: Interfaces with TRUENDO CMP for real-time consent updates
5. **Admin Interface**: Conditional form fields with toggle and grouped category controls

#### Google Consent Categories
- `ad_storage` - Advertising storage permissions
- `ad_user_data` - User data for advertising
- `ad_personalization` - Personalized advertising
- `analytics_storage` - Analytics data storage
- `preferences` - User interface preferences
- `social_content` - Social media content embedding
- `social_sharing` - Social media sharing features
- `personalization_storage` - Content personalization storage
- `functionality_storage` - Essential functionality data

#### Wordpress Consent API Categories
- `statistics` - Cookies or any other form of local storage that are used exclusively for statistical purposes (Analytics Cookies).
- `statistics-anonymous` - Cookies or any other form of local storage that are used exclusively for anonymous statistical purposes (Anonymous Analytics Cookies), that are placed on a first party domain, and that do not allow identification of particular individuals.
- `marketing` - Cookies or any other form of local storage required to create user profiles to send advertising or to track the user on a website or across websites for similar marketing purposes.
- `functional` - The cookie or any other form of local storage is used for the sole purpose of carrying out the transmission of a communication over an electronic communications network OR The technical storage or access is strictly necessary for the legitimate purpose of enabling the use of a specific service explicitly requested by the subscriber or user. If cookies are disabled, the requested functionality will not be available. This makes them essential functional cookies.
- `preferences` - UsCookies or any other form of local storage that can not be seen as statistics, statistics-anonymous, marketing or functional, and where the technical storage or access is necessary for the legitimate purpose of storing preferences.


#### Implementation Patterns
- **Settings**: Follow existing WordPress Settings API pattern with custom sanitization
- **Admin UI**: Use existing CSS/JS conditional display pattern (.truendo_show_when_active)
- **Script Injection**: Mirror existing TRUENDO script injection pattern in both admin and public classes
- **Validation**: Multi-layer validation (client-side, server-side, WordPress sanitization)
- **Security**: All output escaped, input sanitized, nonce verification for admin forms

### Page Builder Compatibility

The plugin includes checks for popular page builders (Breakdance, Divi, Oxygen) and conditionally loads scripts to avoid conflicts during editing mode.