<?php
/**
 * Search form custom select field.
 *
 * Overrides Directorist's template to support optional alphabetical sorting.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$directorist_custom_code_conditional_logic_attr = $searchform->get_conditional_logic_attributes( $data );
$directorist_custom_code_options                = directorist_custom_code_maybe_sort_field_options(
	isset( $data['options']['options'] ) ? $data['options']['options'] : array(),
	$data
);
$directorist_custom_code_placeholder            = ! empty( $data['placeholder'] )
	? $data['placeholder']
	: __( 'Select', 'directorist' );
?>

<div class="directorist-search-field directorist-form-group"<?php echo $directorist_custom_code_conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped internally by Directorist. ?>>
	<div class="directorist-select directorist-search-field__input">
		<?php if ( ! empty( $data['label'] ) ) : ?>
			<label class="directorist-search-field__label">
				<?php echo esc_html( $data['label'] ); ?>
			</label>
		<?php endif; ?>

		<select
			name="custom_field[<?php echo esc_attr( $data['field_key'] ); ?>]"
			data-isSearch="true"
			data-placeholder="<?php echo esc_attr( $directorist_custom_code_placeholder ); ?>"
			<?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?>
		>
			<option value=""><?php echo esc_html( $directorist_custom_code_placeholder ); ?></option>

			<?php foreach ( $directorist_custom_code_options as $directorist_custom_code_option ) : ?>
				<option
					value="<?php echo esc_attr( $directorist_custom_code_option['option_value'] ); ?>"
					<?php selected( $value, $directorist_custom_code_option['option_value'] ); ?>
				>
					<?php echo esc_html( $directorist_custom_code_option['option_label'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="directorist-search-field__btn directorist-search-field__btn--clear">
		<?php directorist_icon( 'fas fa-times-circle' ); ?>
	</div>
</div>
