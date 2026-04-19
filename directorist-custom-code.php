<?php
/**
 * Plugin Name:       Directorist - Custom Code
 * Plugin URI:        https://wpxplore.com
 * Description:       Best way to implement custom code for Directorist plugin.
 * Version:           3.0.0
 * Requires at least: 5.2
 * Author:            wpXplore
 * Author URI:        https://wpxplore.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-custom-code
 * Domain Path:       /languages
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Custom_Code' ) ) {
	/**
	 * Main plugin bootstrap.
	 */
	final class Directorist_Custom_Code {
		/**
		 * Singleton instance.
		 *
		 * @var Directorist_Custom_Code|null
		 */
		private static $instance = null;

		/**
		 * Get the singleton instance.
		 *
		 * @return Directorist_Custom_Code
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
			if ( ! defined( 'DIRECTORIST_CUSTOM_CODE_URI' ) ) {
				define( 'DIRECTORIST_CUSTOM_CODE_URI', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_CUSTOM_CODE_DIR' ) ) {
				define( 'DIRECTORIST_CUSTOM_CODE_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_CUSTOM_CODE_VERSION' ) ) {
				define( 'DIRECTORIST_CUSTOM_CODE_VERSION', '3.0.0' );
			}
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		private function includes() {
			require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/class-template-loader.php';
			require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/functions.php';
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
			Directorist_Custom_Code_Template_Loader::register();
		}

		/**
		 * Enqueue frontend scripts.
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				'directorist-custom-script',
				DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/main.js',
				array( 'jquery' ),
				DIRECTORIST_CUSTOM_CODE_VERSION,
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
				'directorist-custom-style',
				DIRECTORIST_CUSTOM_CODE_URI . 'assets/css/main.css',
				array(),
				DIRECTORIST_CUSTOM_CODE_VERSION
			);
		}
	}
}

if ( ! function_exists( 'directorist_custom_code_is_plugin_active' ) ) {
	/**
	 * Check whether a plugin is active on the current site or network.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_custom_code_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || directorist_custom_code_is_plugin_active_for_network( $plugin );
	}
}

if ( ! function_exists( 'directorist_custom_code_is_plugin_active_for_network' ) ) {
	/**
	 * Check whether a plugin is network-activated.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_custom_code_is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = (array) get_site_option( 'active_sitewide_plugins', array() );

		return isset( $plugins[ $plugin ] );
	}
}

if ( ! function_exists( 'directorist_custom_code' ) ) {
	/**
	 * Start the plugin.
	 *
	 * @return Directorist_Custom_Code
	 */
	function directorist_custom_code() {
		return Directorist_Custom_Code::instance();
	}
}

if ( directorist_custom_code_is_plugin_active( 'directorist/directorist-base.php' ) ) {
	directorist_custom_code();
}
