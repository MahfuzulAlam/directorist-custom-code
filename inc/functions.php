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


add_filter( 'atbdp_form_custom_widgets', function( $widgets ){
	$widgets['url']['options']['link_text'] = [
		'type' => 'text',
		'label' => 'Link Text',
		'value' => '',
		'description' => __('Optional text to display as the link, instead of the URL.', 'directorist-custom-code'),
	];
	return $widgets;
} );