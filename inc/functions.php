<?php
/**
 * Custom extension hooks and helpers.
 *
 * Add project-specific PHP code here.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_filter( 'atbdp_single_listing_content_widgets', function ( $widgets ) {
	$widgets['category'] = [
		'options' => [
			'icon' => [
				'type' => 'icon',
				'label' => 'Icon',
				'value' => 'la la-money',
			],
		],
	];
	$widgets['location'] = [
		'options' => [
			'icon' => [
				'type' => 'icon',
				'label' => 'Icon',
				'value' => 'la la-money',
			],
		],
	];
	$widgets['pricing'] = [
		'options' => [
			'icon' => [
				'type' => 'icon',
				'label' => 'Icon',
				'value' => 'la la-money',
			],
		],
	];
	return $widgets;
} );



add_filter( 'directorist_single_item_template', function ( $template, $field_data ) {
	e_var_dump( $field_data );
	// if ( ! function_exists( 'extension_path' ) ) {
	// 	// Attempt to include class-template-loader.php if not loaded.
	// 	$template_loader = WP_PLUGIN_DIR . '/directorist-custom-code/inc/class-template-loader.php';
	// 	if ( file_exists( $template_loader ) ) {
	// 		require_once $template_loader;
	// 	}
	// }

	// if ( function_exists( 'extension_path' ) ) {
	// 	if ( $field_data['name'] === 'category' ) {
	// 		return extension_path( 'templates/single/fields/custom/category.php' );
	// 	}
	// 	if ( $field_data['name'] === 'location' ) {
	// 		return extension_path( 'templates/single/fields/custom/location.php' );
	// 	}
	// 	if ( $field_data['name'] === 'pricing' ) {
	// 		return extension_path( 'templates/single/fields/custom/pricing.php' );
	// 	}
	// } else {
	// 	// Fallback to original relative paths if extension_path is unavailable.
	// 	if ( $field_data['name'] === 'category' ) {
	// 		return 'templates/single/fields/custom/category.php';
	// 	}
	// 	if ( $field_data['name'] === 'location' ) {
	// 		return 'templates/single/fields/custom/location.php';
	// 	}
	// 	if ( $field_data['name'] === 'pricing' ) {
	// 		return 'templates/single/fields/custom/pricing.php';
	// 	}
	// }

	return $template;
}, 10, 2 );