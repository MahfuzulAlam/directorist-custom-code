<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.2
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$id = $listing->id;

$min_price = get_post_meta( $id, '_min_price', true );
$max_price = get_post_meta( $id, '_max_price', true );

if ( !Helper::has_price_range( $id ) && !Helper::has_price( $id ) && !$min_price && !$max_price ) {
	return;
}
?>

<div class="directorist-info-item directorist-pricing-meta directorist-info-item-price">
	<?php
    if(  $min_price && $max_price ) {
?>
        <span class="directorist-listing-price"><?php echo wp_kses_post( Helper::formatted_price( $min_price ) ); ?></span>
        <span> - </span>
        <span class="directorist-listing-price"><?php echo wp_kses_post( Helper::formatted_price( $max_price ) ); ?></span>
<?php
    }else{
        if ( 'range' === Helper::pricing_type( $id ) ) {
            Helper::price_range_template( $id );
        } else {
            Helper::price_template( $id );
        }
    }
	?>
</div>