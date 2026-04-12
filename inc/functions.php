<?php

/**
 * Add your custom php code here
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persist profile latitude / longitude (dashboard tab-profile custom fields).
 */
add_action(
	'directorist_user_profile_updated',
	static function ( $user_id, $data ) {
		$latitude  = isset( $data['latitude'] ) ? sanitize_text_field( trim( wp_unslash( $data['latitude'] ) ) ) : '';
		$longitude = isset( $data['longitude'] ) ? sanitize_text_field( trim( wp_unslash( $data['longitude'] ) ) ) : '';
		update_user_meta( $user_id, 'latitude', $latitude );
		update_user_meta( $user_id, 'longitude', $longitude );
	},
	10,
	2
);

