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
		$default_categories = array();

		if ( isset( $data['default_categories'] ) ) {
			$default_categories = (array) wp_unslash( $data['default_categories'] );
			$default_categories = array_filter( array_map( 'absint', $default_categories ) );
		}

		update_user_meta( $user_id, 'latitude', $latitude );
		update_user_meta( $user_id, 'longitude', $longitude );
		update_user_meta( $user_id, 'default_categories', $default_categories );
	},
	10,
	2
);


if ( ! function_exists( 'directorist_listing_form_geo_defaults' ) ) {
	/**
	 * Prefill listing form when the listing has no address/coords saved.
	 * Front-end: current user meta. Admin: listing post author meta.
	 *
	 * @param int $post_id Listing post ID (add_listing_id).
	 * @return array{address:string,latitude:string,longitude:string}
	 */
	function directorist_listing_form_geo_defaults( $post_id ) {
		$empty = array(
			'address'   => '',
			'latitude'  => '',
			'longitude' => '',
		);

		$post_id = absint( $post_id );
		if ( is_admin() ) {
			if ( ! $post_id ) {
				return $empty;
			}
			$author_id = (int) get_post_field( 'post_author', $post_id );
		} else {
			if ( ! is_user_logged_in() ) {
				return $empty;
			}
			$author_id = get_current_user_id();
		}

		if ( ! $author_id ) {
			return $empty;
		}

		return array(
			'address'   => (string) get_user_meta( $author_id, 'address', true ),
			'latitude'  => (string) get_user_meta( $author_id, 'latitude', true ),
			'longitude' => (string) get_user_meta( $author_id, 'longitude', true ),
		);
	}
}

if ( ! function_exists( 'directorist_listing_form_profile_contact_defaults' ) ) {
	/**
	 * Prefill listing email / phone / website from the same sources as the Directorist profile tab
	 * (wp_users + atbdp_phone meta). Front-end: current user. Admin: listing author when editing.
	 *
	 * @param int $post_id Listing post ID (add_listing_id).
	 * @return array{email:string,phone:string,website:string}
	 */
	function directorist_listing_form_profile_contact_defaults( $post_id ) {
		$empty = array(
			'email'   => '',
			'phone'   => '',
			'website' => '',
		);

		$post_id = absint( $post_id );
		if ( is_admin() ) {
			if ( ! $post_id ) {
				return $empty;
			}
			$author_id = (int) get_post_field( 'post_author', $post_id );
		} else {
			if ( ! is_user_logged_in() ) {
				return $empty;
			}
			$author_id = get_current_user_id();
		}

		if ( ! $author_id ) {
			return $empty;
		}

		$userdata = get_userdata( $author_id );
		if ( ! $userdata ) {
			return $empty;
		}

		return array(
			'email'   => (string) $userdata->user_email,
			'phone'   => (string) get_user_meta( $author_id, 'atbdp_phone', true ),
			'website' => (string) $userdata->user_url,
		);
	}
}
