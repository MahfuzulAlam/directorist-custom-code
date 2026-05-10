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


/**
 * Load the pricing widget config from directory term meta `submission_form_fields`.
 *
 * Stored as serialized term meta; WordPress returns an array via get_term_meta().
 *
 * @param int $listing_id Listing post ID. Use 0 to use the default directory (if available).
 *
 * @return array The `fields['pricing']` array (labels, pricing_type, modules, etc.) or empty array.
 */
if ( ! function_exists( 'directorist_custom_get_pricing_field_data' ) ) {
	function directorist_custom_get_pricing_field_data( $listing_id = 0 ) {
		$listing_id   = absint( $listing_id );
		$directory_id = 0;

		if ( 0 === $listing_id ) {
			if ( function_exists( 'directorist_get_default_directory' ) ) {
				$directory_id = (int) directorist_get_default_directory( 'id' );
			}
		} elseif ( function_exists( 'directorist_get_listings_directory_type' ) ) {
			$directory_id = (int) directorist_get_listings_directory_type( $listing_id );
		}

		if ( 0 === $directory_id ) {
			return array();
		}

		$submission_form = get_term_meta( $directory_id, 'submission_form_fields', true );

		if ( ! is_array( $submission_form ) ) {
			return array();
		}

		$fields = isset( $submission_form['fields'] ) ? $submission_form['fields'] : array();

		if ( ! is_array( $fields ) || empty( $fields['pricing'] ) || ! is_array( $fields['pricing'] ) ) {
			return array();
		}

		return $fields['pricing'];
	}
}