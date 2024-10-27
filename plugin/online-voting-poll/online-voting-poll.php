<?php

// Register custom post type for Polls

// Exit if accessed directly
if (!defined(constant_name: 'ABSPATH')) {
    exit;
}
function hostingbuzpoll_register_poll_post_type()
{
    $labels = array(
        'name' => __('Polls', 'hostingbuzpoll'),
        'singular_name' => __('Poll', 'hostingbuzpoll'),
        'menu_name' => __('Polls', 'hostingbuzpoll'),
        'name_admin_bar' => __('Poll', 'hostingbuzpoll'),
        'add_new' => __('Add New', 'hostingbuzpoll'),
        'add_new_item' => __('Add New Poll', 'hostingbuzpoll'),
        'new_item' => __('New Poll', 'hostingbuzpoll'),
        'edit_item' => __('Edit Poll', 'hostingbuzpoll'),
        'view_item' => __('View Poll', 'hostingbuzpoll'),
        'all_items' => __('All Polls', 'hostingbuzpoll'),
        'search_items' => __('Search Polls', 'hostingbuzpoll'),
        'parent_item_colon' => __('Parent Polls:', 'hostingbuzpoll'),
        'not_found' => __('No polls found.', 'hostingbuzpoll'),
        'not_found_in_trash' => __('No polls found in Trash.', 'hostingbuzpoll'),
        'featured_image' => __('Poll Image', 'hostingbuzpoll'),
        'set_featured_image' => __('Set poll image', 'hostingbuzpoll'),
        'remove_featured_image' => __('Remove poll image', 'hostingbuzpoll'),
        'use_featured_image' => __('Use as poll image', 'hostingbuzpoll'),
        'archives' => __('Poll Archives', 'hostingbuzpoll'),
        'insert_into_item' => __('Insert into poll', 'hostingbuzpoll'),
        'uploaded_to_this_item' => __('Uploaded to this poll', 'hostingbuzpoll'),
        'filter_items_list' => __('Filter polls list', 'hostingbuzpoll'),
        'items_list_navigation' => __('Polls list navigation', 'hostingbuzpoll'),
        'items_list' => __('Polls list', 'hostingbuzpoll'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'menu_position' => 5, // Position below Posts
        'menu_icon' => 'dashicons-chart-bar', // Dashicon for Polls
        'supports' => array('title', 'thumbnail')
    );

    register_post_type('hostingbuzpoll', $args);
}
add_action('init', 'hostingbuzpoll_register_poll_post_type');


// Add a meta box for poll options
function hbuz_add_poll_metabox()
{
    add_meta_box(
        'hbuz_poll_options',
        __('Poll Add', 'hostingbuzpoll'),
        'hbuz_poll_options_callback',
        'hostingbuzpoll',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hbuz_add_poll_metabox');

function hbuz_poll_options_callback($post)
{
    // Retrieve existing poll options if they exist
    $poll_options = get_post_meta($post->ID, '_poll_options', true);

    // Make sure poll options is an array
    if (!is_array($poll_options)) {
        $poll_options = array('');
    }

    // Nonce for security
    wp_nonce_field('hbuz_save_poll_options', 'hbuz_poll_options_nonce');

    echo '<div id="poll-options-container">';

    foreach ($poll_options as $index => $option) {
        echo '<div class="poll-option-row">';
        echo '<p>';
        echo '<label>' . __('Option', 'hostingbuzpoll') . ' ' . ($index + 1) . '</label><br />';
        echo '<input type="text" name="poll_options[]" value="' . esc_attr($option) . '" style="width: 80%;" />';
        echo '<button type="button" class="button remove-poll-option" style="margin-left: 10px;">' . __('Remove', 'hostingbuzpoll') . '</button>';
        echo '</p>';
        echo '</div>';
    }

    echo '</div>';

    // Add button to allow adding more options dynamically
    echo '<button type="button" id="add-poll-option" class="button button-secondary">' . __('Add Option', 'hostingbuzpoll') . '</button>';

    // JavaScript to add new fields dynamically, to remove them, and to show the alert on unsaved changes
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var formChanged = false;

            // Function to mark form as changed
            function markAsChanged() {
                formChanged = true;
            }

            // Add new poll option
            $('#add-poll-option').click(function (e) {
                e.preventDefault();
                var optionNumber = $('#poll-options-container .poll-option-row').length + 1;
                var newOption = '<div class="poll-option-row"><p><label><?php _e("Option", "hostingbuzpoll"); ?> ' + optionNumber + '</label><br /><input type="text" name="poll_options[]" value="" style="width: 80%;" /><button type="button" class="button remove-poll-option" style="margin-left: 10px;"><?php _e("Remove", "hostingbuzpoll"); ?></button></p></div>';
                $('#poll-options-container').append(newOption);
                markAsChanged(); // Mark as changed when adding an option
            });

            // Remove poll option
            $(document).on('click', '.remove-poll-option', function (e) {
                e.preventDefault();
                $(this).closest('.poll-option-row').remove();

                // Update the labels for remaining options
                $('#poll-options-container .poll-option-row').each(function (index) {
                    $(this).find('label').text('<?php _e("Option", "hostingbuzpoll"); ?> ' + (index + 1));
                });
                markAsChanged(); // Mark as changed when removing an option
            });

            // Track changes in input fields
            $(document).on('input', 'input[name="poll_options[]"]', function () {
                markAsChanged(); // Mark as changed when editing an option
            });

            // Warn the user if they try to leave without saving changes
            $(window).on('beforeunload', function () {
                if (formChanged) {
                    return '<?php _e("You have unsaved changes. Are you sure you want to leave?", "hostingbuzpoll"); ?>';
                }
            });

            // If the post is saved, reset the form change flag
            $('#post').on('submit', function () {
                formChanged = false;
            });
        });
    </script>
    <?php
}

// Save the poll options
function hbuz_save_poll_options($post_id)
{
    // Check if our nonce is set and verify it
    if (!isset($_POST['hbuz_poll_options_nonce']) || !wp_verify_nonce($_POST['hbuz_poll_options_nonce'], 'hbuz_save_poll_options')) {
        return;
    }

    // Check for autosave and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if ('hostingbuzpoll' !== get_post_type($post_id)) {
        return;
    }

    // Sanitize and save the poll options
    if (isset($_POST['poll_options'])) {
        $poll_options = array_map('sanitize_text_field', $_POST['poll_options']);
        update_post_meta($post_id, '_poll_options', $poll_options);
    }
}
add_action('save_post', 'hbuz_save_poll_options');



// Add a "Results" submenu under the "Polls" menu
function hostingbuzpoll_add_results_submenu()
{
    add_submenu_page(
        'edit.php?post_type=hostingbuzpoll', // Parent menu (Polls)
        __('Poll Results', 'hostingbuzpoll'), // Page title
        __('Results', 'hostingbuzpoll'), // Menu title
        'manage_options', // Capability
        'poll-results', // Menu slug
        'hostingbuzpoll_display_results_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'hostingbuzpoll_add_results_submenu');

// Callback function to display the results page
function hostingbuzpoll_display_results_page()
{
    // Check if the user has the required capability
    if (!current_user_can('manage_options')) {
        return;
    }


    // Get all polls from the custom post type 'hostingbuzpoll'
    $args = array(
        'post_type' => 'hostingbuzpoll',
        'posts_per_page' => -1, // Get all polls
    );
    $poll_query = new WP_Query($args);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Poll Results', 'hostingbuzpoll') . '</h1>';

    if ($poll_query->have_posts()) {
        echo '<div class="hbuz-poll-results-container">';

        // Loop through each poll and display its data in a card-like UI
        while ($poll_query->have_posts()) {
            $poll_query->the_post();
            $poll_id = get_the_ID();
            $poll_title = get_the_title();
            $poll_options = get_post_meta($poll_id, '_poll_options', true);
            $poll_votes = get_post_meta($poll_id, '_poll_votes', true) ?: [];
            $total_votes = array_sum($poll_votes);
            $chart_id = 'chart-' . $poll_id;

            echo '<div class="poll-card" onclick="togglePollContent(' . esc_js($poll_id) . ')">';
            echo '<h2>' . esc_html($poll_title) . '</h2>';
            echo '<div class="poll-card-content" id="poll-content-' . esc_attr($poll_id) . '">';

            if (is_array($poll_options) && count($poll_options) > 0) {
                echo '<ul class="poll-options-list">';
                foreach ($poll_options as $index => $option) {
                    $votes_for_option = isset($poll_votes[$index]) ? $poll_votes[$index] : 0;
                    $percentage = ($total_votes > 0) ? round(($votes_for_option / $total_votes) * 100) : 0;
                    echo '<li>
                            <strong>' . esc_html($option) . '</strong>: ' . esc_html($votes_for_option) . ' (' . esc_html($percentage) . '%)
                            <div class="poll-progress-bar">
                                <div class="poll-progress-bar-inner" style="--progress-width: ' . esc_attr($percentage) . '%;"></div>
                            </div>
                          </li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . esc_html__('No options available', 'hostingbuzpoll') . '</p>';
            }

            echo '<p><strong>' . esc_html__('Total Votes:', 'hostingbuzpoll') . '</strong> ' . esc_html($total_votes) . '</p>';


            echo '</div>'; // End of poll-card-content
            echo '</div>'; // End of poll card
        }

        echo '</div>'; // End of hbuz-poll-results-container
    } else {
        echo '<p>' . esc_html__('No polls found.', 'hostingbuzpoll') . '</p>';
    }

    echo '</div>';

    // Reset post data
    wp_reset_postdata();
}



// Add a meta box to show the poll shortcode in the Poll edit screen
function hbuz_add_shortcode_metabox()
{
    add_meta_box(
        'hbuz_poll_shortcode',                    // Meta box ID
        __('Poll Shortcode', 'hostingbuzpoll'),    // Meta box title
        'hbuz_poll_shortcode_callback',           // Callback function
        'hostingbuzpoll',                         // Post type
        'side',                                   // Context (where the box appears: 'normal', 'side', or 'advanced')
        'high'                                    // Priority
    );
}
add_action('add_meta_boxes', 'hbuz_add_shortcode_metabox');

// Callback function to display the shortcode in the meta box
function hbuz_poll_shortcode_callback($post)
{
    $shortcode = '[hostingbuz_poll id="' . esc_attr($post->ID) . '"]';
    echo '<p>' . __('Use the shortcode below to embed this poll anywhere on your site:', 'hostingbuzpoll') . '</p>';
    echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly style="width: 100%; text-align: center;" />';
}


// Add custom columns to Poll post type admin list
function hbuz_poll_add_custom_columns($columns)
{
    // Insert new columns after the title column
    $columns['poll_options'] = __('Total Poll', 'hostingbuzpoll');
    $columns['total_votes'] = __('Total Votes', 'hostingbuzpoll');
    $columns['poll_shortcode'] = __('Shortcode', 'hostingbuzpoll'); // New column for shortcode

    return $columns;
}
add_filter('manage_hostingbuzpoll_posts_columns', 'hbuz_poll_add_custom_columns');

// Populate custom columns with poll data
function hbuz_poll_custom_column_content($column, $post_id)
{
    switch ($column) {
        case 'poll_options':
            // Fetch poll options
            $poll_options = get_post_meta($post_id, '_poll_options', true);
            if (is_array($poll_options)) {
                echo esc_html(count($poll_options)) . ' ' . __('Options', 'hostingbuzpoll');
            } else {
                echo __('No options found', 'hostingbuzpoll');
            }
            break;

        case 'total_votes':
            // Fetch total votes
            $total_votes = get_post_meta($post_id, '_total_votes', true);
            echo esc_html($total_votes ?: '0') . ' ' . __('Votes', 'hostingbuzpoll');
            break;

        case 'poll_shortcode':
            // Display the shortcode for the poll
            $shortcode = '[hostingbuz_poll id="' . esc_attr($post_id) . '"]';
            echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly style="width: 100%; text-align: center;" onclick="this.select();" />';
            break;
    }
}
add_action('manage_hostingbuzpoll_posts_custom_column', 'hbuz_poll_custom_column_content', 10, 2);

// Make custom columns sortable
function hbuz_poll_custom_columns_sortable($columns)
{
    $columns['total_votes'] = 'total_votes';
    return $columns;
}
add_filter('manage_edit-hostingbuzpoll_sortable_columns', 'hbuz_poll_custom_columns_sortable');

// Set the query for sorting by custom columns
function hbuz_poll_sortable_columns_orderby($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($orderby = $query->get('orderby')) {
        switch ($orderby) {
            case 'total_votes':
                $query->set('meta_key', '_total_votes');
                $query->set('orderby', 'meta_value_num');
                break;
        }
    }
}
add_action('pre_get_posts', 'hbuz_poll_sortable_columns_orderby');


// Register AJAX action for voting

add_action('wp_ajax_submit_poll_vote', 'submit_poll_vote');
add_action('wp_ajax_nopriv_submit_poll_vote', 'submit_poll_vote');

function submit_poll_vote()
{
    check_ajax_referer('poll_nonce', 'security');

    $poll_id = isset($_POST['poll_id']) ? intval($_POST['poll_id']) : 0;
    $option = isset($_POST['option']) ? intval($_POST['option']) : -1;

    if (!$poll_id || $option < 0) {
        wp_send_json_error(['message' => __('Invalid poll or option.', 'hostingbuzpoll')]);
        return;
    }

    // Fetch current votes
    $poll_votes = get_post_meta($poll_id, '_poll_votes', true) ?: [];
    $poll_votes[$option] = isset($poll_votes[$option]) ? $poll_votes[$option] + 1 : 1;
    update_post_meta($poll_id, '_poll_votes', $poll_votes);

    // Update total votes
    $total_votes = array_sum($poll_votes);
    update_post_meta($poll_id, '_total_votes', $total_votes);

    // Prepare the response with updated vote counts
    wp_send_json_success([
        'total_votes' => $total_votes,
        'poll_votes' => $poll_votes
    ]);
}


// Hook into the template system to load custom template
function hostingbuzpoll_load_single_template($template)
{
    global $post;

    // Check if the post type is 'hostingbuzpoll' and the template file exists
    if ($post->post_type == 'hostingbuzpoll' && file_exists(plugin_dir_path(__FILE__) . 'single-hostingbuzpoll.php')) {
        $template = plugin_dir_path(__FILE__) . 'single-hostingbuzpoll.php';
    }

    return $template;
}
add_filter('single_template', 'hostingbuzpoll_load_single_template');


// Hook to add plugin template to the WordPress template system
function hostingbuzpoll_add_template($templates)
{
    $templates['poll-template.php'] = 'Poll Template';
    return $templates;
}
add_filter('theme_page_templates', 'hostingbuzpoll_add_template');

// Hook to load the template if selected in the page attributes
function hostingbuzpoll_load_plugin_template($template)
{
    if (get_page_template_slug() === 'poll-template.php') {
        $plugin_template = plugin_dir_path(__FILE__) . 'poll-template.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'hostingbuzpoll_load_plugin_template');



function hostingbuzpoll_template($archive_template) {
    if (is_post_type_archive('hostingbuzpoll')) {
        $archive_template = plugin_dir_path(__FILE__) . 'archive-hostingbuzpoll.php';
    }
    return $archive_template;
}
add_filter('archive_template', 'hostingbuzpoll_template');



// online voting poll
require_once plugin_dir_path(__FILE__) . 'poll-setting-option.php';
require_once plugin_dir_path(__FILE__) . 'poll-shortcode.php';