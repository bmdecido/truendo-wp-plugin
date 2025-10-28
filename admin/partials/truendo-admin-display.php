<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.truendo.com
 * @since      1.0.0
 *
 * @package    Truendo
 * @subpackage Truendo/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$tabs = array(
    __('TRUENDO Configuration', 'truendo'),
    __('Consent Mode', 'truendo'),
    __('Help', 'truendo'),
);
?>
<div class="wrap truendo_settings">
    <div class='truendo_top_holder'>
        <img class='truendo_logo' src='<?php echo plugin_dir_url(__FILE__) . ('../assets/truendoLogo.svg'); ?>' />
        <h1><?php echo __('TRUENDO Settings'); ?></h1>
    </div>
    <form action="options.php" method="post" class='truendo_main_form'>
        <?php
        settings_fields('truendo_settings');
        do_settings_sections('truendo_settings');
        ?>
        <div class='truendo_top_tabs_holder'>
            <?php
            for ($i = 0; $i < count($tabs); $i++) {
                ?>
                <button class='truendo_tab_header <?php if ($i == 0) {
                    echo "active";
                } ?>'
                    data-true_tab="<?php echo $i; ?>"><?php echo $tabs[$i]; ?></button>
            <?php
            }
            ?>
        </div>
        <div class='truendo_settings_holder'>
            <!-- TRUENDO Configuration tab -->
            <section>
                <div class='truendo_setting_holder'>
                    <div class='truendo_setting_info'>
                        <p><?php echo __('Enable TRUENDO', 'truendo'); ?></p>
                    </div>
                    <div class='truendo_setting'>
                        <input type='checkbox' class='truendo_enabled' name='truendo_enabled' <?php echo esc_attr(get_option('truendo_enabled')) == true ? 'checked="checked"' : ''; ?> />
                    </div>
                </div>
                <div
                    class='truendo_show_when_active <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Site-ID', 'truendo'); ?></p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='text' name='truendo_site_id'
                                value="<?php echo esc_attr(get_option('truendo_site_id')); ?>" />
                        </div>
                    </div>

                    <!-- TruSettings Configuration Section -->
                    <div class='truendo_setting_holder truendo_trusettings_toggle' style='cursor: pointer;'>
                        <div class='truendo_setting_info'>
                            <p><strong><?php echo __('Advanced TruSettings Configuration', 'truendo'); ?> &#9660;</strong></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Click to expand advanced TRUENDO settings configuration.', 'truendo'); ?>
                            </p>
                        </div>
                    </div>

                    <div class='truendo_trusettings_fields' style='display: none;'>
                    <!-- Boolean Settings -->
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('No Font', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Disable custom fonts in the privacy panel.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' name='truendo_trusettings_nofont'
                                <?php echo get_option('truendo_trusettings_nofont') ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Transparency', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Enable transparent background for the privacy panel.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' name='truendo_trusettings_transparency'
                                <?php echo get_option('truendo_trusettings_transparency', true) ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Accessibility', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Enable accessibility features in the privacy panel.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' name='truendo_trusettings_accessibility'
                                <?php echo get_option('truendo_trusettings_accessibility') ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Accessibility Border Color', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Set custom border color for accessibility mode (e.g., #000000).', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='text' name='truendo_trusettings_accessibility_border_color'
                                value="<?php echo esc_attr(get_option('truendo_trusettings_accessibility_border_color')); ?>"
                                placeholder="#000000" />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Autoblocking Disabled', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Disable automatic script blocking.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' name='truendo_trusettings_autoblocking_disabled'
                                <?php echo get_option('truendo_trusettings_autoblocking_disabled') ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>

                    <!-- Text/String Settings -->
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Language Override', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Override language setting (e.g., en, de, fr).', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='text' name='truendo_trusettings_lang'
                                value="<?php echo esc_attr(get_option('truendo_trusettings_lang')); ?>"
                                placeholder="auto" />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Popup Delay', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Delay before showing popup (in milliseconds).', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='number' name='truendo_trusettings_popup_delay'
                                value='<?php echo esc_attr(get_option('truendo_trusettings_popup_delay', 0)); ?>'
                                min='0' step='1' />
                            <span class='truendo_unit_label'><?php echo __('ms', 'truendo'); ?></span>
                        </div>
                    </div>

                    </div><!-- End truendo_trusettings_fields -->

                    <div class='truendo_setting_holder'>
                        <div class='submit'>
                            <a href='http://console.truendo.com/' target='_blank'
                                class='button'><?php echo __('Go to Truendo Dashboard', 'truendo'); ?></a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Consent Mode tab -->
            <section>
                <div
                    class='truendo_show_when_active <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('Enable Google Consent Mode v2', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Integrates with Google Analytics and Google Ads for improved measurement under GDPR compliance.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' class='truendo_google_consent_enabled'
                                name='truendo_google_consent_enabled' <?php echo get_option('truendo_google_consent_enabled') ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>
                    <div
                        class='truendo_google_consent_fields <?php echo get_option('truendo_google_consent_enabled') ? 'active' : ''; ?>'>
                        <!-- Google Consent Mode Categories -->
                        <div class='truendo_setting_holder'>
                            <div class='truendo_setting_info'>
                                <p><?php echo __('Default Consent States', 'truendo'); ?></p>
                                <p class='truendo_setting_description'>
                                    <?php echo __('Configure the default consent state for each Google category. Users can change these through the consent banner.', 'truendo'); ?>
                                </p>
                            </div>
                            <div class='truendo_setting'>
                                <div class='truendo_consent_categories'>
                                    <?php
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

                                    $category_keys = array_keys($categories);
                                    $last_key = end($category_keys);

                                    foreach ($categories as $category_key => $category_label) {
                                        // Last category should always be granted and cannot be changed
                                        $is_last_category = ($category_key === $last_key);
                                        $current_state = isset($default_states[$category_key]) ? $default_states[$category_key] : ($is_last_category ? 'granted' : 'denied');
                                        ?>
                                        <div class='truendo_consent_category'>
                                            <div class='truendo_category_label'>
                                                <label><?php echo esc_html($category_label); ?></label>
                                                <?php if ($is_last_category): ?>
                                                    <span class='truendo_setting_description' style='font-size: 12px; color: #6c757d;'><?php echo __('(Always Allowed)', 'truendo'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class='truendo_category_options'>
                                                <label class='truendo_radio_label'>
                                                    <input type='radio'
                                                        name='truendo_google_consent_default_states[<?php echo esc_attr($category_key); ?>]'
                                                        value='granted' <?php echo $is_last_category ? 'checked disabled' : checked('granted', $current_state, false); ?> />
                                                    <span><?php echo __('Granted', 'truendo'); ?></span>
                                                </label>
                                                <label class='truendo_radio_label'>
                                                    <input type='radio'
                                                        name='truendo_google_consent_default_states[<?php echo esc_attr($category_key); ?>]'
                                                        value='denied' <?php echo $is_last_category ? 'disabled' : checked('denied', $current_state, false); ?> />
                                                    <span><?php echo __('Denied', 'truendo'); ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Wait Time Setting -->
                        <div class='truendo_setting_holder'>
                            <div class='truendo_setting_info'>
                                <p><?php echo __('Wait for Consent (milliseconds)', 'truendo'); ?></p>
                                <p class='truendo_setting_description'>
                                    <?php echo __('How long to wait for user consent before applying default states. Range: 500-5000ms.', 'truendo'); ?>
                                </p>
                            </div>
                            <div class='truendo_setting'>
                                <input type='number' name='truendo_google_consent_wait_time'
                                    value='<?php echo esc_attr(get_option('truendo_google_consent_wait_time', 500)); ?>'
                                    min='500' max='5000' step='1' />
                                <span class='truendo_unit_label'><?php echo __('ms', 'truendo'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- WordPress Consent API Section -->
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __('WordPress Consent API', 'truendo'); ?></p>
                            <p class='truendo_setting_description'>
                                <?php echo __('Integrates with WordPress Consent API for standardized consent management across plugins.', 'truendo'); ?>
                            </p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='checkbox' class='truendo_wp_consent_enabled' name='truendo_wp_consent_enabled'
                                <?php echo get_option('truendo_wp_consent_enabled') ? 'checked="checked"' : ''; ?> />
                        </div>
                    </div>
                    <div
                        class='truendo_wp_consent_fields <?php echo get_option('truendo_wp_consent_enabled') ? 'active' : ''; ?>'>
                        <!-- WordPress Consent API Categories -->
                        <div class='truendo_setting_holder'>
                            <div class='truendo_setting_info'>
                                <p><?php echo __('Default WP Consent States', 'truendo'); ?></p>
                                <p class='truendo_setting_description'>
                                    <?php echo __('Configure the default consent state for each WordPress Consent API category. Users can change these through the consent banner.', 'truendo'); ?>
                                </p>
                            </div>
                            <div class='truendo_setting'>
                                <div class='truendo_consent_categories'>
                                    <?php
                                    $wp_categories = array(
                                        'statistics' => __('Statistics', 'truendo'),
                                        'statistics-anonymous' => __('Anonymous Statistics', 'truendo'),
                                        'marketing' => __('Marketing', 'truendo'),
                                        'preferences' => __('Preferences', 'truendo'),
                                        'functional' => __('Functional (Always Allowed)', 'truendo'),
                                    );

                                    $wp_default_states = get_option('truendo_wp_consent_default_states', array());

                                    $wp_category_keys = array_keys($wp_categories);
                                    $wp_last_key = end($wp_category_keys);

                                    foreach ($wp_categories as $category_key => $category_label) {
                                        // Last category should always be allow and cannot be changed
                                        $is_last_category = ($category_key === $wp_last_key);
                                        $current_state = isset($wp_default_states[$category_key]) ? $wp_default_states[$category_key] : ($is_last_category ? 'allow' : 'deny');
                                        ?>
                                        <div class='truendo_consent_category'>
                                            <div class='truendo_category_label'>
                                                <label><?php echo esc_html($category_label); ?></label>
                                            </div>
                                            <div class='truendo_category_options'>
                                                <label class='truendo_radio_label'>
                                                    <input type='radio'
                                                        name='truendo_wp_consent_default_states[<?php echo esc_attr($category_key); ?>]'
                                                        value='allow' <?php echo $is_last_category ? 'checked disabled' : checked('allow', $current_state, false); ?> />
                                                    <span><?php echo __('Allow', 'truendo'); ?></span>
                                                </label>
                                                <label class='truendo_radio_label'>
                                                    <input type='radio'
                                                        name='truendo_wp_consent_default_states[<?php echo esc_attr($category_key); ?>]'
                                                        value='deny' <?php echo $is_last_category ? 'disabled' : checked('deny', $current_state, false); ?> />
                                                    <span><?php echo __('Deny', 'truendo'); ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Help tab -->
            <section>
                <div class='truendo_help_section'>
                    <!-- General Section -->
                    <div class='truendo_help_category'>
                        <h2><?php echo __('General', 'truendo'); ?></h2>
                        <div class='truendo_help_links'>
                            <a href='https://docs.truendo.com/en/1.0/integrations/wordpress' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-book'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Getting Started with WordPress Installation', 'truendo'); ?></strong>
                                    <p><?php echo __('Learn how to install and set up TRUENDO on your WordPress site.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/regionalization/language-setup' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-translation'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Configuring Additional Languages', 'truendo'); ?></strong>
                                    <p><?php echo __('Set up multiple languages for your consent banner.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/customization/banner-customization' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-admin-customizer'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Banner Customisation', 'truendo'); ?></strong>
                                    <p><?php echo __('Customize the appearance and behavior of your consent banner.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/service-management/manual-rescan' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-update'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Initiate a Manual Domain Re-Scan', 'truendo'); ?></strong>
                                    <p><?php echo __('Manually trigger a domain scan to detect new cookies and scripts.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/truendo-integrity' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-warning'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Understanding Console Warnings', 'truendo'); ?></strong>
                                    <p><?php echo __('Learn about common console warnings and how to resolve them.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/getting-started/get-to-know-truendo' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-info'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('More Information About TRUENDO CMP', 'truendo'); ?></strong>
                                    <p><?php echo __('Discover all the features and capabilities of TRUENDO.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Section -->
                    <div class='truendo_help_category'>
                        <h2><?php echo __('Advanced', 'truendo'); ?></h2>
                        <div class='truendo_help_links'>
                            <a href='https://docs.truendo.com/en/1.0/frameworks/google-consent-mode-v2/troubleshooting-advanced' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-admin-tools'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Troubleshooting Google Consent Mode', 'truendo'); ?></strong>
                                    <p><?php echo __('Resolve issues with Google Consent Mode v2 integration.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/regionalization/geo-control' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-location'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Using TRUENDO Smart-Geo', 'truendo'); ?></strong>
                                    <p><?php echo __('Configure region-specific consent behavior with Smart-Geo.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>

                            <a href='https://docs.truendo.com/en/1.0/regionalization/custom-texttranslations' target='_blank' class='truendo_help_link'>
                                <span class='dashicons dashicons-editor-quote'></span>
                                <div class='truendo_help_link_content'>
                                    <strong><?php echo __('Applying Custom Banner Translations', 'truendo'); ?></strong>
                                    <p><?php echo __('Create and apply custom translations for your consent banner.', 'truendo'); ?></p>
                                </div>
                                <span class='dashicons dashicons-external'></span>
                            </a>
                        </div>
                    </div>

                    <!-- Documentation Button -->
                    <div class='truendo_help_docs_button'>
                        <a href='https://docs.truendo.com/' target='_blank' class='truendo_docs_main_button'>
                            <span class='dashicons dashicons-media-document'></span>
                            <?php echo __('Visit TRUENDO Documentation', 'truendo'); ?>
                            <span class='dashicons dashicons-arrow-right-alt2'></span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- save button -->
            <div class='truendo_submit_holder <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
                <?php submit_button(); ?>
            </div>
        </div>
    </form>
    <div class='truendo_show_when_active_extra <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
        <div class='truendo_extra_info'>
            <p><a href='https://truendo.com/docs/connect-cookies-to-the-privacy-panel/'
                    target='_blank'><?php echo __('Click here', 'truendo'); ?></a>
                <?php echo __('Learn how to attach cookies.', 'truendo'); ?></p>
        </div>
        <hr />
    </div>
</div>
