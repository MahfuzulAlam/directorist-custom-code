<?php

/**
 * Class Directorist_Location_Url_Rewrite
 *
 * Description: This class will run all the functions
 * to rewrite the location url.
 *
 * @package DirectoristCustomCode
 */

Class Directorist_Location_Url_Rewrite {

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        add_action( 'init', [$this, 'location_routes'], 10, 0 );
        add_filter( 'term_link', [$this, 'term_link'], 20, 3 );
        add_filter( 'atbdp_single_location', [$this, 'atbdp_single_location'], 20, 4 );

        add_shortcode( 'directorist_custom_location_archive', [$this, 'directorist_custom_location_archive'] );
    }

    /**
     * location_routes
     *
     * @return void
     */
    public function location_routes() {
        // ROUTES
        add_rewrite_rule( '^location/([^/]*)/([^/]*)/?', 'index.php?pagename=location&state=$matches[1]&city=$matches[2]', 'top' );
        add_rewrite_rule( '^location/([^/]*)/?', 'index.php?pagename=location&state=$matches[1]', 'top' );

        // REWRITE TAGS
        add_rewrite_tag( '%state%', '([^&]+)' );
        add_rewrite_tag( '%city%', '([^&]+)' );
    }

    /**
     * directorist_custom_location_archive
     *
     * Shortcode to display the Location Page with new routes
     *
     * @return void
     */
    public function directorist_custom_location_archive() {
        ob_start();
        $state = get_query_var( 'state', '' );
        $city = get_query_var( 'city', '' );
        $location_term = false;
        $location = '';

        if ( !empty( $state ) ) {
            $location_term = term_exists( $state, ATBDP_LOCATION );
            $location = $state;
        }

        if ( !empty( $city ) ) {
            $location_term = term_exists( $city, ATBDP_LOCATION );
            $location = $city;
        }

        if ( $location_term ) {
            echo do_shortcode( '[directorist_all_listing location="' . $location . '"]' );
        } else {
            echo '<p>No listings found!</p>';
        }

        return ob_get_clean();
    }

    /**
     * term_link
     *
     * Redirect URL
     *
     * @param  string $url
     * @param  object $term
     * @param  string $taxonomy
     * @return string
     */
    public function term_link( $url, $term, $taxonomy ) {

        if ( ATBDP_LOCATION === $taxonomy ) {
            $parents = get_term_parents_list( $term->term_id, ATBDP_LOCATION, ['inclusive' => false, 'format' => 'slug', 'link' => false] );
            $url = get_permalink( get_page_by_path( 'location' ) ) . $parents . $term->slug;
        }

        return $url;
    }

    /**
     * atbdp_single_location
     *
     * Manipulate the location link
     *
     * @param  string $link
     * @param  int $page_id
     * @param  object $term
     * @param  string $directory_type
     * @return string
     */
    public function atbdp_single_location( $link, $page_id, $term, $directory_type ) {
        $parents = get_term_parents_list( $term->term_id, ATBDP_LOCATION, ['inclusive' => false, 'format' => 'slug', 'link' => false] );
        $link = get_permalink( get_page_by_path( 'location' ) ) . $parents . $term->slug;
        return $link;
    }

}

new Directorist_Location_Url_Rewrite();