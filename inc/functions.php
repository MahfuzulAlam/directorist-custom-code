<?php
/**
 * Custom extension hooks and helpers.
 *
 * Add project-specific PHP code here.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'directorist_custom_code_sort_field_options' ) ) {
	/**
	 * Sort Directorist custom-field options alphabetically by their display label.
	 *
	 * The original array keys are intentionally discarded because Directorist
	 * identifies each option by its option_value property. The original order is
	 * retained when two labels compare equally.
	 *
	 * @param array $options Directorist field options.
	 * @return array
	 */
	function directorist_custom_code_sort_field_options( $options ) {
		if ( ! is_array( $options ) || count( $options ) < 2 ) {
			return is_array( $options ) ? array_values( $options ) : array();
		}

		$sortable_options = array();

		foreach ( array_values( $options ) as $index => $option ) {
			$label = isset( $option['option_label'] ) ? (string) $option['option_label'] : '';

			$sortable_options[] = array(
				'index'  => $index,
				'label'  => remove_accents( wp_strip_all_tags( $label ) ),
				'option' => $option,
			);
		}

		usort(
			$sortable_options,
			static function ( $first, $second ) {
				$comparison = strnatcasecmp( $first['label'], $second['label'] );

				if ( 0 !== $comparison ) {
					return $comparison;
				}

				return $first['index'] - $second['index'];
			}
		);

		$sorted_options = array();

		foreach ( $sortable_options as $sortable_option ) {
			$sorted_options[] = $sortable_option['option'];
		}

		return $sorted_options;
	}
}
