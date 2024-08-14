<?php
/*
Plugin Name: HTML/PHP to WordPress Post Converter
Plugin URI: https://sohaibsportfolio.netlify.app
Description: Converts HTML or PHP snippets into WordPress posts with customizable attributes and settings.
Version: 1.0
Author: Syed Sohaib
Author URI: https://sohaibsportfolio.netlify.app
*/

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
    delete_option('html_php_to_wp_post_default_settings');
}
register_deactivation_hook(__FILE__, 'html_php_to_wp_post_deactivate');

// Include plugin functions
require_once plugin_dir_path(__FILE__) . 'includes/plugin-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-creation.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Enqueue CodeMirror in admin
function enqueue_codemirror_editor() {
    wp_enqueue_script('codemirror', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js', array(), null, true);
    wp_enqueue_style('codemirror-css', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css');

    // Additional themes and modes
    wp_enqueue_script('codemirror-mode-htmlmixed', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js', array('codemirror'), null, true);
    wp_enqueue_script('codemirror-mode-php', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js', array('codemirror'), null, true);
    wp_enqueue_script('codemirror-mode-css', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js', array('codemirror'), null, true);
    wp_enqueue_script('codemirror-mode-javascript', '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js', array('codemirror'), null, true);
    
    // Initialize CodeMirror
    wp_add_inline_script('codemirror', 'jQuery(document).ready(function($) {
        var editor = CodeMirror.fromTextArea(document.getElementById("html_php_input"), {
            mode: "htmlmixed",
            lineNumbers: true,
            theme: "default"
        });
    });');
}
add_action('admin_enqueue_scripts', 'enqueue_codemirror_editor');
