<?php

/**
 * Add your custom php code here
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'directorist_custom_code_get_request_scalar' ) ) {
	/**
	 * Return a sanitized scalar value from the current request.
	 *
	 * @param string $key Request key.
	 * @return string
	 */
	function directorist_custom_code_get_request_scalar( $key ) {
		if ( ! isset( $_REQUEST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.
			return '';
		}

		$value = wp_unslash( $_REQUEST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.

		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}
}

if ( ! function_exists( 'directorist_custom_code_has_explicit_search_location' ) ) {
	/**
	 * Determine whether the current request already specifies a search location.
	 *
	 * @param array|null $profile_defaults   Resolved profile defaults, if already available.
	 * @param array|null $profile_components Unchanged profile-derived request components.
	 * @return bool
	 */
	function directorist_custom_code_has_explicit_search_location( $profile_defaults = null, $profile_components = null ) {
		if ( null === $profile_defaults ) {
			$profile_defaults = is_user_logged_in() ? directorist_custom_code_get_profile_search_location_defaults( get_current_user_id() ) : array();
		}

		if ( null === $profile_components ) {
			$profile_components = directorist_custom_code_get_profile_search_request_components( $profile_defaults );
		}

		$location_state = directorist_custom_code_get_search_location_state();
		$location_keys  = array(
			'address',
			'cityLat',
			'cityLng',
			'zip',
			'zip_cityLat',
			'zip_cityLng',
			'in_loc',
			'loc_id',
		);

		foreach ( $location_keys as $key ) {
			if ( 'in_loc' === $key && in_array( 'taxonomy', $profile_components, true ) ) {
				continue;
			}

			if ( in_array( $key, array( 'address', 'cityLat', 'cityLng' ), true ) && in_array( 'address', $profile_components, true ) ) {
				continue;
			}

			if ( ! isset( $_REQUEST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.
				continue;
			}

			$value = wp_unslash( $_REQUEST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend search context.
			if ( is_array( $value ) ? ! empty( array_filter( $value ) ) : '' !== trim( (string) $value ) ) {
				return true;
			}
		}

		$archive_location = get_query_var( 'atbdp_location' );
		if ( is_scalar( $archive_location ) && '' !== trim( (string) $archive_location ) ) {
			return true;
		}

		if ( in_array( $location_state, array( 'cleared', 'custom' ), true ) ) {
			return true;
		}

		return '' !== $location_state && empty( $profile_components );
	}
}

if ( ! function_exists( 'directorist_custom_code_location_taxonomy' ) ) {
	/**
	 * Return the active Directorist location taxonomy slug.
	 *
	 * @return string
	 */
	function directorist_custom_code_location_taxonomy() {
		$taxonomy = defined( 'ATBDP_LOCATION' ) ? ATBDP_LOCATION : 'at_biz_dir-location';

		return taxonomy_exists( $taxonomy ) ? $taxonomy : 'at_biz_dir-location';
	}
}

if ( ! function_exists( 'directorist_custom_code_is_valid_coordinate' ) ) {
	/**
	 * Determine whether a value is a valid latitude or longitude.
	 *
	 * @param mixed $value Coordinate value.
	 * @param float $min   Minimum accepted value.
	 * @param float $max   Maximum accepted value.
	 * @return bool
	 */
	function directorist_custom_code_is_valid_coordinate( $value, $min, $max ) {
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

if ( ! function_exists( 'directorist_custom_code_get_profile_search_location_defaults' ) ) {
	/**
	 * Return the valid search-location defaults saved on a user profile.
	 *
	 * @param int $user_id User ID.
	 * @return array{location_id:int,address:string,latitude:string,longitude:string,has_geo:bool}
	 */
	function directorist_custom_code_get_profile_search_location_defaults( $user_id ) {
		$defaults   = array(
			'location_id' => 0,
			'address'     => '',
			'latitude'    => '',
			'longitude'   => '',
			'has_geo'     => false,
		);
		$user_id    = absint( $user_id );

		if ( ! $user_id ) {
			return $defaults;
		}

		$taxonomy     = directorist_custom_code_location_taxonomy();
		$location_ids = wp_parse_id_list( get_user_meta( $user_id, 'default_locations', true ) );
		$location_id  = ! empty( $location_ids ) ? (int) reset( $location_ids ) : 0;
		$location     = $location_id ? get_term( $location_id, $taxonomy ) : null;
		$address      = get_user_meta( $user_id, 'address', true );
		$latitude     = get_user_meta( $user_id, 'latitude', true );
		$longitude    = get_user_meta( $user_id, 'longitude', true );
		$address      = is_scalar( $address ) ? sanitize_text_field( trim( (string) $address ) ) : '';
		$latitude     = is_scalar( $latitude ) ? sanitize_text_field( trim( (string) $latitude ) ) : '';
		$longitude    = is_scalar( $longitude ) ? sanitize_text_field( trim( (string) $longitude ) ) : '';
		$has_geo      = '' !== $address &&
			directorist_custom_code_is_valid_coordinate( $latitude, -90, 90 ) &&
			directorist_custom_code_is_valid_coordinate( $longitude, -180, 180 );

		if ( $location && ! is_wp_error( $location ) ) {
			$defaults['location_id'] = (int) $location->term_id;
		}

		if ( '' !== $address ) {
			$defaults['address'] = $address;
		}

		if ( $has_geo ) {
			$defaults['latitude']  = $latitude;
			$defaults['longitude'] = $longitude;
			$defaults['has_geo']   = true;
		}

		return $defaults;
	}
}

if ( ! function_exists( 'directorist_custom_code_get_search_location_state' ) ) {
	/**
	 * Return the normalized state submitted by the custom location search field.
	 *
	 * @return string
	 */
	function directorist_custom_code_get_search_location_state() {
		$state = directorist_custom_code_get_request_scalar( 'dcc_profile_location_state' );

		if ( in_array( $state, array( 'cleared', 'custom' ), true ) ) {
			return $state;
		}

		$requested_components = array_filter( array_map( 'trim', explode( ',', $state ) ) );
		$components           = array();

		foreach ( array( 'taxonomy', 'address' ) as $component ) {
			if ( in_array( $component, $requested_components, true ) ) {
				$components[] = $component;
			}
		}

		return implode( ',', $components );
	}
}

if ( ! function_exists( 'directorist_custom_code_get_profile_search_request_components' ) ) {
	/**
	 * Return profile-derived location components that remain unchanged in the request.
	 *
	 * @param array $defaults Profile search-location defaults.
	 * @return string[]
	 */
	function directorist_custom_code_get_profile_search_request_components( $defaults = array() ) {
		$state      = directorist_custom_code_get_search_location_state();
		$components = array();

		if ( empty( $defaults ) || in_array( $state, array( '', 'cleared', 'custom' ), true ) ) {
			return $components;
		}

		$state_components = explode( ',', $state );
		$location_ids      = wp_parse_id_list( directorist_custom_code_get_request_scalar( 'in_loc' ) );
		$location_id       = ! empty( $location_ids ) ? (int) reset( $location_ids ) : 0;

		if (
			in_array( 'taxonomy', $state_components, true ) &&
			! empty( $defaults['location_id'] ) &&
			$location_id === (int) $defaults['location_id'] &&
			'' === directorist_custom_code_get_request_scalar( 'loc_id' )
		) {
			$components[] = 'taxonomy';
		}

		if (
			in_array( 'address', $state_components, true ) &&
			! empty( $defaults['address'] ) &&
			directorist_custom_code_get_request_scalar( 'address' ) === $defaults['address'] &&
			directorist_custom_code_get_request_scalar( 'cityLat' ) === $defaults['latitude'] &&
			directorist_custom_code_get_request_scalar( 'cityLng' ) === $defaults['longitude']
		) {
			$components[] = 'address';
		}

		return $components;
	}
}

if ( ! function_exists( 'directorist_custom_code_get_location_path' ) ) {
	/**
	 * Build a parent > child label for a location term.
	 *
	 * @param int|WP_Term $term      Term object or ID.
	 * @param string      $separator Label separator.
	 * @return string
	 */
	function directorist_custom_code_get_location_path( $term, $separator = ' > ' ) {
		$taxonomy = directorist_custom_code_location_taxonomy();
		$term     = is_object( $term ) ? $term : get_term( absint( $term ), $taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}

		$taxonomy = ! empty( $term->taxonomy ) ? $term->taxonomy : $taxonomy;
		$terms    = array();

		$ancestor_ids = array_reverse( get_ancestors( (int) $term->term_id, $taxonomy, 'taxonomy' ) );
		foreach ( $ancestor_ids as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $taxonomy );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$terms[] = $ancestor->name;
			}
		}

		$terms[] = $term->name;

		return implode( $separator, $terms );
	}
}

if ( ! function_exists( 'directorist_custom_code_get_hierarchical_location_terms' ) ) {
	/**
	 * Return location terms in parent-first hierarchy order.
	 *
	 * @param string $taxonomy     Taxonomy slug.
	 * @param string $listing_type Optional Directorist listing type filter.
	 * @return WP_Term[]
	 */
	function directorist_custom_code_get_hierarchical_location_terms( $taxonomy = '', $listing_type = '' ) {
		$taxonomy = $taxonomy ? $taxonomy : directorist_custom_code_location_taxonomy();
		$terms    = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$children = array();
		foreach ( $terms as $term ) {
			$parent = (int) $term->parent;
			if ( ! isset( $children[ $parent ] ) ) {
				$children[ $parent ] = array();
			}
			$children[ $parent ][] = $term;
		}

		foreach ( $children as &$siblings ) {
			usort(
				$siblings,
				static function ( $a, $b ) {
					return strnatcasecmp( $a->name, $b->name );
				}
			);
		}
		unset( $siblings );

		$ordered = array();
		$walk    = static function ( $parent_id ) use ( &$walk, &$ordered, $children, $listing_type ) {
			if ( empty( $children[ $parent_id ] ) ) {
				return;
			}

			foreach ( $children[ $parent_id ] as $term ) {
				$include = true;

				if ( '' !== $listing_type ) {
					$directory_type = get_term_meta( $term->term_id, '_directory_type', true );
					$directory_type = ! empty( $directory_type ) ? $directory_type : array();
					$directory_type = is_array( $directory_type ) ? $directory_type : array( $directory_type );
					$include        = in_array( $listing_type, $directory_type ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- Directorist stores directory IDs inconsistently as strings/ints.
				}

				if ( $include ) {
					$ordered[] = $term;
				}

				$walk( (int) $term->term_id );
			}
		};

		$walk( 0 );

		return $ordered;
	}
}

if ( ! function_exists( 'directorist_custom_code_location_options_html' ) ) {
	/**
	 * Render location options with full hierarchy labels.
	 *
	 * @param string       $taxonomy     Taxonomy slug.
	 * @param array|int    $selected_ids Selected term IDs.
	 * @param string       $listing_type Optional Directorist listing type filter.
	 * @return string
	 */
	function directorist_custom_code_location_options_html( $taxonomy = '', $selected_ids = array(), $listing_type = '' ) {
		$selected_ids = array_filter( array_map( 'absint', (array) $selected_ids ) );
		$options      = '';

		foreach ( directorist_custom_code_get_hierarchical_location_terms( $taxonomy, $listing_type ) as $term ) {
			$options .= sprintf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $term->term_id ),
				selected( in_array( (int) $term->term_id, $selected_ids, true ), true, false ),
				esc_html( directorist_custom_code_get_location_path( $term ) )
			);
		}

		return $options;
	}
}

if ( ! function_exists( 'directorist_custom_code_get_deepest_location_terms' ) ) {
	/**
	 * Remove selected parent terms when a selected child already represents that branch.
	 *
	 * @param WP_Term[] $terms Location terms.
	 * @return WP_Term[]
	 */
	function directorist_custom_code_get_deepest_location_terms( $terms ) {
		$taxonomy = directorist_custom_code_location_taxonomy();
		$term_ids = array_map(
			static function ( $term ) {
				return (int) $term->term_id;
			},
			(array) $terms
		);

		return array_values(
			array_filter(
				(array) $terms,
				static function ( $term ) use ( $term_ids, $taxonomy ) {
					foreach ( $term_ids as $term_id ) {
						if ( (int) $term->term_id === $term_id ) {
							continue;
						}

						$ancestors = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
						if ( in_array( (int) $term->term_id, $ancestors, true ) ) {
							return false;
						}
					}

					return true;
				}
			)
		);
	}
}

if ( ! function_exists( 'directorist_custom_code_get_listing_location_paths' ) ) {
	/**
	 * Return frontend location labels for a listing.
	 *
	 * @param int $listing_id Listing post ID.
	 * @return string[]
	 */
	function directorist_custom_code_get_listing_location_paths( $listing_id ) {
		$terms = get_the_terms( $listing_id, directorist_custom_code_location_taxonomy() );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return array();
		}

		$paths = array_map( 'directorist_custom_code_get_location_path', directorist_custom_code_get_deepest_location_terms( $terms ) );
		$paths = array_filter( $paths );

		natcasesort( $paths );

		return array_values( $paths );
	}
}

if ( ! function_exists( 'directorist_custom_code_get_listing_location_links' ) ) {
	/**
	 * Return linked frontend location labels for a listing.
	 *
	 * @param int $listing_id Listing post ID.
	 * @return string[]
	 */
	function directorist_custom_code_get_listing_location_links( $listing_id ) {
		$terms = get_the_terms( $listing_id, directorist_custom_code_location_taxonomy() );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return array();
		}

		$links = array();
		foreach ( directorist_custom_code_get_deepest_location_terms( $terms ) as $term ) {
			$url = get_term_link( $term );
			if ( is_wp_error( $url ) ) {
				continue;
			}

			$links[ directorist_custom_code_get_location_path( $term ) ] = sprintf(
				'<a href="%1$s" rel="tag">%2$s</a>',
				esc_url( $url ),
				esc_html( directorist_custom_code_get_location_path( $term ) )
			);
		}

		ksort( $links, SORT_NATURAL | SORT_FLAG_CASE );

		return array_values( $links );
	}
}

add_filter(
	'term_links-' . directorist_custom_code_location_taxonomy(),
	static function ( $links ) {
		if ( is_admin() ) {
			return $links;
		}

		$listing_id = get_the_ID();
		if ( ! $listing_id ) {
			return $links;
		}

		$location_links = directorist_custom_code_get_listing_location_links( $listing_id );

		return ! empty( $location_links ) ? $location_links : $links;
	}
);

/**
 * Persist profile latitude / longitude (dashboard tab-profile custom fields).
 */
add_action(
	'directorist_user_profile_updated',
	static function ( $user_id, $data ) {
		$latitude  = isset( $data['latitude'] ) ? sanitize_text_field( trim( wp_unslash( $data['latitude'] ) ) ) : '';
		$longitude = isset( $data['longitude'] ) ? sanitize_text_field( trim( wp_unslash( $data['longitude'] ) ) ) : '';
		$default_categories = array();
		$default_locations  = array();

		if ( isset( $data['default_categories'] ) ) {
			$default_categories = (array) wp_unslash( $data['default_categories'] );
			$default_categories = array_filter( array_map( 'absint', $default_categories ) );
		}

		if ( isset( $data['default_locations'] ) ) {
			$default_locations = (array) wp_unslash( $data['default_locations'] );
			$default_locations = array_filter( array_map( 'absint', $default_locations ) );
			$default_locations = array_slice( $default_locations, 0, 1 );
		}

		update_user_meta( $user_id, 'latitude', $latitude );
		update_user_meta( $user_id, 'longitude', $longitude );
		update_user_meta( $user_id, 'default_categories', $default_categories );
		update_user_meta( $user_id, 'default_locations', $default_locations );
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
