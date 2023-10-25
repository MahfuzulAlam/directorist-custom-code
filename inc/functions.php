<?php

/**
 * Add your custom php code here
 */

function mpp_bbd_inspect_scripts() {

    if ( !is_admin() ) {
        wp_dequeue_script( 'directorist-google-map' );
        wp_deregister_script( 'directorist-google-map' );
    }

}

add_action( 'wp_print_scripts', 'mpp_bbd_inspect_scripts' );

function mpp_custom_google_map_scripts() {
    //wp_enqueue_style('custom-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');
    wp_enqueue_script( 'google-map-api' );
    wp_enqueue_script( 'directorist-markerclusterer' );
    wp_enqueue_script( 'bbd-custom-google', DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/google-map.js', [ 'google-map-api' ], DIRECTORIST_CUSTOM_MAP_VERSION, true );
    wp_localize_script( 'bbd-custom-google', 'directorist_options', bbd_get_option_data() );
}

add_action( 'wp_enqueue_scripts', 'mpp_custom_google_map_scripts', 0 );

function bbd_get_option_data() {
    $options = [];
    $options['script_debugging'] = get_directorist_option( 'script_debugging', DIRECTORIST_LOAD_MIN_FILES, true );
    return $options;
}
