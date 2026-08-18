<?php
/**
 * Limit Directorist searches to the current user's profile location by default.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Profile_Radius_Search' ) ) {
	/**
	 * Adds the current user's saved profile location to Directorist queries.
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
		 * Apply profile location defaults to Directorist results.
		 *
		 * An existing geo query or visitor-selected search location takes precedence.
		 * A valid default location limits results to that taxonomy term. A complete
		 * profile address and coordinates also limit results to the default radius.
		 *
		 * @param array $args Directorist/WP_Query arguments.
		 * @return array
		 */
		public static function filter_query_arguments( $args ) {
			if ( ! is_array( $args ) ) {
				return $args;
			}

			if ( is_admin() && ! wp_doing_ajax() ) {
				return $args;
			}

			$location_taxonomy  = directorist_custom_code_location_taxonomy();
			$args               = self::add_requested_location_id_query( $args, $location_taxonomy );

			if ( ! is_user_logged_in() ) {
				return $args;
			}

			$user_id            = get_current_user_id();
			$defaults           = directorist_custom_code_get_profile_search_location_defaults( $user_id );
			$profile_components = directorist_custom_code_get_profile_search_request_components( $defaults );
			$profile_request    = ! empty( $profile_components );

			if ( directorist_custom_code_has_explicit_search_location( $defaults, $profile_components ) ) {
				return $args;
			}

			$has_location_query = self::has_taxonomy_query( $args, $location_taxonomy );

			if ( $profile_request ) {
				if (
					( $has_location_query && ! in_array( 'taxonomy', $profile_components, true ) ) ||
					( ! empty( $args['atbdp_geo_query'] ) && ! in_array( 'address', $profile_components, true ) )
				) {
					return $args;
				}

				$apply_taxonomy = in_array( 'taxonomy', $profile_components, true );
				$apply_address  = in_array( 'address', $profile_components, true );
			} else {
				if ( ! empty( $args['atbdp_geo_query'] ) || $has_location_query ) {
					return $args;
				}

				$apply_taxonomy = ! empty( $defaults['location_id'] );
				$apply_address  = ! empty( $defaults['address'] );
			}

			if ( $apply_taxonomy && ! $has_location_query ) {
				$args = self::add_location_taxonomy_query( $args, $defaults['location_id'], $location_taxonomy );
			}

			if ( ! $apply_address ) {
				return $args;
			}

			$args = self::remove_profile_address_meta_query( $args, $defaults['address'] );

			if ( empty( $defaults['has_geo'] ) ) {
				return $args;
			}

			$args['atbdp_geo_query'] = array(
				'lat_field'    => '_manual_lat',
				'lng_field'    => '_manual_lng',
				'latitude'     => (float) $defaults['latitude'],
				'longitude'    => (float) $defaults['longitude'],
				'min_distance' => 0,
				'max_distance' => self::DEFAULT_RADIUS_MILES,
				'units'        => 'miles',
			);

			return $args;
		}

		/**
		 * Add a valid loc_id request to query arguments when Directorist has not.
		 *
		 * @param array  $args     Directorist/WP_Query arguments.
		 * @param string $taxonomy Location taxonomy name.
		 * @return array
		 */
		private static function add_requested_location_id_query( $args, $taxonomy ) {
			if (
				'' !== directorist_custom_code_get_request_scalar( 'in_loc' ) ||
				self::has_taxonomy_query( $args, $taxonomy )
			) {
				return $args;
			}

			$location_ids = wp_parse_id_list( directorist_custom_code_get_request_scalar( 'loc_id' ) );
			$location_id  = ! empty( $location_ids ) ? (int) reset( $location_ids ) : 0;
			$location     = $location_id ? get_term( $location_id, $taxonomy ) : null;

			if ( ! $location || is_wp_error( $location ) ) {
				return $args;
			}

			return self::add_location_taxonomy_query( $args, $location->term_id, $taxonomy );
		}

		/**
		 * Add a location taxonomy clause while preserving an existing query group.
		 *
		 * @param array  $args        Directorist/WP_Query arguments.
		 * @param int    $location_id Location term ID.
		 * @param string $taxonomy    Location taxonomy name.
		 * @return array
		 */
		private static function add_location_taxonomy_query( $args, $location_id, $taxonomy ) {
			$location_query = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'term_id',
				'terms'            => array( (int) $location_id ),
				'include_children' => true,
			);

			if ( empty( $args['tax_query'] ) || ! is_array( $args['tax_query'] ) ) {
				$args['tax_query'] = array( $location_query );
			} else {
				$args['tax_query'] = array(
					'relation' => 'AND',
					$args['tax_query'],
					$location_query,
				);
			}

			return $args;
		}

		/**
		 * Check whether query arguments already contain a taxonomy clause.
		 *
		 * @param array  $args     Directorist/WP_Query arguments.
		 * @param string $taxonomy Taxonomy name.
		 * @return bool
		 */
		private static function has_taxonomy_query( $args, $taxonomy ) {
			if ( empty( $args['tax_query'] ) || ! is_array( $args['tax_query'] ) ) {
				return false;
			}

			return self::tax_query_contains_taxonomy( $args['tax_query'], $taxonomy );
		}

		/**
		 * Recursively inspect a possibly nested taxonomy query.
		 *
		 * @param array  $tax_query Taxonomy query group or clause.
		 * @param string $taxonomy  Taxonomy name.
		 * @return bool
		 */
		private static function tax_query_contains_taxonomy( $tax_query, $taxonomy ) {
			if ( isset( $tax_query['taxonomy'] ) && $taxonomy === $tax_query['taxonomy'] ) {
				return true;
			}

			foreach ( $tax_query as $key => $clause ) {
				if ( 'relation' === $key || ! is_array( $clause ) ) {
					continue;
				}

				if ( self::tax_query_contains_taxonomy( $clause, $taxonomy ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Remove Directorist's address-text clause for an unchanged profile default.
		 *
		 * @param array  $args    Directorist/WP_Query arguments.
		 * @param string $address Profile address.
		 * @return array
		 */
		private static function remove_profile_address_meta_query( $args, $address ) {
			if (
				empty( $args['meta_query']['_address'] ) ||
				! is_array( $args['meta_query']['_address'] ) ||
				'_address' !== ( $args['meta_query']['_address']['key'] ?? '' ) ||
				$address !== ( $args['meta_query']['_address']['value'] ?? '' )
			) {
				return $args;
			}

			unset( $args['meta_query']['_address'] );

			if ( empty( array_diff_key( $args['meta_query'], array( 'relation' => true ) ) ) ) {
				unset( $args['meta_query'] );
			}

			return $args;
		}
	}
}
