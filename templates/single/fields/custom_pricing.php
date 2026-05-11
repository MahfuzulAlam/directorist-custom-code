<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.0.5.2
 */
use \Directorist\Helper;
if ( ! defined( 'ABSPATH' ) ) exit;
$label = isset( $data['label'] ) && ! empty( $data['label'] ) ? $data['label'] : 'Pricing';
$listing_id  = isset( $data['listing_id'] ) && ! empty( $data['listing_id'] ) ? $data['listing_id'] : get_the_ID();
?>

<div class="directorist-single-info directorist-single-info-text">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon( $icon );?></span>
        <span class="directorist-single-info__label__text"><?php echo esc_html( $label ); ?></span>
    </div>

    <div class="directorist-single-info__value">
    <?php
        if ( 'range' === Helper::pricing_type( $listing_id ) ) {
            Helper::price_range_template( $listing_id );
        } else {
            Helper::price_template( $listing_id );
        }
    ?>
    </div>

</div>