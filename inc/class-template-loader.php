<?php
/**
 * Template resolution for Directorist – Custom Code.
 *
 * Cascade (first match wins):
 * 1. This plugin: templates/{template_name}.php
 * 2. Child theme:  {stylesheet}/directorist/{template_name}.php
 * 3. Parent theme: {template}/directorist/{template_name}.php
 * 4. Directorist:  plugin templates/{template_name}.php
 *
 * Steps 2–4 are handled by Directorist\Helper::template_path(); this class only
 * prepends step 1 via the directorist_template_file_path filter.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers template path filtering.
 */
final class Directorist_Custom_Code_Template_Loader {

	public const FILTER_PRIORITY = 5;

	/**
	 * Hook into Directorist.
	 */
	public static function register() {
		add_filter( 'directorist_template_file_path', array( __CLASS__, 'filter_file_path' ), self::FILTER_PRIORITY, 3 );
	}

	/**
	 * Absolute path to an override file in this plugin.
	 *
	 * @param string $template_name Slug, e.g. listing-form/fields/map.
	 */
	public static function extension_path( $template_name ) {
		$template_name = self::sanitize_template_name( $template_name );
		if ( $template_name === '' ) {
			return '';
		}

		return DIRECTORIST_CUSTOM_CODE_DIR . 'templates/' . $template_name . '.php';
	}

	/**
	 * Whether this plugin provides a readable template for the slug.
	 *
	 * @param string $template_name Slug, e.g. dashboard/tab-profile.
	 */
	public static function extension_has( $template_name ) {
		$path = self::extension_path( $template_name );

		return $path !== '' && is_readable( $path );
	}

	/**
	 * Prefer plugin template when present; otherwise keep Directorist resolution.
	 *
	 * @param string $template      Path already resolved (child → parent → Directorist).
	 * @param string $template_name Relative template slug.
	 * @param array  $args          Template arguments.
	 */
	public static function filter_file_path( $template, $template_name, $args ) {
		unset( $args ); // Reserved for future use / third-party filters.

		$template_name = self::sanitize_template_name( $template_name );
		if ( $template_name === '' ) {
			return $template;
		}

		$extension = self::extension_path( $template_name );
		if ( $extension !== '' && is_readable( $extension ) ) {
			return wp_normalize_path( $extension );
		}

		return $template;
	}

	/**
	 * @param mixed $template_name Value from Directorist (expected string slug).
	 */
	private static function sanitize_template_name( $template_name ) {
		if ( ! is_string( $template_name ) || $template_name === '' ) {
			return '';
		}
		if ( false !== strpos( $template_name, '..' ) ) {
			return '';
		}

		return trim( str_replace( '\\', '/', $template_name ), '/' );
	}
}
