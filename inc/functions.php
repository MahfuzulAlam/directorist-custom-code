<?php

/**
 * Add your custom php code here
 */


add_action( 'directorist_loop_grid_info_before_excerpt', function( $listing ){

    if( is_singular('at_biz_dir') ) return;

    $listing_id = $listing->loop['id'];

    // Include claim listing button template from the template folder
    $field_data = array(
        'label' => 'Claim Listing',
        'icon' => 'fas fa-flag',
        'custom_block_classes' => 'directorist-claim-listing-block'
    );
    
    // Set the global post data for the template
    global $post;
    $original_post = $post;
    $post = get_post($listing_id);
    setup_postdata($post);
    
    // Include the custom claim listing button template
    include plugin_dir_path(__FILE__) . '../templates/custom/claim-listing-button.php';
    
    // Restore original post data
    wp_reset_postdata();
    $post = $original_post;

} );