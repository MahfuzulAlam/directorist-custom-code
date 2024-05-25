<?php

/**
 * Add your custom php code here
 */


add_filter( 'atbdp_listing_type_settings_field_list', function( $business_fields )
{
    $business_fields['atbh_import_time_format'] = [
        'label'   => __( 'Import Time Format', 'directorist-business-hours' ),
        'type'    => 'select',
        'value'   => '24',
        'options' => [
            [
                'value' => '12',
                'label' => __( '12 Hours', 'directorist-business-hours' ),
            ],
            [
                'value' => '24',
                'label' => __( '24 Hours', 'directorist-business-hours' ),
            ],
        ],
    ];

    return $business_fields;
} );

add_filter( 'atbdp_extension_settings_submenu', function( $submenu )
{
    $submenu['business_hours']['sections']['general_section']['fields'] = ['open_badge_text', 'close_badge_text', 'business_hour_title', 'text247', 'atbh_time_format', 'atbh_import_time_format',  'timezone', 'atbh_display_single_listing', 'cache_plugin_compatibility' ];
    return $submenu;
} );