<?php

/**
 * Add your custom php code here
 */

add_action( 'wp_ajax_directorist_get_category_based_tags', 'directorist_custom_code_get_category_based_tags' );
add_action( 'wp_ajax_nopriv_directorist_get_category_based_tags', 'directorist_custom_code_get_category_based_tags' );

function directorist_custom_code_get_category_based_tags() {

    if ( !directorist_verify_nonce( 'nonce' ) ) {
        wp_send_json(
            [
                'search_form' => __( 'Something went wrong, please try again.', 'directorist' ),
            ]
        );
    }

    $tags = [];
    $tags_html = '';

    $cat_id = !empty( $_POST['cat_id'] ) ? sanitize_key( $_POST['cat_id'] ) : '';

    if ( $cat_id ) {
        $listings = directorist_custom_code_get_listings_from_category( $cat_id );

        if ( $listings && count( $listings ) > 0 ) {
            $tags = directorist_custom_code_get_tags_from_listings( $listings );
        }

    }

    if ( $tags && count( $tags ) > 0 ) {
        ob_start();
        directorist_custom_code_prepare_tag_field_html( $tags );
        $search_form = ob_get_clean();
        //$tags_html = '<input type="text" name="hello"/>';
    }

    wp_send_json(
        [
            'tags_html' => $search_form,
        ]
    );
}

function directorist_custom_code_get_listings_from_category( $cat_id ) {
    $args = [
        'post_type'      => ATBDP_POST_TYPE,
        'tax_query'      => [
            [
                'taxonomy' => 'at_biz_dir-category',
                'fields'   => 'id',
                'terms'    => $cat_id, // The term ID you want to query
            ],
        ],
        'posts_per_page' => -1, // Retrieve all matching posts
        'fields' => 'ids',
    ];

    $query = new WP_Query( $args );

    if ( $query ) {
        return $query->posts;
    } else {
        return false;
    }

}

function directorist_custom_code_get_tags_from_listings( $listings ) {
    $terms_for_posts = [];

    foreach ( $listings as $listing ) {
        $post_terms = wp_get_post_terms( $listing, ATBDP_TAGS );

        if ( !empty( $post_terms ) ) {

            foreach ( $post_terms as $post_term ) {

                if ( !array_key_exists( $post_term->term_id, $terms_for_posts ) ) {
                    $terms_for_posts[$post_term->term_id] = $post_term;
                }

            }

        }

    }

    return $terms_for_posts;

}

function directorist_custom_code_prepare_tag_field_html( $tag_terms ) {

    if ( $tag_terms && count( $tag_terms ) > 0 ) {
        $rand = rand();

        foreach ( $tag_terms as $term ) {
            $id = $rand . $term->term_id;
            ?>

			<div class="directorist-checkbox directorist-checkbox-primary">
				<input type="checkbox" name="in_tag[]" value="<?php echo esc_attr( $term->term_id ); ?>" id="<?php echo esc_attr( $id ); ?>">
				<label for="<?php echo esc_attr( $id ); ?>" class="directorist-checkbox__label"><?php echo esc_html( $term->name ); ?></label>
			</div>

			<?php
}

    }

}
