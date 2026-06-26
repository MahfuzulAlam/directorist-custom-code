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
	 * Register and configure the Directorist category list shortcode.
	 */
	final class Directorist_Custom_Code_Category_List_Shortcode extends Directorist_Custom_Code_Taxonomy_List_Shortcode {
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
			parent::__construct();
		}

		/**
		 * Get shortcode tag.
		 *
		 * @return string
		 */
		protected function get_shortcode() {
			return self::SHORTCODE;
		}

		/**
		 * Get hook prefix.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'directorist_custom_category_list';
		}

		/**
		 * Get CSS class base.
		 *
		 * @return string
		 */
		protected function get_css_base() {
			return 'directorist-custom-category-list';
		}

		/**
		 * Get Directorist category taxonomy.
		 *
		 * @return string
		 */
		protected function get_default_taxonomy() {
			return defined( 'ATBDP_CATEGORY' ) ? ATBDP_CATEGORY : 'at_biz_dir-category';
		}

		/**
		 * Get parent number aliases.
		 *
		 * @return array
		 */
		protected function get_number_aliases() {
			return array( 'category_num', 'category_number', 'number_of_category' );
		}

		/**
		 * Get term type key used by filters.
		 *
		 * @return string
		 */
		protected function get_term_type() {
			return 'category';
		}

		/**
		 * Get plural term label.
		 *
		 * @return string
		 */
		protected function get_term_label_plural() {
			return __( 'categories', 'directorist-custom-code' );
		}

		/**
		 * Get Directorist category listing count.
		 *
		 * @param WP_Term $term Term object.
		 * @return int
		 */
		protected function get_directorist_count( $term ) {
			if ( function_exists( 'atbdp_listings_count_by_category' ) ) {
				return (int) atbdp_listings_count_by_category( $term->term_id );
			}

			return (int) $term->count;
		}

		/**
		 * Get Directorist category URL.
		 *
		 * @param WP_Term $term Term object.
		 * @return string
		 */
		protected function get_directorist_link( $term ) {
			if ( class_exists( 'ATBDP_Permalink' ) && method_exists( 'ATBDP_Permalink', 'atbdp_get_category_page' ) ) {
				return ATBDP_Permalink::atbdp_get_category_page( $term );
			}

			return '';
		}
	}
}
