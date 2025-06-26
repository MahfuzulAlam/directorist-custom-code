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
	if ( ! is_admin() ) return;
    // // Check post type
    if ( get_post_type( $post_id ) !== ATBDP_POST_TYPE ) {
        return;
    }

    // // Avoid autosave and revisions
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

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

add_filter( 'atbdp_search_listings_meta_queries', function ( $meta_query ) {
    $price = [];

    if( $meta_query && count( $meta_query ) > 0 ):
        foreach(  $meta_query as $key => $value ):
            if( isset( $value[ 'key' ] ) && $value[ 'key' ] == "_price" ) { 
                $price = $meta_query[ $key ];
                unset( $meta_query[ $key ] ); 
            }
        endforeach;
    endif;

    if( $price ):
        $meta_query[ 'pricing' ] = [
            'relation' => 'OR'
        ];

        $meta_query[ 'pricing' ][ 'price' ] = $price;
        
        $price['key'] = '_min_price';
        $meta_query[ 'pricing' ][ 'min_price' ] = $price;

        $price['key'] = '_max_price';
        $meta_query[ 'pricing' ][ 'max_price' ] = $price;

    endif;
    
    return $meta_query;
} );

add_action( 'admin_head', function(){
    ?>
    <style>
        .directory_price_unit_range_field
        {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }
        .directory_price_unit_range_field .directory_pricing_field
        {
            min-width: 20%;
            width: 20%;
        }
    </style>
    <?php
} );