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
	 * Register and configure the Directorist location list shortcode.
	 */
	final class Directorist_Custom_Code_Location_List_Shortcode extends Directorist_Custom_Code_Taxonomy_List_Shortcode {
		/**
		 * Shortcode tag.
		 *
		 * @var string
		 */
		const SHORTCODE = 'directorist_location_list';

		/**
		 * Singleton instance.
		 *
		 * @var Directorist_Custom_Code_Location_List_Shortcode|null
		 */
		private static $instance = null;

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
			return 'directorist_custom_location_list';
		}

		/**
		 * Get CSS class base.
		 *
		 * @return string
		 */
		protected function get_css_base() {
			return 'directorist-custom-location-list';
		}

		/**
		 * Get Directorist location taxonomy.
		 *
		 * @return string
		 */
		protected function get_default_taxonomy() {
			return defined( 'ATBDP_LOCATION' ) ? ATBDP_LOCATION : 'at_biz_dir-location';
		}

		/**
		 * Get parent number aliases.
		 *
		 * @return array
		 */
		protected function get_number_aliases() {
			return array( 'location_num', 'location_number', 'number_of_location' );
		}

		/**
		 * Get term type key used by filters.
		 *
		 * @return string
		 */
		protected function get_term_type() {
			return 'location';
		}

		/**
		 * Get plural term label.
		 *
		 * @return string
		 */
		protected function get_term_label_plural() {
			return __( 'locations', 'directorist-custom-code' );
		}

		/**
		 * Get Directorist location listing count.
		 *
		 * @param WP_Term $term Term object.
		 * @return int
		 */
		protected function get_directorist_count( $term ) {
			if ( function_exists( 'atbdp_listings_count_by_location' ) ) {
				return (int) atbdp_listings_count_by_location( $term->term_id );
			}

			return (int) $term->count;
		}

		/**
		 * Get Directorist location URL.
		 *
		 * @param WP_Term $term Term object.
		 * @return string
		 */
		protected function get_directorist_link( $term ) {
			if ( class_exists( 'ATBDP_Permalink' ) && method_exists( 'ATBDP_Permalink', 'atbdp_get_location_page' ) ) {
				return ATBDP_Permalink::atbdp_get_location_page( $term );
			}

			return '';
		}
	}
}
