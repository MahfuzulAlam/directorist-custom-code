<?php

/**
 * Add your custom php code here
 */


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