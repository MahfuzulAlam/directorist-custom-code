<?php

/**
 * Add your custom php code here
 */


 add_filter('directorist_search_form_widgets', function($widgets){
	$widgets['other_widgets']['widgets']['radius_search']['options']['max_radius_distance'] = [
		'type'  => 'range',
		'label' => __( 'Max Radius Distance', 'directorist' ),
		'min'   => 0,
		'max'   => 1000,
		'value' => 0,
	];
	return $widgets;
});


function directorist_custom_bbd_inspect_scripts() {

    if ( !is_admin() ) {
        wp_dequeue_script( 'directorist-search-form' );
        wp_deregister_script( 'directorist-search-form' );
    }

}

add_action( 'wp_print_scripts', 'directorist_custom_bbd_inspect_scripts' );

function directorist_custom_search_form_script() {
    $search_form_fields = Directorist\Helper::get_directory_type_term_data(get_the_ID(), 'search_form_fields');
    $max_radius_distance = '';
    if($search_form_fields){
        if(isset($search_form_fields['fields']['radius_search']['max_radius_distance'])) 
            $max_radius_distance = $search_form_fields['fields']['radius_search']['max_radius_distance'];
    }
    wp_enqueue_script( 'bbd-custom-script', DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/search-form.js', '', '1.0.0', true );
    wp_localize_script( 'bbd-custom-script', 'max_radius_distance', (int)$max_radius_distance );
}

add_action( 'wp_enqueue_scripts', 'directorist_custom_search_form_script', 0 );