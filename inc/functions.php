<?php

/**
 * Add your custom php code here
 */


//add_filter( 'the_content', 'custom_override_page_content' );

 function custom_override_page_content( $content ) {
         return '<h2>This content is overridden!</h2>';
 
     //return $content;
 }

 //add_filter( 'get_the_archive_title', 'custom_taxonomy_archive_title' );

function custom_taxonomy_archive_title( $title ) {
    if ( is_tax( 'at_biz_dir-location' ) ) {
        $term = get_queried_object();
        if ( $term ) {
            // Change the title however you like
            $title = 'Listings in the: ' . $term->name;
        }
    }

    return $title;
}

//add_filter( 'archive_template', 'load_custom_tax_archive_template' );

function load_custom_tax_archive_template( $archive_template ) {
    if ( is_tax( 'at_biz_dir-location' ) ) {
        return DIRECTORIST_REFINED_TAXONOMY_DIR . 'templates/taxonomy/archive/location.php';
    }
    return $archive_template;
}


// add_filter( 'atbdp_single_category', function($link){
//     $link = '';
//     return $link;
// } );


function custom_dir_get_term_link( $term, $taxonomy = '' ) {
	global $wp_rewrite;

	if ( ! is_object( $term ) ) {
		if ( is_int( $term ) ) {
			$term = get_term( $term, $taxonomy );
		} else {
			$term = get_term_by( 'slug', $term, $taxonomy );
		}
	}

	if ( ! is_object( $term ) ) {
		$term = new WP_Error( 'invalid_term', __( 'Empty Term.' ) );
	}

	if ( is_wp_error( $term ) ) {
		return $term;
	}

	$taxonomy = $term->taxonomy;

	$termlink = $wp_rewrite->get_extra_permastruct( $taxonomy );

	$slug = $term->slug;
	$t    = get_taxonomy( $taxonomy );

	if ( empty( $termlink ) ) {
		if ( 'category' === $taxonomy ) {
			$termlink = '?cat=' . $term->term_id;
		} elseif ( $t->query_var ) {
			$termlink = "?$t->query_var=$slug";
		} else {
			$termlink = "?taxonomy=$taxonomy&term=$slug";
		}
		$termlink = home_url( $termlink );
	} else {
		if ( ! empty( $t->rewrite['hierarchical'] ) ) {
			$hierarchical_slugs = array();
			$ancestors          = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
			foreach ( (array) $ancestors as $ancestor ) {
				$ancestor_term        = get_term( $ancestor, $taxonomy );
				$hierarchical_slugs[] = $ancestor_term->slug;
			}
			$hierarchical_slugs   = array_reverse( $hierarchical_slugs );
			$hierarchical_slugs[] = $slug;
			$termlink             = str_replace( "%$taxonomy%", implode( '/', $hierarchical_slugs ), $termlink );
		} else {
			$termlink = str_replace( "%$taxonomy%", $slug, $termlink );
		}
		$termlink = home_url( user_trailingslashit( $termlink, 'category' ) );
	}

	return $termlink;
}


add_filter( 'term_link', function( $url, $term, $taxonomy ) {
    // Categories
    if ( ATBDP_CATEGORY == $taxonomy || ATBDP_LOCATION == $taxonomy ) {
        global $wp_rewrite;

        if ( ! is_object( $term ) ) {
            if ( is_int( $term ) ) {
                $term = get_term( $term, $taxonomy );
            } else {
                $term = get_term_by( 'slug', $term, $taxonomy );
            }
        }

        if ( ! is_object( $term ) ) {
            $term = new WP_Error( 'invalid_term', __( 'Empty Term.' ) );
        }

        if ( is_wp_error( $term ) ) {
            return $term;
        }

        $taxonomy = $term->taxonomy;

        $termlink = $wp_rewrite->get_extra_permastruct( $taxonomy );

        $slug = $term->slug;
	    $t    = get_taxonomy( $taxonomy );

        if ( empty( $termlink ) ) {
            if ( 'category' === $taxonomy ) {
                $termlink = '?cat=' . $term->term_id;
            } elseif ( $t->query_var ) {
                $termlink = "?$t->query_var=$slug";
            } else {
                $termlink = "?taxonomy=$taxonomy&term=$slug";
            }
            $termlink = home_url( $termlink );
        } else {
            if ( ! empty( $t->rewrite['hierarchical'] ) ) {
                $hierarchical_slugs = array();
                $ancestors          = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
                foreach ( (array) $ancestors as $ancestor ) {
                    $ancestor_term        = get_term( $ancestor, $taxonomy );
                    $hierarchical_slugs[] = $ancestor_term->slug;
                }
                $hierarchical_slugs   = array_reverse( $hierarchical_slugs );
                $hierarchical_slugs[] = $slug;
                $termlink             = str_replace( "%$taxonomy%", implode( '/', $hierarchical_slugs ), $termlink );
            } else {
                $termlink = str_replace( "%$taxonomy%", $slug, $termlink );
            }
            $termlink = home_url( user_trailingslashit( $termlink, 'category' ) );
        }

        return $termlink;
    }
    return $url;
}, 20, 3);


add_filter('atbdp_single_category', function($link, $page_id, $term, $directory_type){
    $link = custom_dir_get_term_link( (int) $term->term_id, $term->taxonomy );
	return $link;
}, 10, 4);