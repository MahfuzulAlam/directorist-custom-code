<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$date_format = get_option( 'date_format' );
if( $value && $date_format ) $value = date( $date_format, strtotime( $value ) );
?>

<div class="directorist-listing-card-date"><?php directorist_icon( $icon );?><?php $listings->print_label( $label ); ?><?php echo esc_html( $value ); ?></div>