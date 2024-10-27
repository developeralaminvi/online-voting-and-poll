<?php global $hbuzpoll ?>
<?php
/*
Template Name: Poll Template
*/

// Exit if accessed directly
if (!defined(constant_name: 'ABSPATH')) {
    exit;
}

get_header();


?>

<style type="text/css">
    .hbuz-poll-content h2 a {
        color: <?php echo esc_attr($hbuzpoll['poll_title_color'] ?? '#000'); ?> !important;
        font-size: <?php echo esc_attr($hbuzpoll['poll_font_size'] ?? '18'); ?>px !important;
        transition: all 0.3s;
    }

    .hbuz-poll-content h2 {
        margin: 10px 0;
        line-height: 30px;
    }

    .hbuz-poll-content h2 a:hover {
        color: <?php echo esc_attr($hbuzpoll['poll_title_hover_color'] ?? '#ff0000'); ?> !important;
    }

    .hbuz-poll-date span {
        color: <?php echo esc_attr($hbuzpoll['poll_date_color'] ?? '#888'); ?> !important;
        font-size: <?php echo esc_attr($hbuzpoll['poll_date_font_size'] ?? '14'); ?>px !important;
        margin-bottom: 5px;
        display: inline-block;
    }

    .hbuz-poll-download-btn {
        cursor: pointer;
        color: <?php echo esc_attr($hbuzpoll['poll_download_color'] ?? '#000'); ?> !important;
        font-size: <?php echo esc_attr($hbuzpoll['poll_download_icon_size'] ?? '20'); ?>px !important;
    }

    .hbuz-poll-download-btn:hover {
        color: <?php echo esc_attr($hbuzpoll['poll_download_hover_color'] ?? '#ff0000'); ?> !important;
    }

    .hbuz-progress-option-list {
        font-size: <?php echo esc_attr($hbuzpoll['vote_font_size'] ?? '14'); ?>px !important;
        color: <?php echo esc_attr($hbuzpoll['vote_text_color'] ?? '#333'); ?> !important;
    }

    .hbuz-progress-vot-option-list {
        font-size: <?php echo esc_attr($hbuzpoll['vote_font_size'] ?? '14'); ?>px !important;
        color: <?php echo esc_attr($hbuzpoll['voting_number_color'] ?? '#000'); ?> !important;
    }

    .hbuz-progress-option {
        top: <?php echo esc_attr($hbuzpoll['vote_space'] ?? '0'); ?>px !important;
    }

    .progress-bar {
        border: <?php echo esc_attr($hbuzpoll['progress_bar_border_type'] ?? 'solid'); ?> 
                <?php echo esc_attr($hbuzpoll['progress_bar_border_color'] ?? '#ccc'); ?> !important;
        border-width: <?php echo esc_attr($hbuzpoll['progress_bar_border_width'] ?? '1'); ?>px !important;
    }

    .hbuz-poll-total-votes {
        font-size: <?php echo esc_attr($hbuzpoll['total_vote_text_size'] ?? '16'); ?>px !important;
        color: <?php echo esc_attr($hbuzpoll['total_vote_text_color'] ?? '#000'); ?> !important;
    }

    .hbuz-poll-item {
        background-color: <?php echo esc_attr($hbuzpoll['poll_body_bg_color'] ?? '#f9f9f9'); ?> !important;
    }

    .progress-hbuz {
        background-color: <?php echo esc_attr($hbuzpoll['progress_bar_bg_color'] ?? '#4caf50'); ?> !important;
    }
</style>


<div class="container">

    <div class="poll-grid-container row">

        <?php
        // WP_Query to get polls from the custom post type 'hostingbuzpoll'
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'hostingbuzpoll',
            'posts_per_page' => 12, // Display all polls
            'paged' => $paged,
        );

        $poll_query = new WP_Query($args);

        if ($poll_query->have_posts()):
            while ($poll_query->have_posts()):
                $poll_query->the_post();
                $poll_id = get_the_ID();
                $poll_options = get_post_meta($poll_id, '_poll_options', true);
                $total_votes = get_post_meta($poll_id, '_total_votes', true) ?: 0;
                ?>

                <div class="col-12 col-md-4 mt-3">
                    <div class="hbuz-poll-item" id="poll-<?php echo esc_attr($poll_id); ?>"
                        data-permalink="<?php echo get_permalink($poll_id); ?>">
                        <div class="hbuz-poll-header">
                            <div class="hbuz-poll-date">
                                <span><i class="fa-regular fa-clock"></i> <?php echo esc_html(get_the_date()); ?></span>
                            </div>

                            <div class="hbuz-poll-download-btn" data-poll-id="<?php echo esc_attr($poll_id); ?>">
                                <i class="fa-solid fa-download"></i>
                            </div>
                        </div>

                        <div class="hbuz-poll-thumbnail">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="hbuz-poll-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="hbuz-poll-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>

                        <div class="hbuz-poll-options" id="poll-<?php echo esc_attr($poll_id); ?>">
                            <?php if (is_array($poll_options) && count($poll_options) > 0): ?>
                                <form class="hbuz-poll-vote-form" method="POST">
                                    <?php foreach ($poll_options as $index => $option): ?>
                                        <?php
                                        $poll_votes = get_post_meta($poll_id, '_poll_votes', true) ?: [];
                                        $votes_for_option = isset($poll_votes[$index]) ? $poll_votes[$index] : 0;
                                        $percentage = ($total_votes > 0) ? round(($votes_for_option / $total_votes) * 100) : 0;
                                        $unique_option_id = $poll_id . '_' . $index;
                                        ?>
                                        <div class="hbuz-progress-form">
                                            <label>
                                                <input type="radio" name="poll_option" value="<?php echo esc_attr($index); ?>" required>
                                            </label>
                                        </div>

                                        <div class="hbuz-progress">
                                            <div class="progress-bar">
                                                <div class="hbuz-progress-option">
                                                    <div class="hbuz-progress-option-list"><?php echo esc_html($option); ?></div>
                                                    <div class="hbuz-progress-vot-option-list"
                                                        id="progress-form-<?php echo esc_attr($unique_option_id); ?>">
                                                        (<?php echo esc_html($votes_for_option); ?> votes,
                                                        <?php echo esc_html($percentage); ?>%)
                                                    </div>
                                                </div>
                                                <div class="progress-hbuz" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
                                            </div>
                                        </div>
                                        <br />
                                    <?php endforeach; ?>
                                    <?php wp_nonce_field('poll_nonce', 'poll_nonce_field'); ?>
                                </form>
                            <?php endif; ?>

                            <div class="hbuz-poll-total-votes">
                                <?php esc_html_e('Total Votes:', 'hostingbuzpoll'); ?>         <?php echo esc_html($total_votes); ?>
                            </div>

                            <!-- Share Icons Section -->
                            <div class="hbuz-poll-share">
                                <a href="#" class="hbuz-poll-share-facebook" target="_blank" aria-label="Share on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="hbuz-poll-share-messenger" target="_blank" aria-label="Share on Messenger">
                                    <i class="fab fa-facebook-messenger"></i>
                                </a>
                                <a href="#" class="hbuz-poll-share-whatsapp" target="_blank" aria-label="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="hbuz-poll-share-twitter" target="_blank" aria-label="Share on Twitter">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                                <a href="#" class="hbuz-poll-share-linkedin" target="_blank" aria-label="Share on LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a class="hbuz-poll-copy-link-icon" aria-label="Copy Poll Link">
                                    <i class="fas fa-link"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile;
            wp_reset_postdata();

        else: ?>
            <p><?php esc_html_e('No polls found.', 'hostingbuzpoll'); ?></p>
        <?php endif; ?>

    </div>
    <?php

    $pagination = paginate_links(array(
        'mid_size' => 2,
        'prev_text' => '<i class="fa fa-arrow-left"></i>',
        'next_text' => '<i class="fa fa-arrow-right"></i>',
        'total' => $poll_query->max_num_pages,
        'type' => 'array'
    ));

    if (is_array($pagination)) {
        echo '<div class="hbuz-pagination">';
        echo '<ul class="pagination">';
        foreach ($pagination as $page) {
            echo '<li>' . $page . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }


    ?>
</div>


<script>
    jQuery(document).ready(function ($) {
        $('.hbuz-poll-vote-form input[name="poll_option"]').on('change', function () {
            var selectedOption = $(this).val();
            var pollId = $(this).closest('.hbuz-poll-item').attr('id').split('-')[1];
            var nonce = $('#poll-' + pollId + ' input[name="poll_nonce_field"]').val();
            // Call the function to show poll results
            showPollResults(pollId);

            function showPollResults(pollId) {
                // Show results only for the selected poll
                var selectedPoll = document.querySelector("#poll-" + pollId);

                // Select all progress vote option lists within the selected poll
                var selectedResults = selectedPoll.querySelectorAll(".hbuz-progress-vot-option-list");

                // Show all results for the selected poll
                selectedResults.forEach(function (result) {
                    result.style.display = "block"; // Set to block to show results
                });

                // Optionally show progress bars as well
                var progressHbuz = selectedPoll.querySelectorAll(".progress-hbuz");
                progressHbuz.forEach(function (result) {
                    result.style.display = "block"; // Show progress bars
                });

                // Disable options after voting
                $('#poll-' + pollId + ' .hbuz-poll-options input').attr('disabled', true);
            }

            // Show the selected option's progress votes
            $('#progress-form-' + pollId + '_' + selectedOption).show();

            // AJAX request to submit the vote
            var nonce = $('#poll-' + pollId + ' input[name="poll_nonce_field"]').val(); // Get nonce for security
        });

    });
</script>

<script>
    jQuery(document).ready(function ($) {
        // Check if user has already voted based on IP
        function hasUserVoted(pollId) {
            return localStorage.getItem('voted_' + pollId) === 'true';
        }

        $('.hbuz-poll-vote-form').each(function () {
            var pollId = $(this).closest('.hbuz-poll-item').attr('id').split('-')[1];

            // Disable voting if the user has already voted
            if (hasUserVoted(pollId)) {
                $(this).find('input[name="poll_option"]').prop('disabled', true);
                // Show all results for the selected poll
                $('#poll-' + pollId + ' .hbuz-progress-vot-option-list').show();

                $('#poll-' + pollId + ' .progress-hbuz').show();

            }
        });

        // Handle voting action when an option is clicked
        $('.hbuz-poll-vote-form input[name="poll_option"]').on('change', function () {
            var selectedOption = $(this).val(); // Get the value of the selected option
            var pollId = $(this).closest('.hbuz-poll-item').attr('id').split('-')[1]; // Get poll ID
            var nonce = $('#poll-' + pollId + ' input[name="poll_nonce_field"]').val(); // Get nonce for security
            var ipAddress = "<?php echo $_SERVER['REMOTE_ADDR']; ?>"; // Get user IP

            // Disable all options to prevent multiple votes
            $('#poll-' + pollId + ' .hbuz-poll-options input').attr('disabled', true);
            $('#poll-' + pollId + ' .loading-message').text('Processing your vote...').show(); // Show loading message

            // AJAX request to submit the vote
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'submit_poll_vote',
                    poll_id: pollId,
                    option: selectedOption,
                    security: nonce,
                    ip_address: ipAddress // Send IP address for validation
                },
                beforeSend: function () {
                    // Show loading message
                    $('#poll-' + pollId + ' .loading-message').show();
                },
                success: function (response) {
                    if (response.success) {
                        // Store in localStorage to indicate the user has voted
                        localStorage.setItem('voted_' + pollId, 'true');
                        updatePollResults(pollId, response.data); // Update the results on success
                    } else {
                        alert(response.data.message); // Show error message
                    }
                },
                error: function () {
                    alert('An error occurred while submitting your vote.');
                },
                complete: function () {
                    // Hide loading message after processing
                    $('#poll-' + pollId + ' .loading-message').hide(); // Hide loading message
                }
            });
        });

        function updatePollResults(pollId, data) {
            // Update total votes display
            $('#poll-' + pollId + ' .hbuz-poll-total-votes').text('Total Votes: ' + data.total_votes);

            // Update individual poll option vote counts
            var pollVotes = data.poll_votes;
            $('#poll-' + pollId + ' .hbuz-progress-form').each(function (index) {
                var votes = pollVotes[index] || 0; // Get votes for this option
                var percentage = (data.total_votes > 0) ? Math.round((votes / data.total_votes) * 100) : 0;
                $(this).find('.hbuz-progress-vot-option-list').text('(' + votes + ' votes, ' + percentage + '%)'); // Update text
                $(this).next('.hbuz-progress').find('.progress-hbuz').css('width', percentage + '%'); // Update progress bar width
            });

            // Show all results for the selected poll
            $('#poll-' + pollId + ' .hbuz-progress-vot-option-list').show();
        }
    });
</script>



<?php get_footer();