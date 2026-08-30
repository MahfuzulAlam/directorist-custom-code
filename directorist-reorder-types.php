<?php
/**
 * Plugin Name:       Directorist - Reorder Types
 * Plugin URI:        https://wpxplore.com
 * Description:       Reorder Directorist directory types with a drag-and-drop admin interface.
 * Version:           3.0.0
 * Requires at least: 5.2
 * Author:            wpXplore
 * Author URI:        https://wpxplore.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-reorder-types
 * Domain Path:       /languages
 *
 * @package Directorist_Reorder_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Reorder_Types' ) ) {
	/**
	 * Main plugin bootstrap.
	 */
	final class Directorist_Reorder_Types {
		/**
		 * Singleton instance.
		 *
		 * @var Directorist_Reorder_Types|null
		 */
		private static $instance = null;

		/**
		 * Get the singleton instance.
		 *
		 * @return Directorist_Reorder_Types
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) || ! ( self::$instance instanceof self ) ) {
				self::$instance = new self();
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Initialize plugin hooks and dependencies.
		 *
		 * @return void
		 */
		private function init() {
			$this->define_constants();
			$this->includes();
			$this->enqueue_assets();
			$this->register_template_loader();
		}

		/**
		 * Define plugin constants.
		 *
		 * @return void
		 */
		private function define_constants() {
			if ( ! defined( 'DIRECTORIST_REORDER_TYPES_URI' ) ) {
				define( 'DIRECTORIST_REORDER_TYPES_URI', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_REORDER_TYPES_DIR' ) ) {
				define( 'DIRECTORIST_REORDER_TYPES_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_REORDER_TYPES_VERSION' ) ) {
				define( 'DIRECTORIST_REORDER_TYPES_VERSION', '3.0.0' );
			}
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		private function includes() {
			require_once DIRECTORIST_REORDER_TYPES_DIR . 'inc/class-template-loader.php';
			require_once DIRECTORIST_REORDER_TYPES_DIR . 'inc/class-directory-type-order.php';
			require_once DIRECTORIST_REORDER_TYPES_DIR . 'inc/functions.php';

			Directorist_Reorder_Types_Directory_Type_Order::register();
		}

		/**
		 * Register asset hooks.
		 *
		 * @return void
		 */
		private function enqueue_assets() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Register template override loading.
		 *
		 * @return void
		 */
		private function register_template_loader() {
			Directorist_Reorder_Types_Template_Loader::register();
		}

		/**
		 * Enqueue frontend scripts.
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				'directorist-reorder-types',
				DIRECTORIST_REORDER_TYPES_URI . 'assets/js/main.js',
				array( 'jquery' ),
				DIRECTORIST_REORDER_TYPES_VERSION,
				true
			);
		}

		/**
		 * Enqueue frontend styles.
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			wp_enqueue_style(
				'directorist-reorder-types',
				DIRECTORIST_REORDER_TYPES_URI . 'assets/css/main.css',
				array(),
				DIRECTORIST_REORDER_TYPES_VERSION
			);
		}
	}
}

if ( ! function_exists( 'directorist_reorder_types_is_plugin_active' ) ) {
	/**
	 * Check whether a plugin is active on the current site or network.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_reorder_types_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || directorist_reorder_types_is_plugin_active_for_network( $plugin );
	}
}

if ( ! function_exists( 'directorist_reorder_types_is_plugin_active_for_network' ) ) {
	/**
	 * Check whether a plugin is network-activated.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_reorder_types_is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = (array) get_site_option( 'active_sitewide_plugins', array() );

		return isset( $plugins[ $plugin ] );
	}
}

if ( ! function_exists( 'directorist_reorder_types' ) ) {
	/**
	 * Start the plugin.
	 *
	 * @return Directorist_Reorder_Types
	 */
	function directorist_reorder_types() {
		return Directorist_Reorder_Types::instance();
	}
}

if ( directorist_reorder_types_is_plugin_active( 'directorist/directorist-base.php' ) ) {
	directorist_reorder_types();
}
