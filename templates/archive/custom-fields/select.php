<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if( isset( $data['data']['original_field']['field_key'] ) && ! empty( $data['data']['original_field']['field_key'] )){
    if( $data['data']['original_field']['field_key'] == 'discount_type' ){
        $raw = get_post_meta( get_the_ID(), '_discount_type', true );
        if( $raw == 'percentage' ){
            $percentage = get_post_meta( get_the_ID(), '_percentage', true );
            if( $percentage ){
                $value = $percentage . '% Off';
            }
        }elseif( $raw == 'fixed' ){
            $price = get_post_meta( get_the_ID(), '_price', true );
            if( $price ){
                $value = '&#2547;' . $price;
            }
        }
    }
}

?>

<li class="directorist-listing-card-select">
    <?php directorist_icon( $icon ); ?>
    <?php $listings->print_label( $label ); ?>
    <?php echo esc_html( $value ); ?>
</li>