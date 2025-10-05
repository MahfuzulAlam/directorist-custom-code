<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register and enqueue frontend assets for the React app.
 */
function dcc_register_assets() {
	$asset_handle = 'dcc-remote-listings';

	$script_path  = DIRECTORIST_CUSTOM_CODE_DIR . 'assets/build/index.js';
	$asset_path   = DIRECTORIST_CUSTOM_CODE_DIR . 'assets/build/index.asset.php';
	$style_path   = DIRECTORIST_CUSTOM_CODE_DIR . 'assets/build/index.css';
	$script_uri   = DIRECTORIST_CUSTOM_CODE_URI . 'assets/build/index.js';
	$style_uri    = DIRECTORIST_CUSTOM_CODE_URI . 'assets/build/index.css';

	$deps    = array( 'wp-element' );
	$version = file_exists( $script_path ) ? filemtime( $script_path ) : null;

	if ( file_exists( $asset_path ) ) {
		$asset   = include $asset_path; // returns [ 'dependencies' => [], 'version' => 'xxxx' ]
		$deps    = isset( $asset['dependencies'] ) ? $asset['dependencies'] : $deps;
		$version = isset( $asset['version'] ) ? $asset['version'] : $version;
	}

	if ( file_exists( $script_path ) ) {
		wp_register_script( $asset_handle, $script_uri, $deps, $version, true );
	}

	if ( file_exists( $style_path ) ) {
		wp_register_style( $asset_handle, $style_uri, array(), $version ? $version : filemtime( $style_path ) );
	}
}
add_action( 'wp_enqueue_scripts', 'dcc_register_assets' );

/**
 * Enqueue assets when shortcode is present.
 */
function dcc_maybe_enqueue_assets( $posts ) {
	if ( empty( $posts ) ) {
		return $posts;
	}

	$shortcode_found = false;

	foreach ( $posts as $post ) {
		if ( has_shortcode( $post->post_content, 'remote_directorist_listings' ) ) {
			$shortcode_found = true;
			break;
		}
	}

	if ( $shortcode_found ) {
		wp_enqueue_script( 'dcc-remote-listings' );
		wp_enqueue_style( 'dcc-remote-listings' );
	}

	return $posts;
}
add_filter( 'the_posts', 'dcc_maybe_enqueue_assets' );


