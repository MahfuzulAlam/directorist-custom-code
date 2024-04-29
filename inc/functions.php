<?php

/**
 * Add your custom php code here
 */


add_filter( 'atbdp_reg_settings_sections', function( $sections ) {
    $sections[ 'user_type' ][ 'fields' ] = [
        'display_user_type',
        'user_type_author',
        'user_type_user',
    ];
    return $sections;
} );

add_filter( 'atbdp_listing_type_settings_field_list' , function( $fields ) {
    $fields['user_type_author'] = [
        'label'         => __('Author Label', 'directorist'),
        'type'          => 'text',
        'value'         => 'I am an author',
        'show-if' => [
            'where' => "display_user_type",
            'conditions' => [
                ['key' => 'value', 'compare' => '=', 'value' => true],
            ],
        ],
    ];
    $fields[ 'user_type_user'] = [
        'label'         => __('User Label', 'directorist'),
        'type'          => 'text',
        'value'         => 'I am a user',
        'show-if' => [
            'where' => "display_user_type",
            'conditions' => [
                ['key' => 'value', 'compare' => '=', 'value' => true],
            ],
        ],
    ];
    return $fields;
} );

add_filter( 'directorist_plan_remaining', function( $data ){
    $data = [
        'regular'  => isset( $data['regular'] ) ? $data['regular'] : 0,
        'featured' => isset( $data['featured'] ) ? $data['featured'] : 0,
    ];
    return $data;
} );