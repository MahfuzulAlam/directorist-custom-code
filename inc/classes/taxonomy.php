<?php

class Directorist_Taxonomy_Declaration
{
    public function __construct()
    {
        add_action('init', array($this, 'override_location_taxonomy'), 20);
        add_action('init', array($this, 'override_category_taxonomy'), 20);
        add_filter( 'template_include', array($this,'override_taxonomy_template') );
        //add_filter('the_content', array($this, 'override_taxonomy_template'), 10, 1);
    }

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

        $slug = 'location';
        if ( ! empty( $slug ) ) {
            $args['rewrite'] = array(
                'slug' => $slug,
                'with_front'   => false,
                'hierarchical' => true,
            );
        }

        register_taxonomy( ATBDP_LOCATION, ATBDP_POST_TYPE, $args );
    }

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

        $slug = 'category';
        if ( ! empty( $slug ) ) {
            $args['rewrite'] = array(
                'slug' => $slug,
                'with_front'   => false,
                'hierarchical' => true,
            );
        }

        register_taxonomy( ATBDP_CATEGORY, ATBDP_POST_TYPE, $args );
    }

    public function override_taxonomy_template( $template )
    {
        if ( is_tax( ATBDP_CATEGORY ) ) {
            $custom_template = DIRECTORIST_REFINED_TAXONOMY_DIR . 'templates/taxonomy/archive/category.php';

            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        if ( is_tax( ATBDP_LOCATION ) ) {
            $custom_template = DIRECTORIST_REFINED_TAXONOMY_DIR . 'templates/taxonomy/archive/location.php';

            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }
}

new Directorist_Taxonomy_Declaration();