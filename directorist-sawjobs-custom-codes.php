<?php
/**
 * Plugin Name:       Directorist - Custom Codes for Sportsandwellnessjobs
 * Plugin URI:        https://wpxplore.com
 * Description:       Sawjobs-specific custom features and integrations for Directorist.
 * Version:           3.0.0
 * Requires at least: 5.2
 * Author:            wpXplore
 * Author URI:        https://wpxplore.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-sawjobs-custom-codes
 * Domain Path:       /languages
 *
 * @package Directorist_Sawjobs_Custom_Codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Directorist_Sawjobs_Custom_Codes' ) ) {
	/**
	 * Main plugin bootstrap.
	 */
	final class Directorist_Sawjobs_Custom_Codes {
		/**
		 * Singleton instance.
		 *
		 * @var Directorist_Sawjobs_Custom_Codes|null
		 */
		private static $instance = null;

		/**
		 * Get the singleton instance.
		 *
		 * @return Directorist_Sawjobs_Custom_Codes
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
			if ( ! defined( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI' ) ) {
				define( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR' ) ) {
				define( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION' ) ) {
				define( 'DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION', '3.0.0' );
			}
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		private function includes() {
			require_once DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR . 'inc/class-sawjobs-template-loader.php';
			require_once DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR . 'inc/class-sawjobs-webp-converter.php';
			require_once DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR . 'inc/class-sawjobs-user-gallery.php';
			require_once DIRECTORIST_SAWJOBS_CUSTOM_CODES_DIR . 'inc/sawjobs-functions.php';
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
			Directorist_Sawjobs_Custom_Codes_Template_Loader::register();
			Directorist_Sawjobs_Custom_Codes_User_Gallery::register();
		}

		/**
		 * Enqueue frontend scripts.
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				'directorist-sawjobs-custom-codes-script',
				DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI . 'assets/js/sawjobs-main.js',
				array( 'jquery' ),
				DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION,
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
				'directorist-sawjobs-custom-codes-style',
				DIRECTORIST_SAWJOBS_CUSTOM_CODES_URI . 'assets/css/sawjobs-main.css',
				array(),
				DIRECTORIST_SAWJOBS_CUSTOM_CODES_VERSION
			);
		}
	}
}

if ( ! function_exists( 'directorist_sawjobs_custom_codes_is_plugin_active' ) ) {
	/**
	 * Check whether a plugin is active on the current site or network.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_sawjobs_custom_codes_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || directorist_sawjobs_custom_codes_is_plugin_active_for_network( $plugin );
	}
}

if ( ! function_exists( 'directorist_sawjobs_custom_codes_is_plugin_active_for_network' ) ) {
	/**
	 * Check whether a plugin is network-activated.
	 *
	 * @param string $plugin Plugin path relative to the plugins directory.
	 * @return bool
	 */
	function directorist_sawjobs_custom_codes_is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = (array) get_site_option( 'active_sitewide_plugins', array() );

		return isset( $plugins[ $plugin ] );
	}
}

if ( ! function_exists( 'directorist_sawjobs_custom_codes' ) ) {
	/**
	 * Start the plugin.
	 *
	 * @return Directorist_Sawjobs_Custom_Codes
	 */
	function directorist_sawjobs_custom_codes() {
		return Directorist_Sawjobs_Custom_Codes::instance();
	}
}

if ( directorist_sawjobs_custom_codes_is_plugin_active( 'directorist/directorist-base.php' ) ) {
	directorist_sawjobs_custom_codes();
}
