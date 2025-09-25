<?php

/**
 * Add your custom php code here
 */

add_filter('atbdp_single_listing_content_widgets', function($widgets){
    $widgets['excerpt'] = [
        'options' => [
            'icon' => [
                'type'  => 'icon',
                'label' => 'Icon',
                'value' => 'las la-code',
            ],
        ]
    ];
    return $widgets;
});


add_filter( 'directorist_single_item_template', function($template, $field_data){
    if ( isset($field_data['widget_name']) && 'excerpt' === $field_data['widget_name'] && !empty($field_data['value']) ) {
        echo '<div class="directorist-single-info directorist-listing-details__text">'.$field_data['value'].'</div>';
    }
    return $template;
}, 10, 2 );