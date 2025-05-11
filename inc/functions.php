<?php

/**
 * Add your custom php code here
 */

 /*

 add_shortcode( 'directorist_tag_description', function ( $atts ) {
    ob_start();

	// Define default attributes
    $atts = shortcode_atts(
        array(
            'slug' => '',
            'taxonomy' => '',
        ), 
        $atts
    );

	$taxonomy_name = '';
	$taxonomy_slug = '';

	if( ! empty( $atts[ 'slug' ] ) && ! empty( $atts[ 'taxonomy' ] ) ){

		$taxonomy_name = $atts[ 'taxonomy' ];
		switch( $atts[ 'taxonomy' ] )
		{
			case 'location':
				$taxonomy_name = ATBDP_LOCATION;
				break;
			case 'category':
				$taxonomy_name = ATBDP_CATEGORY;
				break;
			case 'tag':
				$taxonomy_name = ATBDP_TAGS;
				break;
		}
		$taxonomy_slug = $atts[ 'slug' ];
	}
	else
	{
		if ( get_query_var( 'atbdp_category' ) ) {
			$taxonomy_name = ATBDP_CATEGORY;
			$taxonomy_slug = get_query_var( 'atbdp_category' );
		}

		if ( get_query_var( 'atbdp_location' ) ) {
			$taxonomy_name = ATBDP_LOCATION;
			$taxonomy_slug = get_query_var( 'atbdp_location' );
		}

		if ( get_query_var( 'atbdp_tag' ) ) {
			$taxonomy_name = ATBDP_TAGS;
			$taxonomy_slug = get_query_var( 'atbdp_tag' );
		}

	}

    if ( $taxonomy_slug && $taxonomy_name ) {
        $term = get_term_by( 'slug', $taxonomy_slug, $taxonomy_name );
        if ( $term ) {
            $output = tag_description( $term->term_id );
            if ( $output ) {
	?>
                <div class="directorist-tag-description">
					<?php echo  $output; ?>
				</div>
	<?php
            }
        }
    }

    return ob_get_clean();
} );

*/

/*

add_action( 'directorist_taxonomy_category_before_content', function(){
    // show category description
    $term = get_queried_object();
    $term_id = $term->term_id;
    $tag_description = tag_description( $term_id );
    if ( $tag_description ) {
        echo '<div class="directorist-tag-description">';
        echo $tag_description;
        echo '</div>';
    }
} );

*/