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


add_filter( 'atbdp_form_custom_widgets', function( $widgets ){
	$widgets['url']['options']['link_text'] = [
		'type' => 'text',
		'label' => 'Link Text',
		'value' => '',
		'description' => __('Optional text to display as the link, instead of the URL.', 'directorist-custom-code'),
	];
	$widgets['url']['options']['label_placeholder'] = [
		'type' => 'text',
		'label' => 'Label Placeholder',
		'value' => 'Url Label',
		'description' => __('Placeholder text for the URL label input field.', 'directorist-custom-code'),
	];
	return $widgets;
} );


/**
 * Add your custom php code here
 */

 add_action( 'atbdp_listing_inserted', function( $listing_id ) {
    // Save any custom fields ending with _label
    foreach ( $_POST as $key => $value ) {
        if ( substr( $key, -6 ) === '_label' ) {
            $meta_key = '_' . $key;
            $field_value = sanitize_text_field( $value );
            update_post_meta( $listing_id, $meta_key, $field_value );
        }
    }
} );


add_action( 'atbdp_listing_updated', function( $listing_id ) {
    // Save any custom fields ending with _label
    foreach ( $_POST as $key => $value ) {
        if ( substr( $key, -6 ) === '_label' ) {
            $meta_key = '_' . $key;
            $field_value = sanitize_text_field( $value );
            update_post_meta( $listing_id, $meta_key, $field_value );
        }
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

	// Save any custom fields ending with _label
	foreach ( $_POST as $key => $value ) {
		if ( substr( $key, -6 ) === '_label' ) {
			$meta_key = '_' . $key;
			$field_value = sanitize_text_field( $value );
			update_post_meta( $post_id, $meta_key, $field_value );
		}
	}
} );
