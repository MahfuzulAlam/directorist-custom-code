<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.2
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$id = $listing->id;
$price_suffix = get_post_meta( $id, '_price_suffix', true );
if ( ! Helper::has_price_range( $id ) && ! Helper::has_price( $id ) ) {
    return;
}
?>

<div class="directorist-info-item directorist-pricing-meta directorist-info-item-price">
    <?php
    if ( 'range' === Helper::pricing_type( $id ) ) {
        Helper::price_range_template( $id );
    } else {
        Helper::price_template( $id );
    }
    ?>
    <?php if ( $price_suffix ) : ?>
        <span class="directorist-pricing-meta-suffix">
            <?php echo esc_html( $price_suffix ); ?>
        </span>
    <?php endif; ?>
</div>