<?php

class Directorist_Rank_Math_Metas
{
    public function __construct()
    {
        add_filter( 'rank_math/frontend/title', [ $this, 'meta_title' ] );
        add_filter( 'rank_math/frontend/description', [ $this, 'meta_description' ] );
        add_filter( 'rank_math/frontend/canonical', [ $this, 'meta_canonical' ]);
        add_filter( 'rank_math/opengraph/facebook/image',  [ $this, 'meta_image' ] );
        add_filter( 'rank_math/opengraph/twitter/image',  [ $this, 'meta_image' ] );
    }

    public function meta_title( $title )
    {
        if( is_page( 'author-profile' ) ):
            $author_username = get_query_var( 'author_id' );
            // Get user data by username
            $user = get_user_by( 'login', $author_username );
    
            if ($user) {
                return $user->data->display_name;
            }
        endif;
        return $title;
    }

    public function meta_description( $description )
    {
        if( is_page( 'author-profile' ) ):
            $author_username = get_query_var( 'author_id' );
            // Get user data by username
            $user = get_user_by( 'login', $author_username );
            if ($user) {
                $description = get_user_meta( $user->data->ID, 'description', true );
                if( $description ) return $description;
            }
        endif;
        return $description;
    }

    public function meta_canonical( $description )
    {
        if( is_page( 'author-profile' ) ):
            $author_username = get_query_var( 'author_id' );
            // Get user data by username
            $user = get_user_by( 'login', $author_username );
            if ($user) {
                return esc_url( ATBDP_Permalink::get_user_profile_page_link( $user->data->ID ) );
            }
        endif;
        return $description;
    }

    public function meta_image( $image )
    {
        if( is_page( 'author-profile' ) ):
            $author_username = get_query_var( 'author_id' );
            // Get user data by username
            $user = get_user_by( 'login', $author_username );
    
            if ($user) {
                // Get the avatar URL
                $pro_pic_url = $this->user_image_src( $user );
    
                // Output or use the $avatar_url as needed
                if( $pro_pic_url ) return $pro_pic_url;
            }
        endif;
        return $image;
    }

    public static function user_image_src( $user ) {
		$id           = $user->ID;
		$image_id  	  = get_user_meta( $id, 'pro_pic', true );
		$image_data   = wp_get_attachment_image_src( $image_id, 'medium' );
		$image_src    = $image_data ? $image_data[0] : get_avatar_url( $id );
		return $image_src ? $image_src : '';
	}

}

if( directorist_is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
    new Directorist_Rank_Math_Metas();
}