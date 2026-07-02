<?php
/**
 * Shared Directorist taxonomy list shortcode renderer.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Taxonomy_List_Shortcode' ) ) {
	/**
	 * Shared renderer for category and location taxonomy list shortcodes.
	 */
	abstract class Directorist_Custom_Code_Taxonomy_List_Shortcode {
		/**
		 * Count cache.
		 *
		 * @var array<int,int>
		 */
		private $counts = array();

		/**
		 * Render counter used to keep ARIA control IDs unique on pages with repeated shortcodes.
		 *
		 * @var int
		 */
		private $render_index = 0;

		/**
		 * Register the shortcode.
		 */
		protected function __construct() {
			add_shortcode( $this->get_shortcode(), array( $this, 'render' ) );
		}

		/**
		 * Get shortcode tag.
		 *
		 * @return string
		 */
		abstract protected function get_shortcode();

		/**
		 * Get hook prefix.
		 *
		 * @return string
		 */
		abstract protected function get_hook_prefix();

		/**
		 * Get CSS class base.
		 *
		 * @return string
		 */
		abstract protected function get_css_base();

		/**
		 * Get taxonomy default.
		 *
		 * @return string
		 */
		abstract protected function get_default_taxonomy();

		/**
		 * Get parent number aliases.
		 *
		 * @return array
		 */
		abstract protected function get_number_aliases();

		/**
		 * Get term type key used by term-specific hooks.
		 *
		 * @return string
		 */
		abstract protected function get_term_type();

		/**
		 * Get plural term label.
		 *
		 * @return string
		 */
		abstract protected function get_term_label_plural();

		/**
		 * Get Directorist-specific term URL.
		 *
		 * @param WP_Term $term Term object.
		 * @return string
		 */
		abstract protected function get_directorist_link( $term );

		/**
		 * Get Directorist-specific listing count.
		 *
		 * @param WP_Term $term Term object.
		 * @return int
		 */
		abstract protected function get_directorist_count( $term );

		/**
		 * Render shortcode output.
		 *
		 * @param array|string $raw_atts      Shortcode attributes.
		 * @param string       $content       Shortcode content.
		 * @param string       $shortcode_tag Shortcode tag.
		 * @return string
		 */
		public function render( $raw_atts = array(), $content = '', $shortcode_tag = '' ) {
			unset( $content, $shortcode_tag );

			$atts     = $this->normalize_atts( $raw_atts );
			$taxonomy = $this->get_taxonomy( $atts );

			if ( ! taxonomy_exists( $taxonomy ) ) {
				return '';
			}

			$this->render_index++;

			$parents = $this->get_parent_terms( $atts, $taxonomy );

			if ( empty( $parents ) ) {
				return '';
			}

			$cards = '';

			foreach ( $parents as $parent ) {
				if ( $atts['hide_empty'] && 0 === $this->get_term_count( $parent, $atts, $taxonomy ) ) {
					continue;
				}

				$children = $this->get_child_terms( $parent, $atts, $taxonomy, 2 );
				$cards   .= $this->render_parent_card( $parent, $children, $atts, $taxonomy );
			}

			if ( '' === $cards ) {
				return '';
			}

			$base            = $this->get_css_base();
			$wrapper_classes = $this->get_classes(
				'wrapper',
				array(
					$base,
					$base . '--columns-' . $atts['columns'],
					$atts['default_open'] ? $base . '--default-open' : $base . '--default-closed',
				),
				array(
					'atts'     => $atts,
					'taxonomy' => $taxonomy,
				)
			);
			$wrapper_style   = sprintf( '--%s-columns:%d', $base, $atts['columns'] );
			$wrapper_style   = apply_filters( $this->get_hook_prefix() . '_wrapper_style', $wrapper_style, $atts, $taxonomy );

			ob_start();
			do_action( $this->get_hook_prefix() . '_before_wrapper', $atts, $taxonomy );
			?>
			<div class="<?php echo esc_attr( $wrapper_classes ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">
				<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php
			do_action( $this->get_hook_prefix() . '_after_wrapper', $atts, $taxonomy );

			return apply_filters( $this->get_hook_prefix() . '_shortcode_output', ob_get_clean(), $atts, $taxonomy, $parents );
		}

		/**
		 * Get shortcode defaults.
		 *
		 * @return array<string,mixed>
		 */
		protected function get_defaults() {
			$defaults = array(
				'columns'       => 2,
				'number'        => 0,
				'max_child'     => 0,
				'child_number'  => '',
				'child_num'     => '',
				'max_children'  => '',
				'display_count' => 'yes',
				'hide_empty'    => 'no',
				'default_state' => 'open',
				'parent_ids'    => '',
				'parent_slugs'  => '',
				'parents'       => '',
			);

			foreach ( $this->get_number_aliases() as $alias ) {
				$defaults[ $alias ] = '';
			}

			return apply_filters( $this->get_hook_prefix() . '_shortcode_defaults', $defaults );
		}

		/**
		 * Normalize shortcode attributes.
		 *
		 * @param array|string $raw_atts Raw attributes.
		 * @return array<string,mixed>
		 */
		private function normalize_atts( $raw_atts ) {
			$atts      = shortcode_atts( $this->get_defaults(), (array) $raw_atts, $this->get_shortcode() );
			$number    = absint( $atts['number'] );
			$max_child = absint( $atts['max_child'] );

			foreach ( $this->get_number_aliases() as $key ) {
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
				'max_depth'     => 3,
				'raw'           => $atts,
			);

			return apply_filters( $this->get_hook_prefix() . '_atts', $normalized, $atts );
		}

		/**
		 * Get taxonomy name.
		 *
		 * @param array $atts Normalized attributes.
		 * @return string
		 */
		private function get_taxonomy( $atts ) {
			return apply_filters( $this->get_hook_prefix() . '_taxonomy', $this->get_default_taxonomy(), $atts );
		}

		/**
		 * Get top-level terms.
		 *
		 * @param array  $atts     Normalized attributes.
		 * @param string $taxonomy Taxonomy name.
		 * @return WP_Term[]
		 */
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

			$args    = apply_filters( $this->get_hook_prefix() . '_parent_args', $args, $atts, $taxonomy, $include_ids );
			$parents = get_terms( $args );
			$parents = is_wp_error( $parents ) ? array() : $parents;

			return apply_filters( $this->get_hook_prefix() . '_parent_terms', $parents, $atts, $taxonomy );
		}

		/**
		 * Get direct child terms for any term level.
		 *
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @param int     $level    Level being fetched. Direct children are level 2; grandchildren are level 3.
		 * @return WP_Term[]
		 */
		private function get_child_terms( $parent, $atts, $taxonomy, $level ) {
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

			$args     = apply_filters( $this->get_hook_prefix() . '_child_args', $args, $parent, $atts, $taxonomy, $level );
			$children = get_terms( $args );
			$children = is_wp_error( $children ) ? array() : $children;

			if ( $atts['hide_empty'] ) {
				$children = array_values(
					array_filter(
						$children,
						function ( $child ) use ( $atts, $taxonomy ) {
							return $this->get_term_count( $child, $atts, $taxonomy ) > 0;
						}
					)
				);
			}

			if ( $atts['max_child'] > 0 && $atts['hide_empty'] ) {
				$children = array_slice( $children, 0, $atts['max_child'] );
			}

			return apply_filters( $this->get_hook_prefix() . '_child_terms', $children, $parent, $atts, $taxonomy, $level );
		}

		/**
		 * Render one top-level term card.
		 *
		 * @param WP_Term   $parent   Parent term.
		 * @param WP_Term[] $children Child terms.
		 * @param array     $atts     Normalized attributes.
		 * @param string    $taxonomy Taxonomy name.
		 * @return string
		 */
		private function render_parent_card( $parent, $children, $atts, $taxonomy ) {
			$base         = $this->get_css_base();
			$has_children = ! empty( $children );
			$item_id      = $base . '-' . $this->render_index . '-' . (int) $parent->term_id;
			$context      = array(
				'parent'       => $parent,
				'children'     => $children,
				'has_children' => $has_children,
				'item_id'      => $item_id,
				'atts'         => $atts,
				'taxonomy'     => $taxonomy,
				'level'        => 1,
			);
			$custom_html  = apply_filters( $this->get_hook_prefix() . '_parent_html', '', $parent, $children, $atts, $taxonomy, $context );

			if ( is_string( $custom_html ) && '' !== $custom_html ) {
				return $custom_html;
			}

			$card_classes = $this->get_classes(
				'card',
				array(
					$base . '__card',
					$has_children ? $base . '__card--has-children' : $base . '__card--no-children',
					$has_children && $atts['default_open'] ? 'is-open' : '',
					$has_children && ! $atts['default_open'] ? 'is-closed' : '',
				),
				$context
			);

			ob_start();
			do_action( $this->get_hook_prefix() . '_before_parent', $parent, $children, $atts, $taxonomy );
			?>
			<section class="<?php echo esc_attr( $card_classes ); ?>" data-directorist-taxonomy-list-card data-directorist-taxonomy-list-item>
				<?php $this->render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ); ?>
				<?php if ( $has_children ) : ?>
					<?php $this->render_children( $parent, $children, $item_id, $atts, $taxonomy, $context, 2 ); ?>
				<?php endif; ?>
			</section>
			<?php
			do_action( $this->get_hook_prefix() . '_after_parent', $parent, $children, $atts, $taxonomy );

			return ob_get_clean();
		}

		/**
		 * Render top-level term card header.
		 *
		 * @param WP_Term $parent       Parent term.
		 * @param bool    $has_children Whether the parent has children.
		 * @param string  $item_id      Children wrapper ID.
		 * @param array   $atts         Normalized attributes.
		 * @param string  $taxonomy     Taxonomy name.
		 * @param array   $context      Render context.
		 * @return void
		 */
		private function render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ) {
			$base = $this->get_css_base();
			?>
			<div class="<?php echo esc_attr( $this->get_classes( 'header', array( $base . '__header' ), $context ) ); ?>">
				<a class="<?php echo esc_attr( $this->get_classes( 'parent_link', array( $base . '__parent-link' ), $context ) ); ?>" href="<?php echo esc_url( $this->get_term_link( $parent, $atts, $taxonomy ) ); ?>">
					<?php echo esc_html( $this->get_term_name( $parent, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $parent, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php if ( $has_children ) : ?>
					<button type="button" class="<?php echo esc_attr( $this->get_classes( 'toggle', array( $base . '__toggle' ), $context ) ); ?>" aria-expanded="<?php echo esc_attr( $atts['default_open'] ? 'true' : 'false' ); ?>" aria-controls="<?php echo esc_attr( $item_id ); ?>" aria-label="<?php echo esc_attr( $this->get_toggle_label( $parent, $atts, $taxonomy ) ); ?>" data-directorist-taxonomy-list-toggle>
						<span class="<?php echo esc_attr( $this->get_classes( 'icon', array( $base . '__icon' ), $context ) ); ?>" aria-hidden="true"></span>
					</button>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Render children list.
		 *
		 * @param WP_Term   $parent   Parent term.
		 * @param WP_Term[] $children Child terms.
		 * @param string    $item_id  Children wrapper ID.
		 * @param array     $atts     Normalized attributes.
		 * @param string    $taxonomy Taxonomy name.
		 * @param array     $context  Render context.
		 * @param int       $level    List level.
		 * @return void
		 */
		private function render_children( $parent, $children, $item_id, $atts, $taxonomy, $context, $level ) {
			$base                      = $this->get_css_base();
			$children_context          = $context;
			$children_context['term']  = $parent;
			$children_context['level'] = $level;
			$is_direct_child           = 2 === $level;
			$is_collapsible_list       = '' !== $item_id;
			$children_classes          = $this->get_classes( 'children', array( $base . '__children', $base . '__children--level-' . $level ), $children_context );
			$list_classes              = $this->get_classes( 'children_list', array( $base . '__children-list', $base . '__children-list--level-' . $level ), $children_context );
			$children_attrs            = $is_collapsible_list ? sprintf( ' id="%s"%s', esc_attr( $item_id ), $atts['default_open'] ? '' : ' hidden' ) : '';
			?>
			<div class="<?php echo esc_attr( $children_classes ); ?>"<?php echo $children_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
				if ( $is_direct_child ) {
					do_action( $this->get_hook_prefix() . '_before_children', $parent, $children, $atts, $taxonomy );
				}
				?>
				<ul class="<?php echo esc_attr( $list_classes ); ?>">
					<?php foreach ( $children as $child ) : ?>
						<?php $this->render_child_item( $child, $parent, $atts, $taxonomy, $context, $level ); ?>
					<?php endforeach; ?>
				</ul>
				<?php
				if ( $is_direct_child ) {
					do_action( $this->get_hook_prefix() . '_after_children', $parent, $children, $atts, $taxonomy );
				}
				?>
			</div>
			<?php
		}

		/**
		 * Render child or grandchild term item.
		 *
		 * @param WP_Term $child    Child term.
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @param array   $context  Parent render context.
		 * @param int     $level    Current term level.
		 * @return void
		 */
		private function render_child_item( $child, $parent, $atts, $taxonomy, $context, $level ) {
			$base                      = $this->get_css_base();
			$child_context             = $context;
			$child_context['child']    = $child;
			$child_context['term']     = $child;
			$child_context['parent']   = $parent;
			$child_context['level']    = $level;
			$child_context['children'] = array();
			$grandchildren             = array();

			if ( $level < $atts['max_depth'] ) {
				$grandchildren = $this->get_child_terms( $child, $atts, $taxonomy, $level + 1 );
			}

			$child_context['children']     = $grandchildren;
			$child_context['has_children'] = ! empty( $grandchildren );
			$grandchild_list_id            = $child_context['has_children'] ? $base . '-' . $this->render_index . '-' . (int) $child->term_id : '';
			$item_classes                  = $this->get_classes(
				'child_item',
				array(
					$base . '__child',
					$base . '__child--level-' . $level,
					$child_context['has_children'] ? $base . '__child--has-children' : $base . '__child--no-children',
					$child_context['has_children'] && $atts['default_open'] ? 'is-open' : '',
					$child_context['has_children'] && ! $atts['default_open'] ? 'is-closed' : '',
				),
				$child_context
			);
			$header_classes                = $this->get_classes( 'child_header', array( $base . '__child-header', $base . '__child-header--level-' . $level ), $child_context );
			$link_classes                  = $this->get_classes( 'child_link', array( $base . '__child-link', $base . '__child-link--level-' . $level ), $child_context );
			?>
			<li class="<?php echo esc_attr( $item_classes ); ?>"<?php echo $child_context['has_children'] ? ' data-directorist-taxonomy-list-item' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="<?php echo esc_attr( $header_classes ); ?>">
					<a class="<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( $this->get_term_link( $child, $atts, $taxonomy ) ); ?>">
						<?php echo esc_html( $this->get_term_name( $child, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $child, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<?php if ( ! empty( $grandchildren ) ) : ?>
						<button type="button" class="<?php echo esc_attr( $this->get_classes( 'child_toggle', array( $base . '__toggle', $base . '__child-toggle', $base . '__child-toggle--level-' . $level ), $child_context ) ); ?>" aria-expanded="<?php echo esc_attr( $atts['default_open'] ? 'true' : 'false' ); ?>" aria-controls="<?php echo esc_attr( $grandchild_list_id ); ?>" aria-label="<?php echo esc_attr( $this->get_toggle_label( $child, $atts, $taxonomy ) ); ?>" data-directorist-taxonomy-list-toggle>
							<span class="<?php echo esc_attr( $this->get_classes( 'child_icon', array( $base . '__icon', $base . '__child-icon' ), $child_context ) ); ?>" aria-hidden="true"></span>
						</button>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $grandchildren ) ) : ?>
					<?php $this->render_children( $child, $grandchildren, $grandchild_list_id, $atts, $taxonomy, $child_context, $level + 1 ); ?>
				<?php endif; ?>
			</li>
			<?php
		}

		/**
		 * Get filtered term display name.
		 *
		 * @param WP_Term $term     Term object.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_term_name( $term, $atts, $taxonomy ) {
			return apply_filters( $this->get_hook_prefix() . '_' . $this->get_term_type() . '_name', $term->name, $term, $atts, $taxonomy );
		}

		/**
		 * Get optional count HTML.
		 *
		 * @param WP_Term $term     Term object.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_count_html( $term, $atts, $taxonomy ) {
			if ( ! $atts['display_count'] ) {
				return '';
			}

			$count  = $this->get_term_count( $term, $atts, $taxonomy );
			$class  = $this->get_classes(
				'count',
				array( $this->get_css_base() . '__count' ),
				array(
					'term'     => $term,
					'atts'     => $atts,
					'taxonomy' => $taxonomy,
				)
			);
			$markup = sprintf( ' <span class="%s">(%s)</span>', esc_attr( $class ), esc_html( number_format_i18n( $count ) ) );

			return apply_filters( $this->get_hook_prefix() . '_count_html', $markup, $count, $term, $atts, $taxonomy );
		}

		/**
		 * Get listing count for a term.
		 *
		 * @param WP_Term $term     Term object.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return int
		 */
		private function get_term_count( $term, $atts, $taxonomy ) {
			if ( isset( $this->counts[ $term->term_id ] ) ) {
				$count = $this->counts[ $term->term_id ];
			} else {
				$count                         = $this->get_directorist_count( $term );
				$this->counts[ $term->term_id ] = $count;
			}

			return (int) apply_filters( $this->get_hook_prefix() . '_' . $this->get_term_type() . '_count', $count, $term, $atts, $taxonomy );
		}

		/**
		 * Get term URL.
		 *
		 * @param WP_Term $term     Term object.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_term_link( $term, $atts, $taxonomy ) {
			$link = $this->get_directorist_link( $term );

			if ( '' === $link ) {
				$link = get_term_link( $term );
				$link = is_wp_error( $link ) ? '#' : $link;
			}

			return apply_filters( $this->get_hook_prefix() . '_' . $this->get_term_type() . '_link', $link, $term, $atts, $taxonomy );
		}

		/**
		 * Get toggle aria-label.
		 *
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_toggle_label( $parent, $atts, $taxonomy ) {
			$label = sprintf(
				/* translators: 1: Term name, 2: taxonomy label. */
				__( 'Toggle %1$s child %2$s', 'directorist-custom-code' ),
				$parent->name,
				$this->get_term_label_plural()
			);

			return apply_filters( $this->get_hook_prefix() . '_toggle_label', $label, $parent, $atts, $taxonomy );
		}

		/**
		 * Get filtered and sanitized classes.
		 *
		 * @param string       $element Element key.
		 * @param array|string $classes Classes.
		 * @param array        $context Context.
		 * @return string
		 */
		private function get_classes( $element, $classes, $context ) {
			$classes = apply_filters( $this->get_hook_prefix() . '_classes', $classes, $element, $context );

			if ( is_string( $classes ) ) {
				$classes = preg_split( '/\s+/', $classes );
			}

			if ( ! is_array( $classes ) ) {
				return '';
			}

			return implode( ' ', array_values( array_unique( array_filter( array_map( 'sanitize_html_class', $classes ) ) ) ) );
		}

		/**
		 * Resolve parent IDs from explicit ID and slug filters.
		 *
		 * @param array  $parent_ids   Parent IDs.
		 * @param array  $parent_slugs Parent slugs.
		 * @param array  $parents      Mixed parent IDs/slugs.
		 * @param string $taxonomy     Taxonomy name.
		 * @return array
		 */
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

		/**
		 * Convert a shortcode boolean value to bool.
		 *
		 * @param mixed $value Attribute value.
		 * @return bool
		 */
		private function to_bool( $value ) {
			if ( is_bool( $value ) ) {
				return $value;
			}

			return in_array( strtolower( trim( (string) $value ) ), array( '1', 'true', 'yes', 'on' ), true );
		}

		/**
		 * Parse a comma-separated shortcode attribute.
		 *
		 * @param string $value Comma-separated value.
		 * @return array
		 */
		private function parse_list( $value ) {
			$items = array_map( 'sanitize_text_field', array_map( 'trim', explode( ',', (string) $value ) ) );

			return array_values( array_unique( array_filter( $items ) ) );
		}
	}
}
