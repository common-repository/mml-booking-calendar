<?php
/**
 * Plugin Name: MML Booking Calendar
 * Description: Interact with the My Music Lessons platform to take bookings directly from your website.
 * Author: My Music Lessons
 * Author URI: https://mymusiclessons.org.uk
 * Version: 1.0.0
 * Copyright (C) 2020 My Music Lessons Limited
 * License: GNUGPLv3 
 * License URI: https://www.gnu.org/licenses/
 */

add_action('admin_menu', 'mml_booking_calendar_admin_menu');

register_activation_hook(__FILE__, 'mml_booking_calendar_booking_calendar_activate_plugin_hook');

function mml_booking_calendar_booking_calendar_activate_plugin_hook()
{
    // create page if it doesnt already exist
    if (!get_page_by_path('mml_booking_calendar-booking-confirmation', 'OBJECT', 'page')) {
        wp_insert_post(array(
            'post_name' => 'mml-booking-confirmation',
            'post_content' => 'some contents go here',
            'post_title' => 'Booking Confirmation',
            'post_status' => 'publish',
            'post_type' => 'page'
        ));
    }
}

function mml_booking_calendar_admin_menu()
{
    add_menu_page('MML Booking Calendar', 'MML Booking Calendar', 'manage_options', 'mml_booking_calendar-plugin', 'mml_booking_calendar_options_page', 'dashicons-calendar-alt');
}

add_action('admin_init', 'mml_booking_calendar_admin_init');

function mml_booking_calendar_admin_init()
{

    register_setting('mml_booking_calendar-settings-group', 'mml_booking_calendar-plugin-settings');

    add_settings_section('section-1', 'Set Up', 'mml_booking_calendar_section_1_callback', 'mml_booking_calendar-plugin');

    $settings = (array)get_option('mml_booking_calendar-plugin-settings');

    if (isset($settings['api-key']) && $settings['api-key'] !== '') {
        add_settings_section('section-2', 'Your Calendar Code', 'mml_booking_calendar_section_2_callback', 'mml_booking_calendar-plugin');
    }

    add_settings_field('api_key', 'Your Api Key', 'mml_booking_calendar_field_api_key_callback', 'mml_booking_calendar-plugin', 'section-1');

}

/*
 * THE ACTUAL PAGE
 * */
function mml_booking_calendar_options_page()
{
    ?>
    <div class="wrap">
        <h2><?php _e('MML Booking Calendar', 'textdomain'); ?></h2>
        <form action="options.php" method="POST">
            <?php settings_fields('mml_booking_calendar-settings-group'); ?>
            <?php do_settings_sections('mml_booking_calendar-plugin'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

/*
* THE SECTIONS
* Hint: You can omit using add_settings_field() and instead
* directly put the input fields into the sections.
* */
function mml_booking_calendar_section_1_callback()
{
    _e('<div style="background-color: #FBF7DF; padding: 10px;">');
    _e('Please note, to use this plugin you must first have an active teacher account on My Music Lessons. 
    
    <br><br><a href="https://mymusiclessons.org.uk/signup" target="_blank">Sign Up</a> is quick and free.
    
    <br><br>You can then use your My Music Lessons account to set your availability and manage your bookings.<br/><br/>');
    
    _e('Already signed up? You will find your API key inside the settings menu on <a href="https://mymusiclessons.org.uk/dashboard/settings" target="_blank">My Music Lessons.</a>
    
    <br><br>Please copy and paste the API key into the box below and click "Save Changes" to generate your MML Booking Calender embed code.<br><br>Need Help? <a href="https://mymusiclessons.org.uk/contact" target="_blank">Contact Us</a>');
    _e('</div>');

}

function mml_booking_calendar_section_2_callback()
{
    $settings = (array)get_option('mml_booking_calendar-plugin-settings');

    if (isset($settings['api-key']) && $settings['api-key'] !== '') {

        $callbackUrl = base64_encode(site_url());

        $tag = htmlspecialchars('<iframe 
    src="https://mymusiclessons.org.uk/external/diary?key=' . $settings['api-key'] . '&amp;callback_url='.$callbackUrl.'" 
    name="mml-booking-calendar-iframe" 
    width="100%" 
    height="700" 
    frameborder="1" 
    marginwidth="0px" 
    marginheight="0px" 
    scrolling="no" 
    style="border: 0px #ffffff none;">');

        _e('<div style="background-color: #FBF7DF; padding: 10px;">');
        _e('To embed your MML Booking Calender, please copy the HTML code below and paste it onto your preferred page using the wordpress page editor.<br><br><a href="https://mymusiclessons.org.uk/login" target="_blank">Login</a> to manage your account.');
        _e('<div style="font-weight: bold; padding: 20px;">');
        _e('<pre><code style="word-wrap: break-spaces">' . $tag . '</code></pre>');
        _e('</div>');

    }

}

/*
* THE FIELDS
* */
function mml_booking_calendar_field_api_key_callback()
{

    $settings = (array)get_option('mml_booking_calendar-plugin-settings');
    $field = 'api-key';
    $value = esc_attr($settings[$field]);

    echo '<textarea name="mml_booking_calendar-plugin-settings[' . $field . ']" rows="5" style="min-width: 500px;">' . $value . '</textarea>';
}