<?php

/**
 * Add your custom php code here
 */

add_action('init', 'best_us_lawyers_custom_routes', 10, 0);

function best_us_lawyers_custom_routes()
{
    // RV CAMPGROUND ROUTES
    add_rewrite_rule('^lawyers/([^/]*)/([^/]*)/?', 'index.php?pagename=lawyers&location=$matches[1]&category=$matches[2]', 'top');
    add_rewrite_rule('^lawyers/([^/]*)/?', 'index.php?pagename=lawyers&location=$matches[1]', 'top');

    // REWRITE TAGS
    add_rewrite_tag('%category%', '([^&]+)');
    add_rewrite_tag('%location%', '([^&]+)');
}

add_shortcode('best_us_lawyer_taxonomy', function () {
    ob_start();
    $location = get_query_var('location', '');
    $category = get_query_var('category', '');
    $location_term = $category_term = false;

    if (!empty($location)) {
        $location_term = term_exists($location, ATBDP_LOCATION);
    }
    if (!empty($category)) {
        $category_term = term_exists($category, ATBDP_CATEGORY);
    }

    if ($location_term && $category_term) {
        echo do_shortcode('[directorist_all_listing directory_type="general" location="' . $location . '" category="' . $category . '"]');
    } elseif ($location_term) {
        echo do_shortcode('[directorist_all_listing directory_type="general" location="' . $location . '"]');
    } elseif (!empty($location)) {
        $category_term = term_exists($location, ATBDP_CATEGORY);
        if ($category_term) {
            echo do_shortcode('[directorist_all_listing directory_type="general" category="' . $category . '"]');
        } else {
            echo do_shortcode('[directorist_all_listing directory_type="general"]');
        }
    } else {
        echo do_shortcode('[directorist_all_listing directory_type="general"]');
    }
    return ob_get_clean();
});


add_filter('term_link', function ($url, $term, $taxonomy) {
    // Categories
    if (ATBDP_CATEGORY === $taxonomy) {
        $url = ATBDP_Permalink::atbdp_get_category_page($term);
    }

    // Location
    if (ATBDP_LOCATION === $taxonomy) {
        $url = ATBDP_Permalink::atbdp_get_location_page($term);
    }

    // Tag
    if (ATBDP_TAGS === $taxonomy) {
        $url = ATBDP_Permalink::atbdp_get_tag_page($term);
    }
    return $url;
}, 20, 3);


add_filter('body_class', function ($classes) {
    if (is_page('add-listing')) {
        $classes[] = 'directorist-add-listing';
        if (isset($_GET['directory_type']) && !empty($_GET['directory_type'])) {
            $classes[] = 'directorist-type-' . $_GET['directory_type'];
        }
    }
    return $classes;
});

add_filter('atbdp_single_listing_content_widgets', function ($widgets) {
    $widgets['url'] = [
        'options' => [
            'icon' => [
                'type'  => 'icon',
                'label' => __('Icon', 'directorist'),
                'value' => 'las la-link',
            ],
            'anchor' => [
                'type'  => 'text',
                'label' => __('Anchor Text', 'directorist'),
                'value' => 'Visit Website',
            ],
        ]
    ];
    return $widgets;
});

// add_filter('comment_text', function ($comment_text) {
//     $comment_text = str_replace("explicit", "****", $comment_text);
//     return $comment_text;
// });

add_filter('comment_text', function ($comment_text) {
    if (!empty($comment_text)) {
        $wordArray = ['explicit', 'text2', 'text3'];
        $words = explode(' ', $comment_text);
        foreach ($words as &$word) {
            if (in_array(strtolower($word), $wordArray)) {
                $word = str_repeat('*', strlen($word));
            }
        }
        $comment_text = implode(' ', $words);
    }
    return $comment_text;
});

add_action("wp_footer", function () {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".directorist-contact-owner-form button[type=submit]").on("click", function(e) {
                e.preventDefault();
                console.log('sending');
                var form = $(this).parents(".directorist-contact-owner-form");
                var submit_button = form.find('button[type="submit"]');
                var status_area = form.find('.directorist-contact-message-display'); // Show loading message

                var msg = '<div class="directorist-alert"><i class="fas fa-circle-notch fa-spin"></i> ' + directorist.waiting_msg + ' </div>';
                status_area.html(msg);
                var name = form.find('input[name="atbdp-contact-name"]');
                var contact_email = form.find('input[name="atbdp-contact-email"]');
                var message = form.find('textarea[name="atbdp-contact-message"]');
                var post_id = form.find('input[name="atbdp-post-id"]');
                var listing_email = form.find('input[name="atbdp-listing-email"]'); // Post via AJAX
                var contact_phone = form.find('input[name="atbdp-contact-phone"]').val(); // Post via AJAX

                if (contact_phone !== '') {
                    messageLine = message.val() + ' \r\n ' + 'Contact Phone: ' + contact_phone;
                } else {
                    messageLine = message.val();
                }
                var data = {
                    'action': 'atbdp_public_send_contact_email',
                    'post_id': post_id.val(),
                    'name': name.val(),
                    'email': contact_email.val(),
                    'listing_email': listing_email.val(),
                    'message': messageLine,
                    'directorist_nonce': directorist.directorist_nonce
                };
                submit_button.prop('disabled', true);
                $.post(directorist.ajaxurl, data, function(response) {
                    submit_button.prop('disabled', false);

                    if (1 == response.error) {
                        atbdp_contact_submitted = false; // Show error message

                        var msg = '<div class="atbdp-alert alert-danger-light"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>';
                        status_area.html(msg);
                    } else {
                        name.val('');
                        message.val('');
                        contact_email.val(''); // Show success message

                        var msg = '<div class="atbdp-alert alert-success-light"><i class="fas fa-check-circle"></i> ' + response.message + '</div>';
                        status_area.html(msg);
                    }

                    setTimeout(function() {
                        status_area.html('');
                    }, 5000);
                }, 'json');
            });
            $('#atbdp-contact-form,#atbdp-contact-form-widget').removeAttr('novalidate');
        });
    </script>
<?php
});

//if (is_plugin_active("seo-by-rank-math/rank-math.php")) {
// add_filter('rank_math/frontend/canonical', function ($canonical) {
//     $term_slug = false;
//     $taxonomy = '';

//     if (get_query_var('atbdp_category')) {
//         $term_slug = get_query_var('atbdp_category');
//         $taxonomy = ATBDP_CATEGORY;
//     }
//     if (get_query_var('atbdp_location')) {
//         $term_slug = get_query_var('atbdp_location');
//         $taxonomy = ATBDP_LOCATION;
//     }

//     if ($term_slug) {
//         $canonical = get_term_link($term_slug, $taxonomy);
//     }
//     return $canonical;
// });
//}


add_action('wp_footer', function () {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.mark_sold .directorist-checkbox-absent-input__label').text("Mark as Rest");
        });
    </script>
<?php
});
