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


add_filter( 'atbdp_single_listing_other_fields_widget', function ( $widgets ) {
	$widgets['custom_category'] = [
		'type'          => 'widget',
		'label'         => __( 'Category', 'directorist' ),
		'icon'          => 'la la-tags', // Uses an appropriate "tags" icon for category
		'options' => [
			'label'   => [
				'type'  => 'text',
				'label' => __( 'Label', 'directorist' ),
				'value' => 'Category',
			],
			'icon' => [
				'type' => 'icon',
				'label' => __( 'Icon', 'directorist' ),
				'value' => 'la la-tags', // "tags" icon
			],
		],
	];
	$widgets['custom_location'] = [
		'type'          => 'widget',
		'label'         => __( 'Location', 'directorist' ),
		'icon'          => 'la la-map-marker', // Uses an appropriate "map marker" icon for location
		'options' => [
			'label'   => [
				'type'  => 'text',
				'label' => __( 'Label', 'directorist' ),
				'value' => 'Location',
			],
			'icon' => [
				'type' => 'icon',
				'label' => __( 'Icon', 'directorist' ),
				'value' => 'la la-map-marker', // "map marker" icon
			],
		],
	];
	$widgets['custom_pricing'] = [
		'type'          => 'widget',
		'label'         => __( 'Pricing', 'directorist' ),
		'icon'          => 'la la-money-bill', // Keep this icon as it is appropriate
		'options' => [
			'label'   => [
				'type'  => 'text',
				'label' => __( 'Label', 'directorist' ),
				'value' => 'Pricing',
			],
			'icon' => [
				'type' => 'icon',
				'label' => __( 'Icon', 'directorist' ),
				'value' => 'la la-money-bill',
			],
		],
	];
	return $widgets;
} );