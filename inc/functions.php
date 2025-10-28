<?php

/**
 * Add your custom php code here
 */

function bbd_get_option_data()
{
    $options = [];
    $options['script_debugging'] = get_directorist_option('script_debugging', DIRECTORIST_LOAD_MIN_FILES, true);
    $options['google_map_id'] = get_directorist_option('google_map_id', '');
    return $options;
}

/**
 * Add custom field in the admin panel directorist settings page
 */

add_filter( 'atbdp_listing_type_settings_field_list', function( $fields ){
    $fields['google_map_id'] = [
        'label' => __('Google Map ID', 'directorist-custom-code'),
        'type' => 'text',
        'value' => '',
    ];
    return $fields;
}, 20 );

add_filter( 'atbdp_listing_settings_map_sections', function( $sections ){
    $sections['map_settings']['fields'][] = 'google_map_id';
    return $sections;
} );