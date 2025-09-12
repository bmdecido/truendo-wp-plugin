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
        __('Setup','truendo'), 
     //   __('Buttons','truendo'),
        // __('Scripts','truendo'),
//        __('Widget', 'truendo'),
    );
?>
<div class="wrap truendo_settings">
    <div class='truendo_top_holder'>
        <img class='truendo_logo' src='<?php echo plugin_dir_url( __FILE__ ) . ('../assets/truendoLogo.svg'); ?>'/>
        <h1><?php echo __('TRUENDO Settings'); ?></h1>
    </div>
    <form action="options.php" method="post" class='truendo_main_form'>
        <?php
            settings_fields( 'truendo_settings' );
            do_settings_sections( 'truendo_settings' );
        ?>
        <div class='truendo_top_tabs_holder'>
            <?php
                for($i = 0; $i < count($tabs); $i++) {
                    ?>
                    <button class='truendo_tab_header <?php if ($i == 0) { echo "active"; }?>' data-true_tab="<?php echo $i; ?>"><?php echo $tabs[$i]; ?></button>
                    <?php  
                }
            ?>
        </div>
        <div class='truendo_settings_holder'>
            <!-- Setup tab -->
            <section>
            <!--   <p class='truendo_top_sentence'><?php echo __( 'Please configure the options as required below:', 'truendo'); ?></p> -->
                <div class='truendo_setting_holder'>
                    <div class='truendo_setting_info'>
                        <p><?php echo __( 'Enable TRUENDO', 'truendo'); ?></p>
                    </div>
                    <div class='truendo_setting'>
                        <input type='checkbox' class='truendo_enabled' name='truendo_enabled' <?php echo esc_attr( get_option('truendo_enabled') ) == true ? 'checked="checked"' : ''; ?> />
                    </div>
                </div>
                <div class='truendo_show_when_active <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
                    <div class='truendo_setting_holder'>
                        <div class='truendo_setting_info'>
                            <p><?php echo __( 'Site-ID', 'truendo'); ?></p>
                        </div>
                        <div class='truendo_setting'>
                            <input type='text' name='truendo_site_id'  value="<?php echo esc_attr( get_option('truendo_site_id') ); ?>" />
                        </div>
                    </div>

                    <div class='truendo_setting_holder'>

                        <div class='submit'>
                                    <a href='http://console.truendo.com/' target='_blank' class='button'><?php echo __('Go to Truendo Dashboard', 'truendo'); ?></a>
                            </div>
                     
                    </div>


                    </div>
                   
            </section>
            <!-- Scripts tab/settings -->
            <section>
                
                <?php $types = array(
                    //    'ga' => __('Google Analytics Code', 'truendo')
                    'tru_stat_' => array(__('Statistics', 'truendo'), __('One per entry - Google analytics, Hotjar, etc','truendo')),
                    'tru_mark_' => array(__('Marketing', 'truendo'), __('One per entry - Facebook Pixel, etc','truendo')),
                );
                foreach ($types as $val => $key) {
                ?>
                <div class='truendo_setting_holder'>
                    <div class='truendo_setting_info'>
                        <p><?php echo $key[0]; ?></p>
                    </div>
                    <div class='truendo_setting'>
                        <div class="form-group">
                            <textarea class="form-control" name="" id="<?php echo $val; ?>itemInput" placeholder='<?php echo $key[1]; ?>'></textarea>
                            <button id="<?php echo $val; ?>addButton" class="btn btn-primary"></button>
                        </div>
                        <!-- <button id="<?php echo $val; ?>clearButton" class="btn btn-danger"><?php echo __('Remove All');?></button> -->
                        <ul class='truendo_todolist' id="<?php echo $val; ?>todoList"></ul>
                        <input
                            type='hidden'
                            class='<?php echo $val; ?>json_holder'
                            name='<?php echo $val; ?>truendo_header_scripts_json'
                            type='text' value='<?php echo get_option($val . 'truendo_header_scripts_json'); ?>'/>
                    </div>
                </div>
                <?php
                }
            ?>
            </section>
            <!-- Widget tab/settings tru_widget_
            <section>
                <div class='truendo_setting_holder'>
                    <div class='truendo_setting_info'>
                        <p><?php echo __("Social Media Shares/Likes"); ?></p>
                    </div>
                    <div class='truendo_setting'>
                        <div class="form-group">
                            <textarea
                                class="form-control"
                                name=""
                                id="tru_widget_itemInput"
                                placeholder='<?php echo __("Add one share/like function per line"); ?>'></textarea>
                            <button id="tru_widget_addButton" class="btn btn-primary"></button>
                        </div>
                        <ul class='truendo_todolist' id="tru_widget_todoList"></ul>
                        <input 
                            type='hidden'
                            class='tru_widget_json_holder'
                            name='tru_widget_truendo_header_scripts_json'
                            type='text'
                            value='<?php echo get_option('tru_widget_truendo_header_scripts_json'); ?>'/>
                    </div>
                </div>
            </section>
            -->
            <!-- save button -->
            <div class='truendo_submit_holder <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
                <?php submit_button(); ?>
            </div>
        </div>
    </form>
    <div class='truendo_show_when_active_extra <?php echo get_option('truendo_enabled') == true ? 'active' : ''; ?>'>
        <div class='truendo_extra_info'>
            <p><a href='https://truendo.com/docs/connect-cookies-to-the-privacy-panel/' target='_blank'><?php echo __('Click here', 'truendo'); ?></a> <?php echo __( 'Learn how to attach cookies.', 'truendo'); ?></p>
        </div>
        <hr/>
    </div>
    <!--
        <div class='truendo_extra_info hidden_when_enabled'>
            <h3><?php echo __("Don't have a TRUENDO account yet?","truendo");?></h3>
            <p><?php echo __("Scan your site below and sign up with us, the process is quick and easy!","truendo");?></p>
            <form class="truendo_dash_scan_form">
                <div class="tru_dash_scan_input_holder">
                    <input type="text" class="tru_dash_scan_input" placeholder="www.example.com">
                </div>
                <div class="tru_dash_scan_button_holder">
                    <button type="submit" class="tru_dash_scan_button" href=""><?php echo __('START SCAN','truendo') ?></button>
                </div>
            </form>
        </div>
    -->
</div>
