<?php
/*
Plugin Name: HTML/PHP to WordPress Post Converter
Plugin URI: https://sohaibsportfolio.netlify.app
Description: Converts HTML or PHP snippets into WordPress posts with customizable attributes and settings.
Version: 1.0
Author: Syed Sohaib
Author URI: https://sohaibsportfolio.netlify.app
*/

// Hook into WordPress initialization

if (!defined('WPINC')) {
    die;
}

// Plugin Activation
function html_php_to_wp_post_activate()
{
    add_option('html_php_to_wp_post_default_settings', 'default values');
}
register_activation_hook(__FILE__, 'html_php_to_wp_post_activate');

// Plugin Deactivation
function html_php_to_wp_post_deactivate()
{
    // Cleanup logic goes here
}
register_deactivation_hook(__FILE__, 'html_php_to_wp_post_deactivate');

// Include functions
require_once plugin_dir_path(__FILE__) . 'includes/plugin-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-creation.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Hook into WordPress initialization
add_action('init', 'html_php_to_wp_post_init');
function html_php_to_wp_post_init()
{
    // Initialize plugin functionalities
    html_php_to_wp_post_admin_settings_init();
}
?>