# Quickstart: Google Consent Mode v2 Integration

**Feature**: Google Consent Mode v2 Integration
**Target User**: WordPress Site Administrator
**Estimated Time**: 5-10 minutes

## Prerequisites

1. **TRUENDO Plugin Active**: Main TRUENDO plugin must be installed, activated, and configured
2. **Site ID Configured**: TRUENDO site ID must be set in plugin settings
3. **Admin Access**: User must have `manage_options` capability (Administrator role)

## Quick Setup Steps

### Step 1: Access TRUENDO Settings
1. Login to WordPress admin dashboard
2. Navigate to **Settings > TRUENDO**
3. Verify main TRUENDO plugin is enabled (checkbox checked)
4. Verify TRUENDO Site ID is configured (not empty)

### Step 2: Enable Google Consent Mode v2
1. Locate **"Google Consent Mode v2"** section
2. Check the **"Enable Google Consent Mode v2"** toggle
3. Additional configuration options will appear below

### Step 3: Configure Default Consent States
Set initial consent state for each Google category:

| Category | Recommended Setting | Description |
|----------|---------------------|-------------|
| **Advertising Storage** | `denied` | Stores data for advertising purposes |
| **Advertising User Data** | `denied` | Sends user data to Google for advertising |
| **Ad Personalization** | `denied` | Personalizes advertising content |
| **Analytics Storage** | `denied` | Stores data for analytics (can be `granted` if desired) |
| **Preferences** | `granted` | Stores user interface preferences |
| **Social Content** | `denied` | Embeds social media content |
| **Social Sharing** | `denied` | Enables social media sharing |
| **Personalization Storage** | `denied` | Stores data for content personalization |
| **Functionality Storage** | `granted` | Essential functionality data |

**Note**: Start with restrictive settings (mostly `denied`). Users can grant permissions through the TRUENDO consent banner.

### Step 4: Set Wait Time
1. Configure **"Wait for Consent"** field
2. **Recommended**: `500` milliseconds (0.5 seconds)
3. **Range**: 500-5000 milliseconds
4. **Purpose**: How long to wait for user interaction before applying default states

### Step 5: Save Configuration
1. Click **"Save Changes"** button
2. Confirm success message appears
3. Settings are now active on your website

## Verification Steps

### Frontend Verification
1. **Open your website** in a new browser/incognito window
2. **View page source** and look for:
   ```html
   <script>
   window.dataLayer = window.dataLayer || [];
   function gtag(){dataLayer.push(arguments);}
   gtag('consent', 'default', {
       'ad_storage': 'denied',
       'analytics_storage': 'denied',
       // ... other categories
   });
   </script>
   ```
3. **Check loading order**: Google Consent Mode script should appear **before** TRUENDO CMP script

### Admin Verification
1. Return to **Settings > TRUENDO**
2. Verify all Google Consent Mode settings are saved correctly
3. Toggle the main setting off/on to test conditional display

### Browser DevTools Verification
1. Open **Developer Tools** (F12)
2. Go to **Console** tab
3. Type `window.dataLayer` and press Enter
4. Should see array with consent configuration objects

## Integration Testing

### Test Consent Updates
1. **Load your website**
2. **Wait for TRUENDO banner** to appear
3. **Accept/deny categories** through the banner
4. **Check console** for consent update events:
   ```javascript
   gtag('consent', 'update', {
       'analytics_storage': 'granted'  // Example update
   });
   ```

### Test Different Scenarios
1. **All denied**: Set all categories to `denied`, test restricted tracking
2. **Analytics only**: Set only `analytics_storage` to `granted`
3. **All granted**: Test full tracking functionality

## Common Issues & Solutions

### Issue: Google Consent Mode Script Not Loading
**Symptoms**: No consent script in page source
**Solutions**:
1. Verify main TRUENDO plugin is enabled
2. Check TRUENDO Site ID is configured
3. Ensure Google Consent Mode toggle is enabled
4. Clear any caching plugins

### Issue: Script Loads in Wrong Order
**Symptoms**: TRUENDO CMP loads before Google Consent Mode
**Solutions**:
1. Check if theme/plugins modify `wp_head` hook priorities
2. Verify no JavaScript errors preventing script execution
3. Test with default WordPress theme to isolate conflicts

### Issue: Consent Updates Not Working
**Symptoms**: Categories don't update when user makes choices
**Solutions**:
1. Check browser console for JavaScript errors
2. Verify TRUENDO CMP integration is working
3. Ensure no ad blockers are interfering

### Issue: Page Builder Conflicts
**Symptoms**: Script loads in page builder editing mode
**Solutions**:
1. Plugin automatically detects Breakdance, Divi, Oxygen editors
2. If using different page builder, contact support
3. Script should not load in editing/preview modes

## Advanced Configuration

### Custom Default States
For specific use cases, you might want different defaults:

**News/Content Site**:
- `analytics_storage`: `granted` (for content analytics)
- `functionality_storage`: `granted` (for comments, search)
- Everything else: `denied`

**E-commerce Site**:
- `functionality_storage`: `granted` (for cart, checkout)
- `preferences`: `granted` (for user experience)
- `analytics_storage`: `granted` (for sales analytics)
- Advertising categories: `denied` (until user consent)

### Wait Time Optimization
- **Fast Sites**: 500ms (default)
- **Slow Loading**: 1000-2000ms (give more time for user to see banner)
- **Mobile Heavy**: 1000ms (touch interaction may be slower)

## Troubleshooting Commands

### Check Configuration via WordPress CLI
```bash
# Check if Google Consent Mode is enabled
wp option get truendo_google_consent_enabled

# Check default consent states
wp option get truendo_google_consent_default_states

# Check wait time setting
wp option get truendo_google_consent_wait_time
```

### Reset to Defaults
```bash
# Reset Google Consent Mode settings
wp option delete truendo_google_consent_enabled
wp option delete truendo_google_consent_default_states
wp option delete truendo_google_consent_wait_time
```

## Next Steps

After successful setup:

1. **Monitor Analytics**: Check Google Analytics/GTM for proper consent signals
2. **Test Compliance**: Verify GDPR compliance with consent states
3. **User Training**: Educate content creators about consent mode implications
4. **Performance**: Monitor page load impact (should be minimal)
5. **Updates**: Keep TRUENDO plugin updated for latest consent mode features

## Support Resources

- **Plugin Settings**: WordPress Admin > Settings > TRUENDO
- **TRUENDO Documentation**: [TRUENDO Help Center]
- **Google Consent Mode**: [Google Developers Documentation]
- **WordPress Support**: Check plugin's support forum

This quickstart gets Google Consent Mode v2 running quickly while ensuring proper GDPR compliance and integration with your existing TRUENDO setup.