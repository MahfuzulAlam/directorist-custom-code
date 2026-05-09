<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$id = get_the_ID();

$price_suffix = get_post_meta( $id, '_price_suffix', true );
if ( ! Helper::has_price_range( $id ) && ! Helper::has_price( $id ) ) {
    return;
}
?>

<span class="directorist-info-item directorist-pricing-meta">
    <?php
    if ( 'range' === Helper::pricing_type( $id ) ) {
        Helper::price_range_template( $id );
    } elseif ( ! $listings->is_disable_price ) {
        Helper::price_template( $id );
    }
    ?>
    <?php if ( $price_suffix ) : ?>
        <span class="directorist-pricing-meta-suffix">
            <?php echo esc_html( $price_suffix ); ?>
        </span>
    <?php endif; ?>
</span>