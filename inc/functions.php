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

if ( ! function_exists( 'directorist_custom_code_alphabetical_sorting_field' ) ) {
	/**
	 * Get the builder definition for the alphabetical-sorting switch.
	 *
	 * @return array
	 */
	function directorist_custom_code_alphabetical_sorting_field() {
		return array(
			'type'  => 'toggle',
			'label' => __( 'Alphabetical sorting', 'directorist-custom-code' ),
			'value' => false,
		);
	}
}

if ( ! function_exists( 'directorist_custom_code_insert_builder_option' ) ) {
	/**
	 * Insert a field option after another option while preserving display order.
	 *
	 * @param array  $options   Builder options.
	 * @param string $after_key Option key after which the new field is inserted.
	 * @return array
	 */
	function directorist_custom_code_insert_builder_option( $options, $after_key ) {
		if ( ! is_array( $options ) || isset( $options['alphabetical_sorting'] ) ) {
			return $options;
		}

		$updated_options = array();
		$field_inserted  = false;

		foreach ( $options as $option_key => $option ) {
			$updated_options[ $option_key ] = $option;

			if ( $after_key === $option_key ) {
				$updated_options['alphabetical_sorting'] = directorist_custom_code_alphabetical_sorting_field();
				$field_inserted                          = true;
			}
		}

		if ( ! $field_inserted ) {
			$updated_options['alphabetical_sorting'] = directorist_custom_code_alphabetical_sorting_field();
		}

		return $updated_options;
	}
}

if ( ! function_exists( 'directorist_custom_code_add_listing_sorting_options' ) ) {
	/**
	 * Add alphabetical-sorting switches to Add Listing builder custom fields.
	 *
	 * @param array $widgets Directorist custom field widget definitions.
	 * @return array
	 */
	function directorist_custom_code_add_listing_sorting_options( $widgets ) {
		if ( ! is_array( $widgets ) ) {
			return $widgets;
		}

		foreach ( array( 'checkbox', 'radio', 'select' ) as $widget_name ) {
			if ( empty( $widgets[ $widget_name ]['options'] ) || ! is_array( $widgets[ $widget_name ]['options'] ) ) {
				continue;
			}

			$widgets[ $widget_name ]['options'] = directorist_custom_code_insert_builder_option(
				$widgets[ $widget_name ]['options'],
				'options'
			);
		}

		return $widgets;
	}
}
add_filter( 'atbdp_form_custom_widgets', 'directorist_custom_code_add_listing_sorting_options' );

if ( ! function_exists( 'directorist_custom_code_add_search_sorting_options' ) ) {
	/**
	 * Add alphabetical-sorting switches to Search Form builder custom fields.
	 *
	 * @param array $widgets Directorist search field widget definitions.
	 * @return array
	 */
	function directorist_custom_code_add_search_sorting_options( $widgets ) {
		if (
			! is_array( $widgets )
			|| empty( $widgets['available_widgets']['widgets'] )
			|| ! is_array( $widgets['available_widgets']['widgets'] )
		) {
			return $widgets;
		}

		foreach ( array( 'checkbox', 'radio', 'select' ) as $widget_name ) {
			if (
				empty( $widgets['available_widgets']['widgets'][ $widget_name ]['options'] )
				|| ! is_array( $widgets['available_widgets']['widgets'][ $widget_name ]['options'] )
			) {
				continue;
			}

			$after_key = 'select' === $widget_name ? 'placeholder' : 'label';

			$widgets['available_widgets']['widgets'][ $widget_name ]['options'] = directorist_custom_code_insert_builder_option(
				$widgets['available_widgets']['widgets'][ $widget_name ]['options'],
				$after_key
			);
		}

		return $widgets;
	}
}
add_filter( 'directorist_search_form_widgets', 'directorist_custom_code_add_search_sorting_options' );

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

if ( ! function_exists( 'directorist_custom_code_is_alphabetical_sorting_enabled' ) ) {
	/**
	 * Determine whether a field has alphabetical sorting enabled.
	 *
	 * @param array $field_data Directorist field data.
	 * @return bool
	 */
	function directorist_custom_code_is_alphabetical_sorting_enabled( $field_data ) {
		if ( ! is_array( $field_data ) || ! array_key_exists( 'alphabetical_sorting', $field_data ) ) {
			return false;
		}

		return in_array(
			$field_data['alphabetical_sorting'],
			array( true, 1, '1', 'true', 'yes', 'on' ),
			true
		);
	}
}

if ( ! function_exists( 'directorist_custom_code_maybe_sort_field_options' ) ) {
	/**
	 * Sort field options only when enabled in the relevant builder field.
	 *
	 * @param array $options    Directorist field options.
	 * @param array $field_data Directorist field data.
	 * @return array
	 */
	function directorist_custom_code_maybe_sort_field_options( $options, $field_data ) {
		if ( ! is_array( $options ) ) {
			return array();
		}

		if ( ! directorist_custom_code_is_alphabetical_sorting_enabled( $field_data ) ) {
			return $options;
		}

		return directorist_custom_code_sort_field_options( $options );
	}
}
