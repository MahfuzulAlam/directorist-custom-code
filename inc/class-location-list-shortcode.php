<?php
/**
 * Directorist location list shortcode.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Location_List_Shortcode' ) ) {
	/**
	 * Register and render the Directorist location list shortcode.
	 */
	final class Directorist_Custom_Code_Location_List_Shortcode {
		/** Shortcode tag. */
		const SHORTCODE = 'directorist_location_list';

		/** @var Directorist_Custom_Code_Location_List_Shortcode|null */
		private static $instance = null;

		/** @var array<int,int> */
		private $counts = array();

		/**
		 * Get singleton instance.
		 *
		 * @return Directorist_Custom_Code_Location_List_Shortcode
		 */
		public static function instance() {
			if ( ! self::$instance instanceof self ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/** Register the shortcode. */
		private function __construct() {
			add_shortcode( self::SHORTCODE, array( $this, 'render' ) );
		}

		/**
		 * Get shortcode defaults.
		 *
		 * @return array<string,mixed>
		 */
		private function get_defaults() {
			$defaults = array(
				'columns'            => 2,
				'number'             => 0,
				'location_num'       => '',
				'location_number'    => '',
				'number_of_location' => '',
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
			);

			return apply_filters( 'directorist_custom_location_list_shortcode_defaults', $defaults );
		}

		/**
		 * Render shortcode output.
		 *
		 * @param array|string $raw_atts Raw shortcode attributes.
		 * @param string       $content Shortcode content.
		 * @param string       $shortcode_tag Shortcode tag.
		 * @return string
		 */
		public function render( $raw_atts = array(), $content = '', $shortcode_tag = self::SHORTCODE ) {
			unset( $content, $shortcode_tag );

			$atts     = $this->normalize_atts( $raw_atts );
			$taxonomy = $this->get_taxonomy( $atts );

			if ( ! taxonomy_exists( $taxonomy ) ) {
				return '';
			}

			$parents = $this->get_parent_terms( $atts, $taxonomy );
			$cards   = '';

			foreach ( $parents as $parent ) {
				if ( $atts['hide_empty'] && 0 === $this->get_location_count( $parent, $atts, $taxonomy ) ) {
					continue;
				}

				$children = $this->get_child_terms( $parent, $atts, $taxonomy );
				$cards   .= $this->render_parent_card( $parent, $children, $atts, $taxonomy );
			}

			if ( '' === $cards ) {
				return '';
			}

			$classes = $this->get_classes(
				'wrapper',
				array(
					'directorist-custom-location-list',
					'directorist-custom-location-list--columns-' . $atts['columns'],
					$atts['default_open'] ? 'directorist-custom-location-list--default-open' : 'directorist-custom-location-list--default-closed',
				),
				array( 'atts' => $atts, 'taxonomy' => $taxonomy )
			);
			$style = sprintf( '--directorist-custom-location-list-columns:%d', $atts['columns'] );
			$style = apply_filters( 'directorist_custom_location_list_wrapper_style', $style, $atts, $taxonomy );

			ob_start();
			do_action( 'directorist_custom_location_list_before_wrapper', $atts, $taxonomy );
			?>
			<div class="<?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $style ); ?>">
				<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php
			do_action( 'directorist_custom_location_list_after_wrapper', $atts, $taxonomy );

			return apply_filters( 'directorist_custom_location_list_shortcode_output', ob_get_clean(), $atts, $taxonomy, $parents );
		}

		/**
		 * Normalize shortcode attributes.
		 *
		 * @param array|string $raw_atts Raw attributes.
		 * @return array<string,mixed>
		 */
		private function normalize_atts( $raw_atts ) {
			$atts      = shortcode_atts( $this->get_defaults(), (array) $raw_atts, self::SHORTCODE );
			$number    = absint( $atts['number'] );
			$max_child = absint( $atts['max_child'] );

			foreach ( array( 'location_num', 'location_number', 'number_of_location' ) as $key ) {
				if ( '' !== $atts[ $key ] ) {
					$number = absint( $atts[ $key ] );
					break;
				}
			}

			foreach ( array( 'child_number', 'child_num', 'max_children' ) as $key ) {
				if ( '' !== $atts[ $key ] ) {
					$max_child = absint( $atts[ $key ] );
					break;
				}
			}

			$default_open = 'closed' !== strtolower( sanitize_key( $atts['default_state'] ) );
			$normalized   = array(
				'columns'       => max( 1, min( 6, absint( $atts['columns'] ) ) ),
				'number'        => $number,
				'max_child'     => $max_child,
				'display_count' => $this->to_bool( $atts['display_count'] ),
				'hide_empty'    => $this->to_bool( $atts['hide_empty'] ),
				'default_state' => $default_open ? 'open' : 'closed',
				'default_open'  => $default_open,
				'parent_ids'    => $this->parse_list( $atts['parent_ids'] ),
				'parent_slugs'  => $this->parse_list( $atts['parent_slugs'] ),
				'parents'       => $this->parse_list( $atts['parents'] ),
				'raw'           => $atts,
			);

			return apply_filters( 'directorist_custom_location_list_atts', $normalized, $atts );
		}

		/** Get the Directorist location taxonomy. */
		private function get_taxonomy( $atts ) {
			$taxonomy = defined( 'ATBDP_LOCATION' ) ? ATBDP_LOCATION : 'at_biz_dir-location';

			return apply_filters( 'directorist_custom_location_list_taxonomy', $taxonomy, $atts );
		}

		/** Get top-level location terms. */
		private function get_parent_terms( $atts, $taxonomy ) {
			$include_ids = $this->resolve_parent_ids( $atts['parent_ids'], $atts['parent_slugs'], $atts['parents'], $taxonomy );
			$args        = array(
				'taxonomy'   => $taxonomy,
				'parent'     => 0,
				'hide_empty' => false,
				'orderby'    => empty( $include_ids ) ? 'name' : 'include',
				'order'      => 'ASC',
			);

			if ( $atts['number'] > 0 ) {
				$args['number'] = $atts['number'];
			}
			if ( ! empty( $include_ids ) ) {
				$args['include'] = $include_ids;
			}

			$args    = apply_filters( 'directorist_custom_location_list_parent_args', $args, $atts, $taxonomy, $include_ids );
			$parents = get_terms( $args );
			$parents = is_wp_error( $parents ) ? array() : $parents;

			return apply_filters( 'directorist_custom_location_list_parent_terms', $parents, $atts, $taxonomy );
		}

		/** Get direct child locations for a parent. */
		private function get_child_terms( $parent, $atts, $taxonomy ) {
			$args = array(
				'taxonomy'   => $taxonomy,
				'parent'     => $parent->term_id,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			);

			if ( $atts['max_child'] > 0 && ! $atts['hide_empty'] ) {
				$args['number'] = $atts['max_child'];
			}

			$args     = apply_filters( 'directorist_custom_location_list_child_args', $args, $parent, $atts, $taxonomy );
			$children = get_terms( $args );
			$children = is_wp_error( $children ) ? array() : $children;

			if ( $atts['hide_empty'] ) {
				$children = array_values(
					array_filter(
						$children,
						function ( $child ) use ( $atts, $taxonomy ) {
							return $this->get_location_count( $child, $atts, $taxonomy ) > 0;
						}
					)
				);
			}

			if ( $atts['max_child'] > 0 && $atts['hide_empty'] ) {
				$children = array_slice( $children, 0, $atts['max_child'] );
			}

			return apply_filters( 'directorist_custom_location_list_child_terms', $children, $parent, $atts, $taxonomy );
		}

		/** Render one parent location card. */
		private function render_parent_card( $parent, $children, $atts, $taxonomy ) {
			$has_children = ! empty( $children );
			$item_id      = 'directorist-custom-location-list-' . (int) $parent->term_id;
			$context      = compact( 'parent', 'children', 'has_children', 'item_id', 'atts', 'taxonomy' );
			$custom_html  = apply_filters( 'directorist_custom_location_list_parent_html', '', $parent, $children, $atts, $taxonomy, $context );

			if ( is_string( $custom_html ) && '' !== $custom_html ) {
				return $custom_html;
			}

			$card_classes = $this->get_classes(
				'card',
				array(
					'directorist-custom-location-list__card',
					$has_children ? 'directorist-custom-location-list__card--has-children' : 'directorist-custom-location-list__card--no-children',
					$has_children && $atts['default_open'] ? 'is-open' : '',
					$has_children && ! $atts['default_open'] ? 'is-closed' : '',
				),
				$context
			);

			ob_start();
			do_action( 'directorist_custom_location_list_before_parent', $parent, $children, $atts, $taxonomy );
			?>
			<section class="<?php echo esc_attr( $card_classes ); ?>">
				<?php $this->render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ); ?>
				<?php if ( $has_children ) : ?>
					<?php $this->render_children( $parent, $children, $item_id, $atts, $taxonomy, $context ); ?>
				<?php endif; ?>
			</section>
			<?php
			do_action( 'directorist_custom_location_list_after_parent', $parent, $children, $atts, $taxonomy );

			return ob_get_clean();
		}

		/** Render a parent location header. */
		private function render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ) {
			?>
			<div class="<?php echo esc_attr( $this->get_classes( 'header', array( 'directorist-custom-location-list__header' ), $context ) ); ?>">
				<a class="<?php echo esc_attr( $this->get_classes( 'parent_link', array( 'directorist-custom-location-list__parent-link' ), $context ) ); ?>" href="<?php echo esc_url( $this->get_location_link( $parent, $atts, $taxonomy ) ); ?>">
					<?php echo esc_html( $this->get_location_name( $parent, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $parent, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php if ( $has_children ) : ?>
					<button type="button" class="<?php echo esc_attr( $this->get_classes( 'toggle', array( 'directorist-custom-location-list__toggle' ), $context ) ); ?>" aria-expanded="<?php echo esc_attr( $atts['default_open'] ? 'true' : 'false' ); ?>" aria-controls="<?php echo esc_attr( $item_id ); ?>" aria-label="<?php echo esc_attr( $this->get_toggle_label( $parent, $atts, $taxonomy ) ); ?>">
						<span class="<?php echo esc_attr( $this->get_classes( 'icon', array( 'directorist-custom-location-list__icon' ), $context ) ); ?>" aria-hidden="true"></span>
					</button>
				<?php endif; ?>
			</div>
			<?php
		}

		/** Render a child location list. */
		private function render_children( $parent, $children, $item_id, $atts, $taxonomy, $context ) {
			?>
			<div id="<?php echo esc_attr( $item_id ); ?>" class="<?php echo esc_attr( $this->get_classes( 'children', array( 'directorist-custom-location-list__children' ), $context ) ); ?>"<?php echo $atts['default_open'] ? '' : ' hidden'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php do_action( 'directorist_custom_location_list_before_children', $parent, $children, $atts, $taxonomy ); ?>
				<ul class="<?php echo esc_attr( $this->get_classes( 'children_list', array( 'directorist-custom-location-list__children-list' ), $context ) ); ?>">
					<?php foreach ( $children as $child ) : ?>
						<?php $this->render_child_item( $child, $parent, $atts, $taxonomy, $context ); ?>
					<?php endforeach; ?>
				</ul>
				<?php do_action( 'directorist_custom_location_list_after_children', $parent, $children, $atts, $taxonomy ); ?>
			</div>
			<?php
		}

		/** Render a child location. */
		private function render_child_item( $child, $parent, $atts, $taxonomy, $context ) {
			$context['child']  = $child;
			$context['parent'] = $parent;
			?>
			<li class="<?php echo esc_attr( $this->get_classes( 'child_item', array( 'directorist-custom-location-list__child' ), $context ) ); ?>">
				<a class="<?php echo esc_attr( $this->get_classes( 'child_link', array( 'directorist-custom-location-list__child-link' ), $context ) ); ?>" href="<?php echo esc_url( $this->get_location_link( $child, $atts, $taxonomy ) ); ?>">
					<?php echo esc_html( $this->get_location_name( $child, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $child, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
			<?php
		}

		/** Get a filtered location name. */
		private function get_location_name( $term, $atts, $taxonomy ) {
			return apply_filters( 'directorist_custom_location_list_location_name', $term->name, $term, $atts, $taxonomy );
		}

		/** Get optional listing count markup. */
		private function get_count_html( $term, $atts, $taxonomy ) {
			if ( ! $atts['display_count'] ) {
				return '';
			}

			$count  = $this->get_location_count( $term, $atts, $taxonomy );
			$class  = $this->get_classes( 'count', array( 'directorist-custom-location-list__count' ), array( 'term' => $term, 'atts' => $atts, 'taxonomy' => $taxonomy ) );
			$markup = sprintf( ' <span class="%s">(%s)</span>', esc_attr( $class ), esc_html( number_format_i18n( $count ) ) );

			return apply_filters( 'directorist_custom_location_list_count_html', $markup, $count, $term, $atts, $taxonomy );
		}

		/** Get a Directorist location listing count. */
		private function get_location_count( $term, $atts, $taxonomy ) {
			if ( isset( $this->counts[ $term->term_id ] ) ) {
				$count = $this->counts[ $term->term_id ];
			} elseif ( function_exists( 'atbdp_listings_count_by_location' ) ) {
				$count = (int) atbdp_listings_count_by_location( $term->term_id );
			} else {
				$count = (int) $term->count;
			}

			$this->counts[ $term->term_id ] = $count;

			return (int) apply_filters( 'directorist_custom_location_list_location_count', $count, $term, $atts, $taxonomy );
		}

		/** Get a Directorist location URL. */
		private function get_location_link( $term, $atts, $taxonomy ) {
			if ( class_exists( 'ATBDP_Permalink' ) && method_exists( 'ATBDP_Permalink', 'atbdp_get_location_page' ) ) {
				$link = ATBDP_Permalink::atbdp_get_location_page( $term );
			} else {
				$link = get_term_link( $term );
				$link = is_wp_error( $link ) ? '#' : $link;
			}

			return apply_filters( 'directorist_custom_location_list_location_link', $link, $term, $atts, $taxonomy );
		}

		/** Get the toggle button label. */
		private function get_toggle_label( $parent, $atts, $taxonomy ) {
			$label = sprintf(
				/* translators: %s: Location name. */
				__( 'Toggle %s child locations', 'directorist-custom-code' ),
				$parent->name
			);

			return apply_filters( 'directorist_custom_location_list_toggle_label', $label, $parent, $atts, $taxonomy );
		}

		/** Get filtered and sanitized element classes. */
		private function get_classes( $element, $classes, $context ) {
			$classes = apply_filters( 'directorist_custom_location_list_classes', $classes, $element, $context );

			if ( is_string( $classes ) ) {
				$classes = preg_split( '/\s+/', $classes );
			}

			if ( ! is_array( $classes ) ) {
				return '';
			}

			return implode( ' ', array_values( array_unique( array_filter( array_map( 'sanitize_html_class', $classes ) ) ) ) );
		}

		/** Resolve parent IDs from ID and slug filters. */
		private function resolve_parent_ids( $parent_ids, $parent_slugs, $parents, $taxonomy ) {
			foreach ( $parents as $parent ) {
				if ( is_numeric( $parent ) ) {
					$parent_ids[] = $parent;
				} else {
					$parent_slugs[] = $parent;
				}
			}

			$ids = array_filter( array_map( 'absint', $parent_ids ) );
			foreach ( $parent_slugs as $slug ) {
				$term = get_term_by( 'slug', sanitize_title( $slug ), $taxonomy );
				if ( $term && ! is_wp_error( $term ) ) {
					$ids[] = (int) $term->term_id;
				}
			}

			return array_values( array_unique( $ids ) );
		}

		/** Convert a shortcode boolean value to bool. */
		private function to_bool( $value ) {
			if ( is_bool( $value ) ) {
				return $value;
			}

			return in_array( strtolower( trim( (string) $value ) ), array( '1', 'true', 'yes', 'on' ), true );
		}

		/** Parse a comma-separated shortcode attribute. */
		private function parse_list( $value ) {
			$items = array_map( 'sanitize_text_field', array_map( 'trim', explode( ',', (string) $value ) ) );

			return array_values( array_unique( array_filter( $items ) ) );
		}
	}
}
