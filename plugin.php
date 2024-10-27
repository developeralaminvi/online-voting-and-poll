<?php
/**
 * Plugin Name:       Online Voting and Poll
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       A plugin for online voting and poll with featured image, social sharing, and vote tracking.
 * Version:           1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Hosting Buz
 * Author URI:        https://hostingbuz.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hostingbuzpoll
 */

// بِسْمِ ٱللَّٰهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ

// Exit if accessed directly
if (!defined(constant_name: 'ABSPATH')) {
    exit;
}

// Enqueue plugin scripts and styles
function hostingbuzpoll_enqueue_scripts()
{
    // Enqueue html2canvas library
    wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', array(), '1.4.1', true);

    // Enqueue Font Awesome from CDN
    wp_enqueue_style('hostingbuzpoll-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

    // Enqueue Bootstrap CSS from CDN
    wp_enqueue_style('hostingbuzpoll-bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', array(), '5.2.3');

    // Enqueue custom CSS
    wp_enqueue_style('hostingbuzpoll-style', plugin_dir_url(__FILE__) . 'assets/css/poll-style.css', array(), '1.0');

    // Enqueue jQuery (WordPress includes it by default, but ensure it's loaded)
    wp_enqueue_script('jquery');

    // Enqueue Bootstrap JS from CDN with jQuery dependency
    wp_enqueue_script('hostingbuzpoll-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.2.3', true);

    // Enqueue custom JS with jQuery dependency
    wp_enqueue_script('hostingbuzpoll-script', plugin_dir_url(__FILE__) . 'assets/js/poll.js', array('jquery'), '1.0', true);

    // Localize script to pass AJAX URL and nonce
    wp_localize_script('hostingbuzpoll-script', 'hostingbuzpoll_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hostingbuzpoll_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'hostingbuzpoll_enqueue_scripts');


// Enqueue plugin admin scripts and styles
function hbuz_poll_enqueue_admin_scripts()
{
    // Enqueue the custom admin script
    wp_enqueue_script('hbuz-admin-poll-js', plugin_dir_url(__FILE__) . 'assets/js/admin-poll.js', array('jquery'), '1.0', true);

    // Enqueue the CSS file for admin pages
    wp_enqueue_style('hbuz-admin-poll-css', plugin_dir_url(__FILE__) . 'assets/css/admin-poll-styles.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'hbuz_poll_enqueue_admin_scripts');


// Example AJAX handler
add_action('wp_ajax_hostingbuzpoll_vote', 'hostingbuzpoll_vote_handler');
add_action('wp_ajax_nopriv_hostingbuzpoll_vote', 'hostingbuzpoll_vote_handler');

function hostingbuzpoll_vote_handler()
{
    // Security check
    check_ajax_referer('hostingbuzpoll_nonce', 'security');

    // Fetch and sanitize vote data
    $poll_id = isset($_POST['poll_id']) ? intval($_POST['poll_id']) : 0;

    if (!$poll_id) {
        wp_send_json_error(['message' => __('Invalid poll ID', 'hostingbuzpoll')]);
    }

    wp_send_json_success(['message' => __('Your vote has been recorded', 'hostingbuzpoll')]);
}


// online voting poll
require_once plugin_dir_path(__FILE__) . 'plugin/online-voting-poll/online-voting-poll.php';



// "Settings" link next to the "Deactivate" link for hostingbuzpoll plugin

function hostingbuzpoll_plugin_action_links($links)
{
    $settings_link = '<a href="edit.php?post_type=hostingbuzpoll&page=hostingbuzpoll_settings">' . __('Settings', 'hostingbuzpoll') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'hostingbuzpoll_plugin_action_links');