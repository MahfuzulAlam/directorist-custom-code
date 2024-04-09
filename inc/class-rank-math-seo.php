<?php

class Directorist_Rank_Math_Metas
{
    public function __construct()
    {
        add_filter( 'rank_math/frontend/title', [ $this, 'meta_title' ], 30 );
        add_filter( 'rank_math/frontend/description', [ $this, 'meta_description' ], 30 );
        add_filter( 'rank_math/frontend/canonical', [ $this, 'meta_canonical' ], 30);
        add_filter( 'rank_math/opengraph/facebook/image',  [ $this, 'meta_image' ], 30 );
        add_filter( 'rank_math/opengraph/twitter/image',  [ $this, 'meta_image' ], 30 );
    }
	
	// extract_user_id
	public function extract_user_data( $user_id = '' ) {
		$user_id = urldecode($user_id); //decode the URL to remove encoded spaces, special characters

		if ( is_string( $user_id ) && ! empty( $user_id ) ) {
			return get_user_by( 'login', $user_id );
		}

		return false;
	}

    // check if author profile page
    public function is_author_profile_page()
    {
        $page = get_directorist_option( 'author_profile_page' );
        if( $page && is_page( $page ) ){
            return true;
        }
    }

    public function is_directorist_taxonomy_page()
    {
        if( atbdp_is_page( 'single_category' ) )
        {
            return [ 'slug' => 'atbdp_category', 'name' => ATBDP_CATEGORY ];
        }
        elseif( atbdp_is_page( 'single_location' ) )
        {
            return [ 'slug' => 'atbdp_location', 'name' => ATBDP_LOCATION ];
        }
        elseif( atbdp_is_page( 'single_tag' ) )
        {
            return [ 'slug' => 'atbdp_tag', 'name' => ATBDP_TAGS ];
        }
        else
        {
            return false;
        }
    }

    public function meta_title( $title )
    {
        $taxonomy = $this->is_directorist_taxonomy_page();
        if( $taxonomy )
        {
            $term_slug = get_query_var( $taxonomy[ 'slug' ] );
            if( $term_slug )
            {
                $term = get_term_by( 'slug', $term_slug, $taxonomy[ 'name' ] );
                if( $term ){
                    $custom_title =  get_term_meta( $term->term_id, 'rank_math_title', true );
                    if( $custom_title ) return RankMath\Helper::replace_vars( str_replace( "%term%", $term->name, $custom_title ) );
                }
            }
        }
        else if( $this->is_author_profile_page() )
        {
            $user = $this->extract_user_data( get_query_var( 'author_id' ) );
    
            if ($user) {
                return $user->data->display_name;
            }
        }
        return $title;
    }

    public function meta_description( $description )
    {
        $taxonomy = $this->is_directorist_taxonomy_page();
        if( $taxonomy )
        {
            $term_slug = get_query_var( $taxonomy[ 'slug' ] );
            if( $term_slug )
            {
                $term = get_term_by( 'slug', $term_slug, $taxonomy[ 'name' ] );
                if( $term ){
                    $custom_description =  get_term_meta( $term->term_id, 'rank_math_description', true );
                    if( $custom_description ) return RankMath\Helper::replace_vars( str_replace( [ "%term%", "%term_description%" ], [ $term->name, $term->description ], $custom_description ) );
                }
            }
        }
        else if( $this->is_author_profile_page() )
        {
            $user = $this->extract_user_data( get_query_var( 'author_id' ) );
    
            if ($user) {
                $description = get_user_meta( $user->data->ID, 'description', true );
                if( $description ) return $description;
            }
        }
        return $description;
    }

    public function meta_canonical( $description )
    {
        if( $this->is_author_profile_page() ):
            $user = $this->extract_user_data( get_query_var( 'author_id' ) );
    
            if ($user) {
                return esc_url( ATBDP_Permalink::get_user_profile_page_link( $user->data->ID ) );
            }
        endif;
        return $description;
    }

    public function meta_image( $image )
    {
        if( $this->is_author_profile_page() ):
            $user = $this->extract_user_data( get_query_var( 'author_id' ) );
    
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