<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$listing = $data[ 'listings' ];
$data = $data[ 'data' ];
$field = $data[ 'original_field' ];

if( ! $field[ 'options' ] || empty( $field[ 'options' ]) ){
	$field[ 'options' ] = get_option( $field[ 'field_key' ] . '_options' );
}

$value = directorist_get_custom_select_value_label( $value, $field[ 'options' ] );
?>

<div class="directorist-listing-card-select"><?php directorist_icon( $icon ); ?><?php $listing->print_label( $label ); ?><?php echo esc_html( $value ); ?></div>