<?php

/**
 * Add your custom php code here
 */

function directorist_custom_code_modify_category_remove_query_params() {

    $directorist_search_page = get_directorist_option('search_result_page');

    if ( $directorist_search_page && is_page( $directorist_search_page ) ) {
        // Get the current URL
        $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Parse the query string into an associative array
        parse_str( parse_url( $current_url, PHP_URL_QUERY ), $query_params );

        // Define an array of query parameters to remove
        $params_to_remove = ['in_cat_parent', 'in_cat_child'];

        if ( isset( $query_params['in_cat_parent'] ) || isset( $query_params['in_cat_child'] ) ) {

            // Check and remove the specified query parameters
            foreach ( $params_to_remove as $param ) {
                if ( isset( $query_params[$param] ) ) {
                    unset( $query_params[$param] );
                }
            }

            // Reconstruct the query string without the removed parameters
            $new_query_string = http_build_query( $query_params );

            // Construct the new URL without the removed parameters
            $new_url = strtok( $current_url, '?' ) . '?' . $new_query_string;

            // Redirect to the new URL without the removed parameters
            wp_redirect( $new_url, 301 );
            exit();
        }

    }

}

add_action( 'template_redirect', 'directorist_custom_code_modify_category_remove_query_params' );
