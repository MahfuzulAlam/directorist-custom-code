<?php
/**
 * Directorist taxonomy term field shortcode.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code_Term_Field_Shortcode' ) ) {
	/**
	 * Renders a Directorist taxonomy term's title, description, or image.
	 */
	final class Directorist_Custom_Code_Term_Field_Shortcode {
		/**
		 * Shortcode tag.
		 */
		const SHORTCODE = 'directorist_term_field';

		/**
		 * Register the shortcode.
		 *
		 * @return void
		 */
		public static function register() {
			add_shortcode( self::SHORTCODE, array( __CLASS__, 'render' ) );
		}

		/**
		 * Render a Directorist taxonomy term field.
		 *
		 * When slug and taxonomy are omitted, the current Directorist category,
		 * location, or tag archive supplies them.
		 *
		 * @param array $atts Shortcode attributes.
		 * @return string
		 */
		public static function render( $atts ) {
			$atts = shortcode_atts(
				array(
					'slug'     => '',
					'taxonomy' => '',
					'field'    => 'description',
					'width'    => '',
				),
				is_array( $atts ) ? $atts : array(),
				self::SHORTCODE
			);

			$field = sanitize_key( (string) $atts['field'] );
			if ( ! in_array( $field, array( 'description', 'title', 'image' ), true ) ) {
				return '';
			}

			$term = self::resolve_term( $atts );
			if ( ! $term || is_wp_error( $term ) ) {
				return '';
			}

			$output = '';

			switch ( $field ) {
				case 'title':
					$title  = get_term_field( 'name', $term, $term->taxonomy, 'display' );
					$output = is_wp_error( $title ) ? '' : esc_html( $title );
					break;

				case 'image':
					$output = self::render_image( $term );
					break;

				case 'description':
				default:
					$description = get_term_field( 'description', $term, $term->taxonomy, 'display' );
					$output      = is_wp_error( $description ) ? '' : wp_kses_post( $description );
					break;
			}

			return self::apply_content_width( $output, $field, $atts['width'] );
		}

		/**
		 * Resolve the requested term from attributes or the current archive.
		 *
		 * @param array $atts Normalized shortcode attributes.
		 * @return WP_Term|false
		 */
		private static function resolve_term( $atts ) {
			$taxonomy_name = '';
			$taxonomy_slug = '';

			if ( ! empty( $atts['slug'] ) && ! empty( $atts['taxonomy'] ) ) {
				$taxonomy_name = self::normalize_taxonomy( $atts['taxonomy'] );
				$taxonomy_slug = self::sanitize_slug( $atts['slug'] );
			} else {
				$category_slug = self::sanitize_slug( get_query_var( 'atbdp_category' ) );
				if ( $category_slug ) {
					$taxonomy_name = ATBDP_CATEGORY;
					$taxonomy_slug = $category_slug;
				}

				$location_slug = self::sanitize_slug( get_query_var( 'atbdp_location' ) );
				if ( $location_slug ) {
					$taxonomy_name = ATBDP_LOCATION;
					$taxonomy_slug = $location_slug;
				}

				$tag_slug = self::sanitize_slug( get_query_var( 'atbdp_tag' ) );
				if ( $tag_slug ) {
					$taxonomy_name = ATBDP_TAGS;
					$taxonomy_slug = $tag_slug;
				}
			}

			if ( '' === $taxonomy_name || '' === $taxonomy_slug ) {
				return false;
			}

			return get_term_by( 'slug', $taxonomy_slug, $taxonomy_name );
		}

		/**
		 * Render the Directorist term image stored in the "image" term meta.
		 *
		 * @param WP_Term $term Directorist taxonomy term.
		 * @return string
		 */
		private static function render_image( $term ) {
			$image_id = absint( get_term_meta( $term->term_id, 'image', true ) );
			if ( ! $image_id ) {
				return '';
			}

			$alt   = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
			$alt   = $alt ? $alt : $term->name;
			$size  = apply_filters( 'directorist_custom_code_term_field_image_size', 'full', $term );
			$image = wp_get_attachment_image(
				$image_id,
				$size,
				false,
				array(
					'alt'   => $alt,
					'class' => 'directorist-term-field__image',
				)
			);

			return $image ? wp_kses_post( $image ) : '';
		}

		/**
		 * Wrap shortcode output in a safely sized container.
		 *
		 * @param string $output Rendered field output.
		 * @param string $field  Rendered field name.
		 * @param mixed  $width  Requested content width.
		 * @return string
		 */
		private static function apply_content_width( $output, $field, $width ) {
			if ( '' === $output ) {
				return '';
			}

			$width = self::normalize_width( $width );
			if ( '' === $width ) {
				return $output;
			}

			$style = safecss_filter_attr( 'width: ' . $width . '; max-width: 100%;' );
			if ( '' === $style ) {
				return $output;
			}

			return sprintf(
				'<div class="%1$s" style="%2$s">%3$s</div>',
				esc_attr( 'directorist-term-field directorist-term-field--' . $field . ' directorist-term-field--sized' ),
				esc_attr( $style ),
				$output
			);
		}

		/**
		 * Normalize a shortcode width to a restricted CSS length.
		 *
		 * Unitless values are treated as pixels. Supported units are px, percent,
		 * em, rem, vw, vh, vmin, vmax, and ch.
		 *
		 * @param mixed $width Requested content width.
		 * @return string
		 */
		private static function normalize_width( $width ) {
			if ( ! is_scalar( $width ) ) {
				return '';
			}

			$width = strtolower( trim( (string) $width ) );
			if ( '' === $width || 'auto' === $width ) {
				return $width;
			}

			$number_pattern = '(?:\d+(?:\.\d+)?|\.\d+)';
			if ( preg_match( '/^' . $number_pattern . '$/D', $width ) ) {
				return $width . 'px';
			}

			if ( preg_match( '/^(?:0|' . $number_pattern . '(?:px|%|em|rem|vw|vh|vmin|vmax|ch))$/D', $width ) ) {
				return $width;
			}

			return '';
		}

		/**
		 * Convert a shortcode taxonomy alias into a Directorist taxonomy slug.
		 *
		 * @param string $taxonomy Taxonomy alias or slug.
		 * @return string
		 */
		private static function normalize_taxonomy( $taxonomy ) {
			$taxonomy = sanitize_key( $taxonomy );

			switch ( $taxonomy ) {
				case 'location':
					return ATBDP_LOCATION;

				case 'category':
					return ATBDP_CATEGORY;

				case 'tag':
					return ATBDP_TAGS;
			}

			return '';
		}

		/**
		 * Sanitize a term slug, including URL-encoded archive query vars.
		 *
		 * @param mixed $slug Raw term slug.
		 * @return string
		 */
		private static function sanitize_slug( $slug ) {
			if ( ! is_scalar( $slug ) ) {
				return '';
			}

			return sanitize_title( rawurldecode( trim( (string) $slug ) ) );
		}

	}
}
