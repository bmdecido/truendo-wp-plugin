# Tasks: Google Consent Mode v2 Integration

**Input**: Design documents from `/specs/002-consent-mode-integration/`
**Prerequisites**: plan.md, research.md, data-model.md, contracts/
**Context**: MVP implementation for local browser testing (no testing tasks included per user request)

## Implementation Overview

This MVP focuses on implementing the core Google Consent Mode v2 functionality following the existing TRUENDO WordPress plugin patterns. The implementation extends the current plugin architecture with:
- 3 new WordPress settings (toggle, default states, wait time)
- Admin interface extensions with conditional form fields
- Frontend script injection before TRUENDO CMP script
- Sanitization and validation callbacks

## Format: `[ID] [P?] Description`
- **[P]**: Can run in parallel (different files, no dependencies)
- File paths are absolute from repository root

## Phase 3.1: Settings Foundation

### T001 Add Google Consent Mode settings registration
**File**: `admin/class-truendo-admin.php` (modify existing `truendo_admin_add_settings` method)
- Add 3 new `register_setting()` calls for Google Consent Mode options
- Follow existing pattern with `truendo_settings` group
- Include: `truendo_google_consent_enabled`, `truendo_google_consent_default_states`, `truendo_google_consent_wait_time`

### T002 [P] Create sanitization callbacks for Google Consent Mode settings
**File**: `admin/class-truendo-admin.php` (add new methods)
- Add `truendo_sanitize_consent_states($input)` method
- Add `truendo_sanitize_wait_time($input)` method
- Implement validation rules per data-model.md specifications

## Phase 3.2: Admin Interface Extensions

### T003 Add Google Consent Mode admin form fields
**File**: `admin/partials/truendo-admin-display.php` (extend existing form)
- Add master toggle checkbox after existing TRUENDO enable checkbox
- Add conditional div with `truendo_google_consent_fields` class
- Follow existing `.truendo_show_when_active` pattern for conditional display

### T004 [P] Create consent categories form fields
**File**: `admin/partials/truendo-admin-display.php` (within conditional div)
- Add radio button groups for 9 Google consent categories
- Each category gets granted/denied radio options
- Use category labels from data-model.md
- Include wait time numeric input field (min: 500, max: 5000)

### T005 [P] Extend admin JavaScript for conditional display
**File**: `admin/js/truendo-admin.js` (add event handler)
- Add change event handler for `.truendo_google_consent_enabled` checkbox
- Toggle `.truendo_google_consent_fields.active` class
- Mirror existing toggle behavior pattern

### T006 [P] Add CSS for Google Consent Mode fields
**File**: `admin/css/truendo-admin.css` (extend existing styles)
- Add `.truendo_google_consent_fields` styling
- Follow existing conditional display pattern
- Ensure proper spacing and layout for consent categories

## Phase 3.3: Script Injection Implementation

### T007 Add Google Consent Mode script injection to Admin class
**File**: `admin/class-truendo-admin.php` (add new method)
- Create `add_google_consent_mode_script()` method
- Follow exact pattern from existing `add_truendo_script()` method
- Include page builder compatibility check
- Add validation for enabled state and configuration

### T008 [P] Add Google Consent Mode script injection to Public class
**File**: `public/class-truendo-public.php` (add new method)
- Create `add_google_consent_mode_script()` method
- Mirror admin class implementation
- Output inline JavaScript for Google Consent Mode initialization
- Include default consent states and wait time configuration

### T009 Add helper methods for script injection
**File**: `admin/class-truendo-admin.php` and `public/class-truendo-public.php` (add supporting methods)
- Create `is_google_consent_mode_active()` validation method
- Create `get_consent_mode_config()` configuration getter
- Implement fallback logic when no default states configured

## Phase 3.4: Hook Integration

### T010 Register Google Consent Mode script hooks
**File**: `includes/class-truendo.php` (modify hook registration methods)
- Add `add_google_consent_mode_script` hook to `truendo_define_admin_hooks()` with priority 5
- Add `add_google_consent_mode_script` hook to `truendo_define_public_hooks()` with priority 5
- Ensure hooks fire before existing TRUENDO script hooks (priority 10)

## Phase 3.5: Final Integration & Polish

### T011 Add Google Consent Mode configuration getter utility
**File**: `admin/class-truendo-admin.php` (add utility methods)
- Create `truendo_get_google_consent_config()` static utility method
- Implement safe option retrieval with defaults
- Add configuration validation logic

### T012 Verify script loading order and functionality
**Manual verification step** (no specific file)
- Load WordPress admin and configure Google Consent Mode settings
- Verify conditional form fields show/hide correctly
- Check frontend script injection order (Consent Mode before TRUENDO CMP)
- Test different consent category configurations

## Dependencies
- T001 must complete before T003 (settings must exist before admin form)
- T003 must complete before T005 (form fields must exist before JavaScript)
- T007-T008 must complete before T010 (methods must exist before hooks)
- T002 runs parallel with T001 (different parts of same file)
- T004-T006 can run in parallel (different files)
- T007-T008 can run in parallel (different files)

## MVP Validation Checklist
After completing all tasks, verify:
- [ ] Google Consent Mode toggle appears in WordPress admin (Settings > TRUENDO)
- [ ] Conditional fields show/hide when toggle is changed
- [ ] All 9 consent categories display with granted/denied options
- [ ] Wait time field accepts values 500-5000ms
- [ ] Settings save and persist correctly
- [ ] Frontend shows Google Consent Mode script before TRUENDO CMP script
- [ ] Page builder editing modes don't load consent mode script
- [ ] Configuration appears correctly in browser console as `window.dataLayer`

## File Modification Summary
- **Modified files**: 5 existing plugin files
- **New files**: 0 (extends existing architecture)
- **Core changes**: WordPress Settings API registration, admin form fields, script injection
- **Integration points**: Existing TRUENDO plugin hooks and patterns

## Notes for Implementation
- Follow existing WordPress coding standards used in the plugin
- Mirror existing TRUENDO script injection patterns exactly
- Maintain backward compatibility with existing plugin functionality
- Use WordPress sanitization functions (`rest_sanitize_boolean`, `sanitize_text_field`, etc.)
- All output must be properly escaped (`esc_attr`, `esc_html`, `wp_json_encode`)
- Test with popular page builders (Breakdance, Divi, Oxygen) for compatibility