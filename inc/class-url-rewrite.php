<?php

/**
 * Class Directorist_Category_Url_Rewrite
 *
 * Description: This class will run all the functions
 * to rewrite the locacategorytion url.
 *
 * @package DirectoristCustomCode
 */

Class Directorist_Category_Url_Rewrite {

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        add_action( 'init', [$this, 'category_routes'], 10, 0 );
        add_filter( 'term_link', [$this, 'term_link'], 20, 3 );
        add_filter( 'atbdp_single_category', [$this, 'atbdp_single_category'], 20, 4 );

        // Category Page Title
        add_filter( 'wpwax_theme_page_title', [$this, 'category_page_title'] );
        add_filter( 'wp_title', [$this, 'category_page_title'], 20, 1 );

        add_shortcode( 'directorist_custom_category_archive', [$this, 'directorist_custom_category_archive'] );
    }

    /**
     * category_routes
     *
     * @return void
     */
    public function category_routes() {
        // ROUTES
        add_rewrite_rule( '^category/([^/]*)/([^/]*)/?', 'index.php?pagename=category&main=$matches[1]&sub=$matches[2]', 'top' );
        add_rewrite_rule( '^category/([^/]*)/?', 'index.php?pagename=category&main=$matches[1]', 'top' );

        // REWRITE TAGS
        add_rewrite_tag( '%main%', '([^&]+)' );
        add_rewrite_tag( '%sub%', '([^&]+)' );
    }

    /**
     * directorist_custom_category_archive
     *
     * Shortcode to display the Category Page with new routes
     *
     * @return void
     */
    public function directorist_custom_category_archive() {
        ob_start();
        $main = get_query_var( 'main', '' );
        $sub = get_query_var( 'sub', '' );
        $category_term = false;
        $category_term = '';

        if ( !empty( $main ) ) {
            $category_term = term_exists( $main, ATBDP_CATEGORY );
            $category = $main;
        }

        if ( !empty( $sub ) ) {
            $category_term = term_exists( $sub, ATBDP_CATEGORY );
            $category = $sub;
        }

        if ( $category_term ) {
            echo do_shortcode( '[directorist_all_listing category="' . $category . '"]' );
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

        if ( ATBDP_CATEGORY === $taxonomy ) {
            $parents = get_term_parents_list( $term->term_id, ATBDP_CATEGORY, ['inclusive' => false, 'format' => 'slug', 'link' => false] );
            $url = get_permalink( get_page_by_path( 'category' ) ) . $parents . $term->slug;
        }

        return $url;
    }

    /**
     * atbdp_single_category
     *
     * Manipulate the catgeory link
     *
     * @param  string $link
     * @param  int $page_id
     * @param  object $term
     * @param  string $directory_type
     * @return string
     */
    public function atbdp_single_category( $link, $page_id, $term, $directory_type ) {
        $parents = get_term_parents_list( $term->term_id, ATBDP_CATEGORY, ['inclusive' => false, 'format' => 'slug', 'link' => false] );
        $link = get_permalink( get_page_by_path( 'category' ) ) . $parents . $term->slug;
        return $link;
    }

    /**
     * category_page_title
     *
     * Category Page Title
     *
     * @param  mixed $title
     * @return void
     */
    public function category_page_title( $title ) {
        $pagename = get_query_var( 'pagename', '' );

        if ( $pagename === 'category' ):

            $main = get_query_var( 'main', '' );
            $sub = get_query_var( 'sub', '' );

            $category_slug = '';

            if ( !empty( $sub ) ) {
                $category_slug = $sub;
            }

            if ( !empty( $main ) ) {
                $category_slug = $main;
            }

            if ( !empty( $category_slug ) ):
                $category = get_term_by( 'slug', $category_slug, ATBDP_CATEGORY );

                if ( $category && !empty( $category->name ) ) {
                    $title = $category->name;
                    remove_action( 'wp_head', '_wp_render_title_tag', 1 );
                }

            endif;

        endif;

        return $title;
    }

}

new Directorist_Category_Url_Rewrite();