<?php
/**
 * Default Directorist searches around the current user's profile address.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Profile_Radius_Search' ) ) {
	/**
	 * Adds the current user's saved profile coordinates to Directorist queries.
	 */
	final class Directorist_Custom_Code_Profile_Radius_Search {
		/**
		 * Default profile search radius in miles.
		 */
		const DEFAULT_RADIUS_MILES = 25;

		/**
		 * Register Directorist query filters.
		 *
		 * @return void
		 */
		public static function register() {
			add_filter( 'directorist_all_listings_query_arguments', array( __CLASS__, 'filter_query_arguments' ), 5 );
			add_filter( 'atbdp_listing_search_query_argument', array( __CLASS__, 'filter_query_arguments' ), 5 );
		}

		/**
		 * Default Directorist results around the logged-in user's profile.
		 *
		 * An existing geo query or visitor-selected search location takes precedence.
		 * The fallback requires address, latitude, and longitude on the user profile.
		 *
		 * @param array $args Directorist/WP_Query arguments.
		 * @return array
		 */
		public static function filter_query_arguments( $args ) {
			if ( ! is_array( $args ) || ! is_user_logged_in() ) {
				return $args;
			}

			if ( is_admin() && ! wp_doing_ajax() ) {
				return $args;
			}

			if ( ! empty( $args['atbdp_geo_query'] ) || self::has_explicit_search_location() ) {
				return $args;
			}

			$user_id   = get_current_user_id();
			$address   = trim( (string) get_user_meta( $user_id, 'address', true ) );
			$latitude  = get_user_meta( $user_id, 'latitude', true );
			$longitude = get_user_meta( $user_id, 'longitude', true );

			if (
				'' === $address ||
				! self::is_valid_coordinate( $latitude, -90, 90 ) ||
				! self::is_valid_coordinate( $longitude, -180, 180 )
			) {
				return $args;
			}

			$args['atbdp_geo_query'] = array(
				'lat_field'    => '_manual_lat',
				'lng_field'    => '_manual_lng',
				'latitude'     => (float) $latitude,
				'longitude'    => (float) $longitude,
				'min_distance' => 0,
				'max_distance' => self::DEFAULT_RADIUS_MILES,
				'units'        => 'miles',
			);

			return $args;
		}

		/**
		 * Check whether the current request contains a visitor-selected location.
		 *
		 * @return bool
		 */
		private static function has_explicit_search_location() {
			$location_keys = array(
				'address',
				'cityLat',
				'cityLng',
				'zip',
				'zip_cityLat',
				'zip_cityLng',
				'in_loc',
			);

			foreach ( $location_keys as $key ) {
				if ( ! isset( $_REQUEST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.
					continue;
				}

				$value = wp_unslash( $_REQUEST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.
				if ( is_array( $value ) ? ! empty( array_filter( $value ) ) : '' !== trim( (string) $value ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Determine whether a value is a valid latitude or longitude.
		 *
		 * @param mixed $value Coordinate value.
		 * @param float $min   Minimum accepted value.
		 * @param float $max   Maximum accepted value.
		 * @return bool
		 */
		private static function is_valid_coordinate( $value, $min, $max ) {
			if ( ! is_scalar( $value ) ) {
				return false;
			}

			$value = trim( (string) $value );
			if ( '' === $value || ! is_numeric( $value ) ) {
				return false;
			}

			$value = (float) $value;

			return $value >= $min && $value <= $max;
		}
	}
}
