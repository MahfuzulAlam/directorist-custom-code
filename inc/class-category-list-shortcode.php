<?php
/**
 * Directorist category list shortcode.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Category_List_Shortcode' ) ) {
	/**
	 * Register and render the Directorist category list shortcode.
	 */
	final class Directorist_Custom_Code_Category_List_Shortcode {
		/**
		 * Shortcode tag.
		 *
		 * @var string
		 */
		const SHORTCODE = 'directorist_category_list';

		/**
		 * Singleton instance.
		 *
		 * @var Directorist_Custom_Code_Category_List_Shortcode|null
		 */
		private static $instance = null;

		/**
		 * Category count cache.
		 *
		 * @var array<int,int>
		 */
		private $counts = array();

		/**
		 * Get singleton instance.
		 *
		 * @return Directorist_Custom_Code_Category_List_Shortcode
		 */
		public static function instance() {
			if ( ! self::$instance instanceof self ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {
			add_shortcode( self::SHORTCODE, array( $this, 'render' ) );
		}

		/**
		 * Shortcode defaults.
		 *
		 * @return array<string,mixed>
		 */
		private function get_defaults() {
			$defaults = array(
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
			);

			/**
			 * Filter the default shortcode attributes.
			 *
			 * @param array $defaults Default shortcode attributes.
			 */
			return apply_filters( 'directorist_custom_category_list_shortcode_defaults', $defaults );
		}

		/**
		 * Render shortcode output.
		 *
		 * @param array|string $raw_atts Shortcode attributes.
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

			if ( empty( $parents ) ) {
				return '';
			}

			$cards = '';

			foreach ( $parents as $parent ) {
				$parent_count = $this->get_category_count( $parent, $atts, $taxonomy );

				if ( $atts['hide_empty'] && 0 === $parent_count ) {
					continue;
				}

				$children = $this->get_child_terms( $parent, $atts, $taxonomy );
				$cards   .= $this->render_parent_card( $parent, $children, $atts, $taxonomy );
			}

			if ( '' === $cards ) {
				return '';
			}

			$wrapper_classes = $this->get_classes(
				'wrapper',
				array(
					'directorist-custom-category-list',
					'directorist-custom-category-list--columns-' . $atts['columns'],
					$atts['default_open'] ? 'directorist-custom-category-list--default-open' : 'directorist-custom-category-list--default-closed',
				),
				array(
					'atts'     => $atts,
					'taxonomy' => $taxonomy,
				)
			);

			$wrapper_style = sprintf( '--directorist-custom-category-list-columns:%d', $atts['columns'] );

			/**
			 * Filter the inline style applied to the shortcode wrapper.
			 *
			 * @param string $wrapper_style Wrapper inline style.
			 * @param array  $atts          Normalized shortcode attributes.
			 * @param string $taxonomy      Taxonomy name.
			 */
			$wrapper_style = apply_filters( 'directorist_custom_category_list_wrapper_style', $wrapper_style, $atts, $taxonomy );

			ob_start();

			/**
			 * Fires before the shortcode wrapper.
			 *
			 * @param array  $atts     Normalized shortcode attributes.
			 * @param string $taxonomy Taxonomy name.
			 */
			do_action( 'directorist_custom_category_list_before_wrapper', $atts, $taxonomy );
			?>
			<div class="<?php echo esc_attr( $wrapper_classes ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">
				<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php
			/**
			 * Fires after the shortcode wrapper.
			 *
			 * @param array  $atts     Normalized shortcode attributes.
			 * @param string $taxonomy Taxonomy name.
			 */
			do_action( 'directorist_custom_category_list_after_wrapper', $atts, $taxonomy );

			$output = ob_get_clean();

			/**
			 * Filter the final shortcode output.
			 *
			 * @param string    $output   Shortcode HTML.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 * @param WP_Term[] $parents  Parent terms.
			 */
			return apply_filters( 'directorist_custom_category_list_shortcode_output', $output, $atts, $taxonomy, $parents );
		}

		/**
		 * Normalize and sanitize shortcode attributes.
		 *
		 * @param array|string $raw_atts Raw shortcode attributes.
		 * @return array<string,mixed>
		 */
		private function normalize_atts( $raw_atts ) {
			$atts = shortcode_atts( $this->get_defaults(), (array) $raw_atts, self::SHORTCODE );

			$number    = absint( $atts['number'] );
			$max_child = absint( $atts['max_child'] );

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

			$default_state = strtolower( sanitize_key( $atts['default_state'] ) );
			$default_open  = 'closed' !== $default_state;

			$normalized = array(
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

			/**
			 * Filter normalized shortcode attributes.
			 *
			 * @param array $normalized Normalized shortcode attributes.
			 * @param array $atts       Raw shortcode attributes after shortcode_atts().
			 */
			return apply_filters( 'directorist_custom_category_list_atts', $normalized, $atts );
		}

		/**
		 * Get the taxonomy used by the shortcode.
		 *
		 * @param array $atts Normalized shortcode attributes.
		 * @return string
		 */
		private function get_taxonomy( $atts ) {
			$taxonomy = defined( 'ATBDP_CATEGORY' ) ? ATBDP_CATEGORY : 'at_biz_dir-category';

			/**
			 * Filter the taxonomy used by the shortcode.
			 *
			 * @param string $taxonomy Taxonomy name.
			 * @param array  $atts     Normalized shortcode attributes.
			 */
			return apply_filters( 'directorist_custom_category_list_taxonomy', $taxonomy, $atts );
		}

		/**
		 * Get parent terms.
		 *
		 * @param array  $atts     Normalized shortcode attributes.
		 * @param string $taxonomy Taxonomy name.
		 * @return WP_Term[]
		 */
		private function get_parent_terms( $atts, $taxonomy ) {
			$include_ids = $this->resolve_parent_ids( $atts['parent_ids'], $atts['parent_slugs'], $atts['parents'], $taxonomy );

			$args = array(
				'taxonomy'   => $taxonomy,
				'parent'     => 0,
				'hide_empty' => false,
				'orderby'    => ! empty( $include_ids ) ? 'include' : 'name',
				'order'      => 'ASC',
			);

			if ( $atts['number'] > 0 ) {
				$args['number'] = $atts['number'];
			}

			if ( ! empty( $include_ids ) ) {
				$args['include'] = $include_ids;
			}

			/**
			 * Filter get_terms() args for parent categories.
			 *
			 * @param array  $args        Parent get_terms() args.
			 * @param array  $atts        Normalized shortcode attributes.
			 * @param string $taxonomy    Taxonomy name.
			 * @param array  $include_ids Resolved parent IDs.
			 */
			$args = apply_filters( 'directorist_custom_category_list_parent_args', $args, $atts, $taxonomy, $include_ids );

			$parents = get_terms( $args );

			if ( is_wp_error( $parents ) ) {
				$parents = array();
			}

			/**
			 * Filter parent category terms before rendering.
			 *
			 * @param WP_Term[] $parents  Parent terms.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_parent_terms', $parents, $atts, $taxonomy );
		}

		/**
		 * Get child terms for a parent category.
		 *
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return WP_Term[]
		 */
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

			/**
			 * Filter get_terms() args for child categories.
			 *
			 * @param array   $args     Child get_terms() args.
			 * @param WP_Term $parent   Parent term.
			 * @param array   $atts     Normalized shortcode attributes.
			 * @param string  $taxonomy Taxonomy name.
			 */
			$args = apply_filters( 'directorist_custom_category_list_child_args', $args, $parent, $atts, $taxonomy );

			$children = get_terms( $args );

			if ( is_wp_error( $children ) ) {
				$children = array();
			}

			if ( $atts['hide_empty'] ) {
				$children = array_values(
					array_filter(
						$children,
						function ( $child ) use ( $atts, $taxonomy ) {
							return $this->get_category_count( $child, $atts, $taxonomy ) > 0;
						}
					)
				);
			}

			if ( $atts['max_child'] > 0 && $atts['hide_empty'] ) {
				$children = array_slice( $children, 0, $atts['max_child'] );
			}

			/**
			 * Filter child category terms before rendering.
			 *
			 * @param WP_Term[] $children Child terms.
			 * @param WP_Term   $parent   Parent term.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_child_terms', $children, $parent, $atts, $taxonomy );
		}

		/**
		 * Render a parent category card.
		 *
		 * @param WP_Term   $parent   Parent term.
		 * @param WP_Term[] $children Child terms.
		 * @param array     $atts     Normalized shortcode attributes.
		 * @param string    $taxonomy Taxonomy name.
		 * @return string
		 */
		private function render_parent_card( $parent, $children, $atts, $taxonomy ) {
			$has_children = ! empty( $children );
			$item_id      = 'directorist-custom-category-list-' . (int) $parent->term_id;
			$context      = array(
				'parent'       => $parent,
				'children'     => $children,
				'has_children' => $has_children,
				'item_id'      => $item_id,
				'atts'         => $atts,
				'taxonomy'     => $taxonomy,
			);

			/**
			 * Filter full parent card HTML.
			 *
			 * Return a non-empty string to replace the default parent card markup.
			 *
			 * @param string    $html     Custom parent card HTML. Default empty.
			 * @param WP_Term   $parent   Parent term.
			 * @param WP_Term[] $children Child terms.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 * @param array     $context  Render context.
			 */
			$custom_html = apply_filters( 'directorist_custom_category_list_parent_html', '', $parent, $children, $atts, $taxonomy, $context );

			if ( is_string( $custom_html ) && '' !== $custom_html ) {
				return $custom_html;
			}

			$card_classes = $this->get_classes(
				'card',
				array(
					'directorist-custom-category-list__card',
					$has_children ? 'directorist-custom-category-list__card--has-children' : 'directorist-custom-category-list__card--no-children',
					$has_children && $atts['default_open'] ? 'is-open' : '',
					$has_children && ! $atts['default_open'] ? 'is-closed' : '',
				),
				$context
			);

			ob_start();

			/**
			 * Fires before each parent category card.
			 *
			 * @param WP_Term   $parent   Parent term.
			 * @param WP_Term[] $children Child terms.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 */
			do_action( 'directorist_custom_category_list_before_parent', $parent, $children, $atts, $taxonomy );
			?>
			<section class="<?php echo esc_attr( $card_classes ); ?>">
				<?php $this->render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ); ?>

				<?php if ( $has_children ) : ?>
					<?php $this->render_children( $parent, $children, $item_id, $atts, $taxonomy, $context ); ?>
				<?php endif; ?>
			</section>
			<?php
			/**
			 * Fires after each parent category card.
			 *
			 * @param WP_Term   $parent   Parent term.
			 * @param WP_Term[] $children Child terms.
			 * @param array     $atts     Normalized shortcode attributes.
			 * @param string    $taxonomy Taxonomy name.
			 */
			do_action( 'directorist_custom_category_list_after_parent', $parent, $children, $atts, $taxonomy );

			return ob_get_clean();
		}

		/**
		 * Render parent card header.
		 *
		 * @param WP_Term $parent       Parent term.
		 * @param bool    $has_children Whether parent has children.
		 * @param string  $item_id      Children wrapper ID.
		 * @param array   $atts         Normalized shortcode attributes.
		 * @param string  $taxonomy     Taxonomy name.
		 * @param array   $context      Render context.
		 * @return void
		 */
		private function render_header( $parent, $has_children, $item_id, $atts, $taxonomy, $context ) {
			$header_classes = $this->get_classes( 'header', array( 'directorist-custom-category-list__header' ), $context );
			$link_classes   = $this->get_classes( 'parent_link', array( 'directorist-custom-category-list__parent-link' ), $context );
			$toggle_classes = $this->get_classes( 'toggle', array( 'directorist-custom-category-list__toggle' ), $context );
			$icon_classes   = $this->get_classes( 'icon', array( 'directorist-custom-category-list__icon' ), $context );
			?>
			<div class="<?php echo esc_attr( $header_classes ); ?>">
				<a class="<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( $this->get_category_link( $parent, $atts, $taxonomy ) ); ?>">
					<?php echo esc_html( $this->get_category_name( $parent, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $parent, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>

				<?php if ( $has_children ) : ?>
					<button
						type="button"
						class="<?php echo esc_attr( $toggle_classes ); ?>"
						aria-expanded="<?php echo esc_attr( $atts['default_open'] ? 'true' : 'false' ); ?>"
						aria-controls="<?php echo esc_attr( $item_id ); ?>"
						aria-label="<?php echo esc_attr( $this->get_toggle_label( $parent, $atts, $taxonomy ) ); ?>"
					>
						<span class="<?php echo esc_attr( $icon_classes ); ?>" aria-hidden="true"></span>
					</button>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Render child category list.
		 *
		 * @param WP_Term   $parent   Parent term.
		 * @param WP_Term[] $children Child terms.
		 * @param string    $item_id  Children wrapper ID.
		 * @param array     $atts     Normalized shortcode attributes.
		 * @param string    $taxonomy Taxonomy name.
		 * @param array     $context  Render context.
		 * @return void
		 */
		private function render_children( $parent, $children, $item_id, $atts, $taxonomy, $context ) {
			$children_classes = $this->get_classes( 'children', array( 'directorist-custom-category-list__children' ), $context );
			$list_classes     = $this->get_classes( 'children_list', array( 'directorist-custom-category-list__children-list' ), $context );
			?>
			<div id="<?php echo esc_attr( $item_id ); ?>" class="<?php echo esc_attr( $children_classes ); ?>"<?php echo $atts['default_open'] ? '' : ' hidden'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
				/**
				 * Fires before the child category list.
				 *
				 * @param WP_Term   $parent   Parent term.
				 * @param WP_Term[] $children Child terms.
				 * @param array     $atts     Normalized shortcode attributes.
				 * @param string    $taxonomy Taxonomy name.
				 */
				do_action( 'directorist_custom_category_list_before_children', $parent, $children, $atts, $taxonomy );
				?>
				<ul class="<?php echo esc_attr( $list_classes ); ?>">
					<?php foreach ( $children as $child ) : ?>
						<?php $this->render_child_item( $child, $parent, $atts, $taxonomy, $context ); ?>
					<?php endforeach; ?>
				</ul>
				<?php
				/**
				 * Fires after the child category list.
				 *
				 * @param WP_Term   $parent   Parent term.
				 * @param WP_Term[] $children Child terms.
				 * @param array     $atts     Normalized shortcode attributes.
				 * @param string    $taxonomy Taxonomy name.
				 */
				do_action( 'directorist_custom_category_list_after_children', $parent, $children, $atts, $taxonomy );
				?>
			</div>
			<?php
		}

		/**
		 * Render a child category item.
		 *
		 * @param WP_Term $child    Child term.
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @param array   $context  Parent render context.
		 * @return void
		 */
		private function render_child_item( $child, $parent, $atts, $taxonomy, $context ) {
			$child_context           = $context;
			$child_context['child']  = $child;
			$child_context['parent'] = $parent;
			$item_classes            = $this->get_classes( 'child_item', array( 'directorist-custom-category-list__child' ), $child_context );
			$link_classes            = $this->get_classes( 'child_link', array( 'directorist-custom-category-list__child-link' ), $child_context );
			?>
			<li class="<?php echo esc_attr( $item_classes ); ?>">
				<a class="<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( $this->get_category_link( $child, $atts, $taxonomy ) ); ?>">
					<?php echo esc_html( $this->get_category_name( $child, $atts, $taxonomy ) ); ?><?php echo $this->get_count_html( $child, $atts, $taxonomy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
			<?php
		}

		/**
		 * Get category display name.
		 *
		 * @param WP_Term $term     Category term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_category_name( $term, $atts, $taxonomy ) {
			$name = $term->name;

			/**
			 * Filter a category display name.
			 *
			 * @param string  $name     Category name.
			 * @param WP_Term $term     Category term.
			 * @param array   $atts     Normalized shortcode attributes.
			 * @param string  $taxonomy Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_category_name', $name, $term, $atts, $taxonomy );
		}

		/**
		 * Get optional category count HTML.
		 *
		 * @param WP_Term $term     Category term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_count_html( $term, $atts, $taxonomy ) {
			if ( ! $atts['display_count'] ) {
				return '';
			}

			$count        = $this->get_category_count( $term, $atts, $taxonomy );
			$count_class  = $this->get_classes(
				'count',
				array( 'directorist-custom-category-list__count' ),
				array(
					'term'     => $term,
					'atts'     => $atts,
					'taxonomy' => $taxonomy,
				)
			);
			$count_markup = sprintf(
				' <span class="%s">(%s)</span>',
				esc_attr( $count_class ),
				esc_html( number_format_i18n( $count ) )
			);

			/**
			 * Filter category count HTML.
			 *
			 * @param string  $count_markup Count HTML.
			 * @param int     $count        Listing count.
			 * @param WP_Term $term         Category term.
			 * @param array   $atts         Normalized shortcode attributes.
			 * @param string  $taxonomy     Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_count_html', $count_markup, $count, $term, $atts, $taxonomy );
		}

		/**
		 * Get category count.
		 *
		 * @param WP_Term $term     Category term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return int
		 */
		private function get_category_count( $term, $atts, $taxonomy ) {
			if ( isset( $this->counts[ $term->term_id ] ) ) {
				$count = $this->counts[ $term->term_id ];
			} elseif ( function_exists( 'atbdp_listings_count_by_category' ) ) {
				$count = (int) atbdp_listings_count_by_category( $term->term_id );
			} else {
				$count = (int) $term->count;
			}

			$this->counts[ $term->term_id ] = $count;

			/**
			 * Filter category listing count.
			 *
			 * @param int     $count    Listing count.
			 * @param WP_Term $term     Category term.
			 * @param array   $atts     Normalized shortcode attributes.
			 * @param string  $taxonomy Taxonomy name.
			 */
			return (int) apply_filters( 'directorist_custom_category_list_category_count', $count, $term, $atts, $taxonomy );
		}

		/**
		 * Get category URL.
		 *
		 * @param WP_Term $term     Category term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_category_link( $term, $atts, $taxonomy ) {
			if ( class_exists( 'ATBDP_Permalink' ) && method_exists( 'ATBDP_Permalink', 'atbdp_get_category_page' ) ) {
				$link = ATBDP_Permalink::atbdp_get_category_page( $term );
			} else {
				$link = get_term_link( $term );
				$link = is_wp_error( $link ) ? '#' : $link;
			}

			/**
			 * Filter category link URL.
			 *
			 * @param string  $link     Category URL.
			 * @param WP_Term $term     Category term.
			 * @param array   $atts     Normalized shortcode attributes.
			 * @param string  $taxonomy Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_category_link', $link, $term, $atts, $taxonomy );
		}

		/**
		 * Get toggle aria-label.
		 *
		 * @param WP_Term $parent   Parent term.
		 * @param array   $atts     Normalized shortcode attributes.
		 * @param string  $taxonomy Taxonomy name.
		 * @return string
		 */
		private function get_toggle_label( $parent, $atts, $taxonomy ) {
			$label = sprintf(
				/* translators: %s: Category name. */
				__( 'Toggle %s child categories', 'directorist-custom-code' ),
				$parent->name
			);

			/**
			 * Filter the collapse button aria-label.
			 *
			 * @param string  $label    Toggle aria-label.
			 * @param WP_Term $parent   Parent term.
			 * @param array   $atts     Normalized shortcode attributes.
			 * @param string  $taxonomy Taxonomy name.
			 */
			return apply_filters( 'directorist_custom_category_list_toggle_label', $label, $parent, $atts, $taxonomy );
		}

		/**
		 * Get filtered classes for an element.
		 *
		 * @param string $element Element key.
		 * @param array  $classes Default classes.
		 * @param array  $context Context for the element.
		 * @return string
		 */
		private function get_classes( $element, $classes, $context ) {
			/**
			 * Filter CSS classes for any shortcode element.
			 *
			 * @param array|string $classes Classes for the element.
			 * @param string       $element Element key.
			 * @param array        $context Render context.
			 */
			$classes = apply_filters( 'directorist_custom_category_list_classes', $classes, $element, $context );

			return implode( ' ', $this->sanitize_classes( $classes ) );
		}

		/**
		 * Sanitize class names.
		 *
		 * @param array|string $classes Class names.
		 * @return array
		 */
		private function sanitize_classes( $classes ) {
			if ( is_string( $classes ) ) {
				$classes = preg_split( '/\s+/', $classes );
			}

			if ( ! is_array( $classes ) ) {
				return array();
			}

			$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

			return array_values( array_unique( $classes ) );
		}

		/**
		 * Resolve parent category IDs from ID/slug shortcode filters.
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
		 * Convert a shortcode boolean-like value to bool.
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
			$items = array_filter( $items );

			return array_values( array_unique( $items ) );
		}
	}
}
