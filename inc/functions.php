<?php

/**
 * Add your custom php code here
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
