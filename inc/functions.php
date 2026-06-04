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

if ( ! function_exists( 'directorist_custom_code_to_bool' ) ) {
	/**
	 * Convert shortcode boolean-like values to a bool.
	 *
	 * @param mixed $value Shortcode attribute value.
	 * @return bool
	 */
	function directorist_custom_code_to_bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		return in_array( strtolower( trim( (string) $value ) ), array( '1', 'true', 'yes', 'on' ), true );
	}
}

if ( ! function_exists( 'directorist_custom_code_parse_list' ) ) {
	/**
	 * Parse comma-separated shortcode values.
	 *
	 * @param string $value Comma-separated value.
	 * @return array
	 */
	function directorist_custom_code_parse_list( $value ) {
		$items = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );

		return array_values( array_unique( $items ) );
	}
}

if ( ! function_exists( 'directorist_custom_code_get_category_link' ) ) {
	/**
	 * Get a Directorist category URL with a WordPress fallback.
	 *
	 * @param WP_Term $term Category term.
	 * @return string
	 */
	function directorist_custom_code_get_category_link( $term ) {
		if ( class_exists( 'ATBDP_Permalink' ) && method_exists( 'ATBDP_Permalink', 'atbdp_get_category_page' ) ) {
			return ATBDP_Permalink::atbdp_get_category_page( $term );
		}

		$link = get_term_link( $term );

		return is_wp_error( $link ) ? '#' : $link;
	}
}

if ( ! function_exists( 'directorist_custom_code_get_category_count' ) ) {
	/**
	 * Get listing count for a category.
	 *
	 * @param WP_Term $term Category term.
	 * @return int
	 */
	function directorist_custom_code_get_category_count( $term ) {
		static $counts = array();

		if ( isset( $counts[ $term->term_id ] ) ) {
			return $counts[ $term->term_id ];
		}

		if ( function_exists( 'atbdp_listings_count_by_category' ) ) {
			$counts[ $term->term_id ] = (int) atbdp_listings_count_by_category( $term->term_id );

			return $counts[ $term->term_id ];
		}

		$counts[ $term->term_id ] = (int) $term->count;

		return $counts[ $term->term_id ];
	}
}

if ( ! function_exists( 'directorist_custom_code_get_parent_category_ids' ) ) {
	/**
	 * Resolve parent category IDs from shortcode ID and slug filters.
	 *
	 * @param array  $parent_ids   Parent IDs.
	 * @param array  $parent_slugs Parent slugs.
	 * @param string $taxonomy     Taxonomy name.
	 * @return array
	 */
	function directorist_custom_code_get_parent_category_ids( $parent_ids, $parent_slugs, $taxonomy ) {
		$ids = array_filter( array_map( 'absint', $parent_ids ) );

		foreach ( $parent_slugs as $slug ) {
			$term = get_term_by( 'slug', sanitize_title( $slug ), $taxonomy );

			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return array_values( array_unique( $ids ) );
	}
}

if ( ! function_exists( 'directorist_custom_code_category_count_html' ) ) {
	/**
	 * Build optional count HTML.
	 *
	 * @param WP_Term $term          Category term.
	 * @param bool    $display_count Whether to display count.
	 * @return string
	 */
	function directorist_custom_code_category_count_html( $term, $display_count ) {
		if ( ! $display_count ) {
			return '';
		}

		return sprintf(
			' <span class="directorist-custom-category-list__count">(%s)</span>',
			esc_html( number_format_i18n( directorist_custom_code_get_category_count( $term ) ) )
		);
	}
}

if ( ! function_exists( 'directorist_custom_code_category_list_shortcode' ) ) {
	/**
	 * Display parent Directorist categories with collapsible child categories.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function directorist_custom_code_category_list_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'            => 2,
				'number'             => 0,
				'category_num'       => '',
				'category_number'    => '',
				'number_of_category' => '',
				'max_child'          => 0,
				'child_number'       => '',
				'child_num'          => '',
				'max_children'       => '',
				'display_count'      => 'yes',
				'hide_empty'         => 'no',
				'default_state'      => 'open',
				'parent_ids'         => '',
				'parent_slugs'       => '',
				'parents'            => '',
			),
			(array) $atts,
			'directorist_category_list'
		);

		$taxonomy = defined( 'ATBDP_CATEGORY' ) ? ATBDP_CATEGORY : 'at_biz_dir-category';

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return '';
		}

		$columns       = max( 1, min( 6, absint( $atts['columns'] ) ) );
		$number        = absint( $atts['number'] );
		$max_child     = absint( $atts['max_child'] );
		$display_count = directorist_custom_code_to_bool( $atts['display_count'] );
		$hide_empty    = directorist_custom_code_to_bool( $atts['hide_empty'] );
		$default_open  = 'closed' !== strtolower( trim( (string) $atts['default_state'] ) );

		foreach ( array( 'category_num', 'category_number', 'number_of_category' ) as $number_key ) {
			if ( '' !== $atts[ $number_key ] ) {
				$number = absint( $atts[ $number_key ] );
				break;
			}
		}

		foreach ( array( 'child_number', 'child_num', 'max_children' ) as $child_number_key ) {
			if ( '' !== $atts[ $child_number_key ] ) {
				$max_child = absint( $atts[ $child_number_key ] );
				break;
			}
		}

		$parent_ids   = directorist_custom_code_parse_list( $atts['parent_ids'] );
		$parent_slugs = directorist_custom_code_parse_list( $atts['parent_slugs'] );

		foreach ( directorist_custom_code_parse_list( $atts['parents'] ) as $parent ) {
			if ( is_numeric( $parent ) ) {
				$parent_ids[] = $parent;
			} else {
				$parent_slugs[] = $parent;
			}
		}

		$include_ids = directorist_custom_code_get_parent_category_ids( $parent_ids, $parent_slugs, $taxonomy );

		$parent_args = array(
			'taxonomy'   => $taxonomy,
			'parent'     => 0,
			'hide_empty' => false,
			'orderby'    => ! empty( $include_ids ) ? 'include' : 'name',
			'order'      => 'ASC',
		);

		if ( $number > 0 ) {
			$parent_args['number'] = $number;
		}

		if ( ! empty( $include_ids ) ) {
			$parent_args['include'] = $include_ids;
		}

		$parents = get_terms( $parent_args );

		if ( is_wp_error( $parents ) || empty( $parents ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="directorist-custom-category-list" style="<?php echo esc_attr( '--directorist-custom-category-list-columns:' . $columns ); ?>">
			<?php
			foreach ( $parents as $parent ) {
				$parent_count = directorist_custom_code_get_category_count( $parent );

				if ( $hide_empty && 0 === $parent_count ) {
					continue;
				}

				$child_args = array(
					'taxonomy'   => $taxonomy,
					'parent'     => $parent->term_id,
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				);

				if ( $max_child > 0 && ! $hide_empty ) {
					$child_args['number'] = $max_child;
				}

				$children = get_terms( $child_args );

				if ( is_wp_error( $children ) ) {
					$children = array();
				}

				if ( $hide_empty ) {
					$children = array_values(
						array_filter(
							$children,
							static function ( $child ) {
								return directorist_custom_code_get_category_count( $child ) > 0;
							}
						)
					);
				}

				if ( $max_child > 0 && $hide_empty ) {
					$children = array_slice( $children, 0, $max_child );
				}

				$has_children = ! empty( $children );
				$item_id      = 'directorist-custom-category-list-' . (int) $parent->term_id;
				$card_class   = $has_children ? 'directorist-custom-category-list__card' : 'directorist-custom-category-list__card directorist-custom-category-list__card--no-children';
				?>
				<section class="<?php echo esc_attr( $card_class ); ?>">
					<div class="directorist-custom-category-list__header">
						<a class="directorist-custom-category-list__parent-link" href="<?php echo esc_url( directorist_custom_code_get_category_link( $parent ) ); ?>">
							<?php echo esc_html( $parent->name ); ?><?php echo directorist_custom_code_category_count_html( $parent, $display_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php if ( $has_children ) : ?>
							<button
								type="button"
								class="directorist-custom-category-list__toggle"
								aria-expanded="<?php echo esc_attr( $default_open ? 'true' : 'false' ); ?>"
								aria-controls="<?php echo esc_attr( $item_id ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Toggle %s child categories', 'directorist-custom-code' ), $parent->name ) ); ?>"
							>
								<span class="directorist-custom-category-list__icon" aria-hidden="true"></span>
							</button>
						<?php endif; ?>
					</div>

					<?php if ( $has_children ) : ?>
						<div id="<?php echo esc_attr( $item_id ); ?>" class="directorist-custom-category-list__children" <?php echo $default_open ? '' : 'hidden'; ?>>
							<ul class="directorist-custom-category-list__children-list">
								<?php foreach ( $children as $child ) : ?>
									<li class="directorist-custom-category-list__child">
										<a class="directorist-custom-category-list__child-link" href="<?php echo esc_url( directorist_custom_code_get_category_link( $child ) ); ?>">
											<?php echo esc_html( $child->name ); ?><?php echo directorist_custom_code_category_count_html( $child, $display_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</section>
				<?php
			}
			?>
		</div>
		<?php

		return ob_get_clean();
	}
}

add_shortcode( 'directorist_category_list', 'directorist_custom_code_category_list_shortcode' );
