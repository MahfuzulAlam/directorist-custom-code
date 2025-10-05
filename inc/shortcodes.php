<?php

/**
 * Shortcodes for Directorist Custom Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Remote Directorist Listings shortcode
 *
 * Usage: [remote_directorist_listings site="healthcare" per_page="8" featured="1" orderby="date" order="desc"]
 *
 * Attributes:
 * - site: Sub-site path segment (e.g. "healthcare"). If empty, current site is used.
 * - site_url: Full base URL to use instead of network home + site (optional)
 * - per_page, featured, orderby, order: API query controls (optional)
 */
function dcc_render_remote_directorist_listings_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'site'      => '',
			'site_url'  => '',
			'per_page'  => '8',
			'featured'  => '0',
			'orderby'   => 'rand',
			'order'     => 'desc',
			'directory' => '34',
		),
		$atts,
		'remote_directorist_listings'
	);

	$base_url = '';

	if ( ! empty( $atts['site_url'] ) ) {
		$base_url = esc_url_raw( trailingslashit( $atts['site_url'] ) );
	} elseif ( ! empty( $atts['site'] ) ) {
		$network_root = trailingslashit( network_home_url( '/' ) );
		$base_url     = esc_url_raw( $network_root . trailingslashit( $atts['site'] ) );
	} else {
		$base_url = esc_url_raw( trailingslashit( home_url( '/' ) ) );
	}

	$query_args = array(
		'per_page' => absint( $atts['per_page'] ),
		'featured' => sanitize_text_field( $atts['featured'] ),
		'orderby'  => sanitize_text_field( $atts['orderby'] ),
		'order'    => sanitize_text_field( $atts['order'] ),
		'directory'=> sanitize_text_field( $atts['directory'] ),
	);

	$endpoint = add_query_arg(
		$query_args,
		rtrim( $base_url, '/' ) . '/wp-json/directorist/v1/listings'
	);

	$container_id = 'dcc-remote-listings-' . wp_generate_password( 6, false, false );

	ob_start();
	?>
	<div id="<?php echo esc_attr( $container_id ); ?>" class="dcc-remote-listings" data-endpoint="<?php echo esc_url( $endpoint ); ?>"></div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'remote_directorist_listings', 'dcc_render_remote_directorist_listings_shortcode' );


