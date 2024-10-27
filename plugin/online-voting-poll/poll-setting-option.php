<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function hostingbuzpoll_add_settings_submenu()
{
    add_submenu_page(
        'edit.php?post_type=hostingbuzpoll', // Parent menu slug
        __('Poll Plugin Settings', 'hostingbuzpoll'), // Page title
        __('Settings', 'hostingbuzpoll'), // Submenu title
        'manage_options', // Required capability
        'hostingbuzpoll_settings', // Menu slug
        'hostingbuzpoll_display_settings_page' // Callback function to render settings page
    );
}
add_action('admin_menu', 'hostingbuzpoll_add_settings_submenu');

// Callback function to render the settings page
function hostingbuzpoll_display_settings_page()
{
   
}

if (!class_exists('ReduxFramework') && file_exists(dirname(__FILE__) . '/redux-framework/redux-core/framework.php')) {
    require_once(dirname(__FILE__) . '/redux-framework/redux-core/framework.php');
}
if (!isset($redux_demo) && file_exists(dirname(__FILE__) . '/redux-framework/sample/config.php')) {
    require_once(dirname(__FILE__) . '/redux-framework/sample/config.php');
}
