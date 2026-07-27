<?php
/**
 * Listing form custom select field.
 *
 * Overrides Directorist's template to display options alphabetically.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$directorist_custom_code_conditional_logic_attr = $listing_form->get_conditional_logic_attributes( $data );
$directorist_custom_code_options                = directorist_custom_code_sort_field_options(
	isset( $data['options'] ) ? $data['options'] : array()
);
?>
<div class="directorist-form-group directorist-custom-field-select"<?php echo $directorist_custom_code_conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped internally by Directorist. ?>>
	<?php $listing_form->field_label_template( $data ); ?>

	<?php if ( ! empty( $directorist_custom_code_options ) ) : ?>
		<div class="directorist-select directorist-select-multi" data-is-multi="false">
			<select
				class="directorist-select2"
				name="<?php echo esc_attr( $data['field_key'] ); ?>"
				<?php $listing_form->required( $data ); ?>
			>
				<?php foreach ( $directorist_custom_code_options as $directorist_custom_code_option ) : ?>
					<option
						value="<?php echo esc_attr( $directorist_custom_code_option['option_value'] ); ?>"
						<?php selected( $data['value'], $directorist_custom_code_option['option_value'] ); ?>
					>
						<?php echo esc_html( $directorist_custom_code_option['option_label'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<?php $listing_form->field_description_template( $data ); ?>
</div>
