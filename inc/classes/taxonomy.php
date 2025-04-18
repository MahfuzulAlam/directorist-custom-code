<?php
/**
 * Directorist Taxonomy Declaration
 *
 * @package Directorist_Refined_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Directorist_Taxonomy_Declaration
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_filter( 'atbdp_categories_settings_sections', array($this, 'add_legacy_taxonomy_section') );
        add_filter( 'atbdp_listing_type_settings_field_list', array($this, 'add_legacy_taxonomy_fields') );

        add_action( 'init', array($this, 'legacy_taxonomy_settings') );
        add_action( 'term_link', array($this, 'get_legacy_term_link'), 10, 3 );
    }

    /**
     * Add the legacy taxonomy settings
     */
    public function legacy_taxonomy_settings()
    {
        $location_enabled = get_directorist_option( 'enable_legacy_location', false );
        if ( $location_enabled ) {
            add_action( 'init', array($this, 'override_location_taxonomy'), 20 );
            add_filter( 'template_include', array($this,'override_location_taxonomy_template') );
            add_filter( 'atbdp_single_location', array($this, 'custom_taxonomy_archive_link'), 10, 4 );
            add_filter( 'get_the_archive_title', array($this, 'location_archive_title'), 10, 3 );
        }

        $category_enabled = get_directorist_option( 'enable_legacy_category', false );
        if ( $category_enabled ) {
            add_action( 'init', array($this, 'override_category_taxonomy'), 20 );
            add_filter( 'template_include', array($this,'override_category_taxonomy_template') );
            add_filter( 'atbdp_single_category', array($this, 'custom_taxonomy_archive_link'), 10, 4 );
            add_filter( 'get_the_archive_title', array($this, 'category_archive_title'), 10, 3 );
            add_filter( 'directorist_category_page_redirection_enabled', '__return_false' );
        }
    }

    /**
     * Add the legacy taxonomy section to the settings
     */
    public function add_legacy_taxonomy_section( $sections )
    {
        $new_section = [
            'legacy_taxonomy' => [
                'title'       => __('Legacy Taxonomy Settings', 'directorist'),
                'fields'      => [
                    'enable_legacy_location',
                    'legacy_location_slug',
                    'enable_legacy_category',
                    'legacy_category_slug',
                ],
            ],
        ];
        return $new_section + $sections;
    }

    /**
     * Add the legacy taxonomy fields to the settings
     */
    public function add_legacy_taxonomy_fields( $fields )
    {
        $new_field = [
            'enable_legacy_location' => [
                'label'         => __('Enable Legacy Location', 'directorist'),
                'type'          => 'toggle',
                'value'         => false,
            ],
            'legacy_location_slug' => [
                'type'  => 'text',
                'label' => __('Legacy Location Slug', 'directorist'),
                'value' => 'location',
                'show-if' => [
                    'where' => "enable_legacy_location",
                    'conditions' => [
                        ['key' => 'value', 'compare' => '=', 'value' => true],
                    ],
                ],
            ],
            'enable_legacy_category' => [
                'label'         => __('Enable Legacy Category', 'directorist'),
                'type'          => 'toggle',
                'value'         => false,
            ],
            'legacy_category_slug' => [
                'type'  => 'text',
                'label' => __('Legacy Category Slug', 'directorist'),
                'value' => 'listing-cat',
                'show-if' => [
                    'where' => "enable_legacy_category",
                    'conditions' => [
                        ['key' => 'value', 'compare' => '=', 'value' => true],
                    ],
                ],
            ],
        ];
        return $new_field + $fields;
    }

    /**
     * Override the location taxonomy
     */

    public function override_location_taxonomy()
    {
        unregister_taxonomy( ATBDP_LOCATION ); // remove the old one first

        $labels = array(
            'name'              => _x( 'Listing Locations', 'Location general name', 'directorist' ),
            'singular_name'     => _x( 'Listing Location', 'Location singular name', 'directorist' ),
            'search_items'      => __( 'Search Location', 'directorist' ),
            'all_items'         => __( 'All Locations', 'directorist' ),
            'parent_item'       => __( 'Parent Location', 'directorist' ),
            'parent_item_colon' => __( 'Parent Location:', 'directorist' ),
            'edit_item'         => __( 'Edit Location', 'directorist' ),
            'update_item'       => __( 'Update Location', 'directorist' ),
            'add_new_item'      => __( 'Add New Location', 'directorist' ),
            'new_item_name'     => __( 'New location name', 'directorist' ),
            'menu_name'         => __( 'Locations', 'directorist' ),
        );

        $args = array(
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public'            => true,
            'show_in_nav_menus' => true,
            'capabilities'      => array(
                'assign_terms' => get_post_type_object( ATBDP_POST_TYPE )->cap->publish_posts,
            ),
        );

        $slug = get_directorist_option( 'legacy_location_slug', 'location' );
        if ( ! empty( $slug ) ) {
            $args['rewrite'] = array(
                'slug' => $slug,
                'with_front'   => false,
                'hierarchical' => true,
            );
        }

        register_taxonomy( ATBDP_LOCATION, ATBDP_POST_TYPE, $args );
    }

    /**
     * Override the location taxonomy template
     */
    public function override_category_taxonomy()
    {
        unregister_taxonomy( ATBDP_CATEGORY ); // remove the old one first

        $labels = array(
            'name'              => _x( 'Listing Categories', 'Category general name', 'directorist' ),
            'singular_name'     => _x( 'Listing Category', 'Category singular name', 'directorist' ),
            'search_items'      => __( 'Search category', 'directorist' ),
            'all_items'         => __( 'All categories', 'directorist' ),
            'parent_item'       => __( 'Parent category', 'directorist' ),
            'parent_item_colon' => __( 'Parent category:', 'directorist' ),
            'edit_item'         => __( 'Edit category', 'directorist' ),
            'update_item'       => __( 'Update category', 'directorist' ),
            'add_new_item'      => __( 'Add New Category', 'directorist' ),
            'new_item_name'     => __( 'New category name', 'directorist' ),
            'menu_name'         => __( 'Categories', 'directorist' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public'            => true,
            'show_in_nav_menus' => true,
            'capabilities'      => array(
                'assign_terms' => get_post_type_object( ATBDP_POST_TYPE )->cap->publish_posts,
            ),
        );

        $slug = get_directorist_option( 'legacy_category_slug', 'listing-cat' );
        if ( ! empty( $slug ) ) {
            $args['rewrite'] = array(
                'slug' => $slug,
                'with_front'   => false,
                'hierarchical' => true,
            );
        }

        register_taxonomy( ATBDP_CATEGORY, ATBDP_POST_TYPE, $args );
    }

    /**
     * Override the location taxonomy template
     */
    public function override_location_taxonomy_template( $template )
    {
        if ( is_tax( ATBDP_LOCATION ) ) {
            $custom_template = DIRECTORIST_REFINED_TAXONOMY_DIR . 'templates/taxonomy/archive/location.php';

            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Override the location taxonomy template
     */
    public function override_category_taxonomy_template( $template )
    {
        if ( is_tax( ATBDP_CATEGORY ) ) {
            $custom_template = DIRECTORIST_REFINED_TAXONOMY_DIR . 'templates/taxonomy/archive/category.php';

            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Get the term link for a custom directory
     *
     * @param mixed $term Term object or term ID.
     * @param string $taxonomy Taxonomy name.
     * @return string|WP_Error Term link or WP_Error on failure.
     */
    public function custom_dir_get_term_link( $term, $taxonomy = '' ) {
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

    /**
     * Custom taxonomy archive link
     *
     * @param string $link The term link.
     * @param int $page_id The page ID.
     * @param object $term The term object.
     * @param string $directory_type The directory type.
     * @return string The custom term link.
     */
    public function custom_taxonomy_archive_link($link, $page_id, $term, $directory_type){
        $link = $this->custom_dir_get_term_link( (int) $term->term_id, $term->taxonomy );
        return $link;
    }

    /**
     * Custom taxonomy category archive title
     *
     * @param string $title The archive title.
     * @param string $original_title The original title.
     * @param string $prefix The prefix.
     * @return string The custom archive title.
     */
    public function category_archive_title( $title, $original_title, $prefix ){
        if( is_tax( 'at_biz_dir-category' ) ) {
            return $original_title;
        }
        return $title;
    }

    /**
     * Custom taxonomy location archive title
     *
     * @param string $title The archive title.
     * @param string $original_title The original title.
     * @param string $prefix The prefix.
     * @return string The custom archive title.
     */
    public function location_archive_title( $title, $original_title, $prefix ){
        if( is_tax( 'at_biz_dir-location' ) ) {
            return $original_title;
        }
        return $title;
    }

    /**
     * Get the term link for a custom directory
     *
     * @param string $url The term link.
     * @param object $term The term object.
     * @param string $taxonomy The taxonomy name.
     * @return string|WP_Error The term link or WP_Error on failure.
     */
    public function get_legacy_term_link( $url, $term, $taxonomy ) {
        $category_enabled = get_directorist_option( 'enable_legacy_category', false );
        $location_enabled = get_directorist_option( 'enable_legacy_location', false );
    
        if ( ATBDP_CATEGORY == $taxonomy && ! $category_enabled ) {
            return $url;
        }
    
        if ( ATBDP_LOCATION == $taxonomy && ! $location_enabled ) {
            return $url;
        }
    
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
    }
}

/**
 * Initialize the Directorist_Taxonomy_Declaration class
 */
new Directorist_Taxonomy_Declaration();