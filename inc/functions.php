<?php

/**
 * Add your custom php code here
 */

add_filter('directorist_search_field_template', function ($template, $field_data) {
    if ('author_search' === $field_data['widget_name']) {
        directorist_custom_get_template('search-form/author', $field_data);
    }
    return $template;
}, 10, 2);

/**
 * Get Template
 */
function directorist_custom_get_template($template_file, $args = array())
{
    if (is_array($args)) {
        extract($args);
    }
    $data = $args;

    if (isset($args['form'])) $listing_form = $args['form'];

    $file = DIRECTORIST_AUTHOR_SEARCH_DIR . '/templates/' . $template_file . '.php';

    if (directorist_template_exists($template_file)) {
        include $file;
    }
}

function directorist_template_exists($template_file)
{
    $file = DIRECTORIST_AUTHOR_SEARCH_DIR . '/templates/' . $template_file . '.php';

    if (file_exists($file)) {
        return true;
    } else {
        return false;
    }
}


add_filter('atbdp_listing_search_query_argument', function ($args) {
    if (isset($_REQUEST['custom_field']['author_search']) && ! empty($_REQUEST['custom_field']['author_search'])) {
        $args['author'] = $_REQUEST['custom_field']['author_search'];
    }
    if (isset($args['meta_query']) && count($args['meta_query']) > 0) {
        foreach ($args['meta_query'] as $key => $meta_query) {
            if (isset($meta_query['key']) && $meta_query['key'] == '_author_search') {
                unset($args['meta_query'][$key]);
            }
        }
    }
    return $args;
});
