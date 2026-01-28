<?php

/**
 * Add your custom php code here
 */


 add_filter( 'atbdp_listing_search_query_argument', function( $args ){
    if( isset( $_REQUEST['in_locs'] ) && !empty( $_REQUEST['in_locs'] ) ){
        $args['tax_query'][] = [
            'taxonomy' => ATBDP_LOCATION,
            'field' => 'term_id',
            'terms' => $_REQUEST['in_locs'],
            'include_children' => true,
        ];
    }
    if( isset( $_REQUEST['in_cats'] ) && !empty( $_REQUEST['in_cats'] ) ){
        $args['tax_query'][] = [
            'taxonomy' => ATBDP_CATEGORY,
            'field' => 'term_id',
            'terms' => $_REQUEST['in_cats'],
            'include_children' => true,
        ];
    }
    return $args;
} );

add_filter( 'directorist_all_listings_query_arguments', function( $args ){
    if( isset( $_REQUEST['in_loc'] ) && !empty( $_REQUEST['in_loc'] ) ){
        // Check if find any "," comma separated values
        if( strpos( $_REQUEST['in_loc'], ',' ) !== false ){
            // fund and unset if already same tax query in place
            if( isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) && count( $args['tax_query'] ) > 0 ){
                foreach( $args['tax_query'] as $key => $value ){
                    if( isset( $value['taxonomy'] ) && $value['taxonomy'] == ATBDP_LOCATION ){
                        unset( $args['tax_query'][$key] );
                    }
                }
            }
            $in_locs = explode( ',', $_REQUEST['in_loc'] );
            $args['tax_query'][] = [
                'taxonomy' => ATBDP_LOCATION,
                'field' => 'term_id',
                'terms' => $in_locs,
                'include_children' => true,
            ];
        }
    }
    if( isset( $_REQUEST['in_cat'] ) && !empty( $_REQUEST['in_cat'] ) ){
        // Check if find any "," comma separated values
        if( strpos( $_REQUEST['in_cat'], ',' ) !== false ){
            // fund and unset if already same tax query in place
            if( isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) && count( $args['tax_query'] ) > 0 ){
                foreach( $args['tax_query'] as $key => $value ){
                    if( isset( $value['taxonomy'] ) && $value['taxonomy'] == ATBDP_CATEGORY ){
                        unset( $args['tax_query'][$key] );
                    }
                }
            }
            $in_cats = explode( ',', $_REQUEST['in_cat'] );
            $args['tax_query'][] = [
                'taxonomy' => ATBDP_CATEGORY,
                'field' => 'term_id',
                'terms' => $in_cats,
                'include_children' => true,
            ];
        }
    }
    return $args;
} );