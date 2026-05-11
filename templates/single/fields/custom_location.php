<?php
/**
 * Custom Location field for Directorist single listing.
 *
 * Custom taxonomy: ATBDP_LOCATION
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label      = ( isset( $data['label'] ) && ! empty( $data['label'] ) ) ? $data['label'] : __( 'Location', 'directorist' );
$listing_id = ( isset( $data['listing_id'] ) && ! empty( $data['listing_id'] ) ) ? absint( $data['listing_id'] ) : get_the_ID();

$terms = array();
if ( $listing_id ) {
	$terms = wp_get_post_terms( $listing_id, ATBDP_LOCATION );
}

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

$links = array();
foreach ( $terms as $term ) {
	if ( ! $term instanceof WP_Term ) {
		continue;
	}

	$term_link = get_term_link( $term );
	if ( is_wp_error( $term_link ) ) {
		$links[] = esc_html( $term->name );
		continue;
	}

	$links[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $term_link ),
		esc_html( $term->name )
	);
}

if ( empty( $links ) ) {
	return;
}
?>

<div class="directorist-single-info directorist-single-info-text">
	<div class="directorist-single-info__label">
		<span class="directorist-single-info__label-icon"><?php directorist_icon( $icon ); ?></span>
		<span class="directorist-single-info__label__text"><?php echo esc_html( $label ); ?></span>
	</div>

	<div class="directorist-single-info__value">
		<?php echo wp_kses_post( implode( ', ', $links ) ); ?>
	</div>
</div>

