<?php

/**
 * Add your custom php code here
 */


 add_filter( 'rank_math/opengraph/facebook/image', function( $image ){
    if( is_page( 'author-profile' ) ):
        $author_username = get_query_var( 'author_id' );
        // Get user data by username
        $user = get_user_by( 'login', $author_username );

        if ($user) {
            // Get the avatar URL
            $avatar_url = get_avatar_url($user->ID, array('size' => 150));

            // Output or use the $avatar_url as needed
            if( $avatar_url ) return $avatar_url;
        }
    endif;
    return $image;
} );

add_filter( 'rank_math/opengraph/twitter/image', function( $image ){
    if( is_page( 'author-profile' ) ):
        $author_username = get_query_var( 'author_id' );
        // Get user data by username
        $user = get_user_by( 'login', $author_username );

        if ($user) {
            // Get the avatar URL
            $avatar_url = get_avatar_url($user->ID, array('size' => 150));

            // Output or use the $avatar_url as needed
            if( $avatar_url ) return $avatar_url;
        }
    endif;
    return $image;
} );

add_filter( 'wpseo_opengraph_image', function( $image ) {
    if( is_page( 'author-profile' ) ):
        $author_username = get_query_var( 'author_id' );
        // Get user data by username
        $user = get_user_by( 'login', $author_username );

        if ($user) {
            // Get the avatar URL
            $avatar_url = get_avatar_url($user->ID, array('size' => 150));

            // Output or use the $avatar_url as needed
            if( $avatar_url ) return $avatar_url;
        }
    endif;
    return $image;
} );