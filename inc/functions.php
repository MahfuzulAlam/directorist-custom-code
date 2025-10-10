<?php

/**
 * Add your custom php code here
 */

/**
 * Add Custom Badges
 */

add_action( 'init', function (){
    $category_badge_atts = [
        'id'         => 'category-badge',
        'label'      => 'Categories',
        'icon'       => 'uil uil-text-fields',
        'hook'       => 'atbdp-category-badge',
        'title'      => 'Categories',
        'meta_key'   => '_categories',
        'meta_value' => '',
        'class'      => 'categories-badge'
    ];

    $listing_view_badge_atts = [
        'id'         => 'listing-view-badge',
        'label'      => 'Views',
        'icon'       => 'uil uil-text-fields',
        'hook'       => 'atbdp-view-badge',
        'title'      => 'Listing Views',
        'meta_key'   => '_listing_views',
        'meta_value' => '',
        'class'      => 'listing-view-badge'
    ];

    new Shanir_Akhra_Badge($category_badge_atts);
    new Shanir_Akhra_Badge($listing_view_badge_atts);
} );

add_action( 'wp_footer', function(){
    $network_home = esc_url( network_home_url( '/' ) );
    ?>
    <script type="text/javascript">
        jQuery( document ).ready( function( $ ){
            $( '.theme-header-logo__brand a, .theme-header-logo-brand' ).attr( 'href', '<?php echo $network_home; ?>' );
        } );
    </script>
    <?php
} );

add_filter('directorist_custom_field_meta_key_field_args', function ($args) {
    $args['type'] = 'text';
    return $args;
});