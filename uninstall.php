<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('html_php_to_wp_post_default_settings');
// Additional cleanup logic like deleting custom tables can go here
