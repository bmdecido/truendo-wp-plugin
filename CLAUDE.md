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

- `truendo_enabled` - Boolean to enable/disable the plugin
- `truendo_site_id` - TRUENDO account site identifier (required)
- `truendo_language` - Language setting (defaults to 'auto')
- `tru_stat_truendo_header_scripts_json` - Statistics scripts configuration
- `tru_mark_truendo_header_scripts_json` - Marketing scripts configuration

### Page Builder Compatibility

The plugin includes checks for popular page builders (Breakdance, Divi, Oxygen) and conditionally loads scripts to avoid conflicts during editing mode.