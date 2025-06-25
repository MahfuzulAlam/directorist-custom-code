<?php

/**
 * Add your custom php code here
 */


add_action( 'atbdp_listing_inserted', function( $listing_id ) {
    // Sanitize and validate min_price
    if ( isset( $_POST['min_price'] ) && $_POST['min_price'] !== '' ) {
        $min_price = sanitize_text_field( $_POST['min_price'] );
        if ( is_numeric( $min_price ) ) {
            update_post_meta( $listing_id, '_min_price', $min_price );
        }
    }

    // Sanitize and validate max_price
    if ( isset( $_POST['max_price'] ) && $_POST['max_price'] !== '' ) {
        $max_price = sanitize_text_field( $_POST['max_price'] );
        if ( is_numeric( $max_price ) ) {
            update_post_meta( $listing_id, '_max_price', $max_price );
        }
    }
} );


add_action( 'atbdp_listing_updated', function( $listing_id ) {
    // Sanitize and validate min_price
    if ( isset( $_POST['min_price'] ) && $_POST['min_price'] !== '' ) {
        $min_price = sanitize_text_field( $_POST['min_price'] );
        if ( is_numeric( $min_price ) ) {
            update_post_meta( $listing_id, '_min_price', $min_price );
        }
    }

    // Sanitize and validate max_price
    if ( isset( $_POST['max_price'] ) && $_POST['max_price'] !== '' ) {
        $max_price = sanitize_text_field( $_POST['max_price'] );
        if ( is_numeric( $max_price ) ) {
            update_post_meta( $listing_id, '_max_price', $max_price );
        }
    }
} );


add_action( 'save_post', function( $post_id ) {
	// if ( ! is_admin() ) return;
    // // Check post type
    // if ( get_post_type( $post_id ) !== ATBDP_POST_TYPE ) {
    //     return;
    // }

    // // Avoid autosave and revisions
    // if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    //     return;
    // }

	if ( isset( $_POST['min_price'] ) && $_POST['min_price'] !== '' ) {
		$min_price = sanitize_text_field( $_POST['min_price'] );
		if ( is_numeric( $min_price ) ) {
			update_post_meta( $post_id, '_min_price', $min_price );
		}
	}

	// Sanitize and validate max_price
	if ( isset( $_POST['max_price'] ) && $_POST['max_price'] !== '' ) {
		$max_price = sanitize_text_field( $_POST['max_price'] );
		if ( is_numeric( $max_price ) ) {
			update_post_meta( $post_id, '_max_price', $max_price );
		}
	}
} );

add_filter( 'atbdp_form_preset_widgets', function ( $widgets ){
    $widgets[ 'pricing' ][ 'pricing_type' ][ 'options' ][] = ['value' => 'price_unit_range', 'label' => 'Price Unit Range'];
    return $widgets;
} );