<?php
/**
 * Custom extension hooks and helpers.
 *
 * Add project-specific PHP code here.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_filter( 'atbdp_form_preset_widgets', function( $widgets ){
	$widgets['pricing']['options']['suffix'] = [
		'type' => 'text',
		'label' => 'Suffix',
		'value' => '',
		'description' => 'Suffix for the pricing field. e.g. per day, per night, per hour, etc.',
		'show_if' => [
			'where'      => "self.pricing_type",
			'compare'    => 'or',
			'conditions' => [
				['key' => 'value', 'compare' => '=', 'value' => 'both'],
				['key' => 'value', 'compare' => '=', 'value' => 'price_unit'],
			],
		],
	];
	return $widgets;
} );



/**
 * Add your custom php code here
 */

 add_action( 'atbdp_listing_inserted', function( $listing_id ) {
    if ( isset( $_POST['price_suffix'] ) && $_POST['price_suffix'] !== '' ) {
        $price_suffix = sanitize_text_field( $_POST['price_suffix'] );
        update_post_meta( $listing_id, '_price_suffix', $price_suffix );
    }
} );


add_action( 'atbdp_listing_updated', function( $listing_id ) {
    if ( isset( $_POST['price_suffix'] ) && $_POST['price_suffix'] !== '' ) {
        $price_suffix = sanitize_text_field( $_POST['price_suffix'] );
        update_post_meta( $listing_id, '_price_suffix', $price_suffix );
    }
} );


add_action( 'save_post', function( $post_id ) {
	if ( ! is_admin() ) return;
    // // Check post type
    if ( get_post_type( $post_id ) !== ATBDP_POST_TYPE ) {
        return;
    }

    // // Avoid autosave and revisions
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

	if ( isset( $_POST['price_suffix'] ) && $_POST['price_suffix'] !== '' ) {
		$price_suffix = sanitize_text_field( $_POST['price_suffix'] );
		update_post_meta( $post_id, '_price_suffix', $price_suffix );
	}
} );