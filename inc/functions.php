<?php

/**
 * Add your custom php code here
 */


add_shortcode('directorist_listing_qrcode', 'directorist_qrcode_generator');

function directorist_qrcode_generator( $atts )
{
    $atts = shortcode_atts(array(
        'text' => '',
        'size' => 200,
        'level' => 'L',
    ), $atts, 'directorist_listing_qrcode');

    if( ! is_user_logged_in() ) return;

    ob_start();
    ?>
    <div id="directorist_qrcode" text="<?php echo get_the_permalink(); ?>"></div>
    <?php
    return ob_get_clean();
}