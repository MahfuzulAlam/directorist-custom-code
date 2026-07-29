<?php
/**
 * Listing form custom checkbox field.
 *
 * Overrides Directorist's template to support optional alphabetical sorting.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$directorist_custom_code_conditional_logic_attr = $listing_form->get_conditional_logic_attributes( $data );
$directorist_custom_code_options                = directorist_custom_code_maybe_sort_field_options(
	isset( $data['options'] ) ? $data['options'] : array(),
	$data
);
?>
<div class="directorist-form-group directorist-custom-field-checkbox"<?php echo $directorist_custom_code_conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped internally by Directorist. ?>>
	<?php $listing_form->field_label_template( $data ); ?>

	<?php if ( ! empty( $directorist_custom_code_options ) ) : ?>
		<?php foreach ( $directorist_custom_code_options as $directorist_custom_code_option ) : ?>
			<?php $directorist_custom_code_unique_id = $directorist_custom_code_option['option_value'] . '-' . wp_rand(); ?>
			<div class="directorist-checkbox directorist-mb-10">
				<input
					type="checkbox"
					id="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>"
					name="<?php echo esc_attr( $data['field_key'] ); ?>[]"
					value="<?php echo esc_attr( $directorist_custom_code_option['option_value'] ); ?>"
					<?php checked( in_array( $directorist_custom_code_option['option_value'], (array) $data['value'], true ) ); ?>
				>
				<label for="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>" class="directorist-checkbox__label">
					<?php echo esc_html( $directorist_custom_code_option['option_label'] ); ?>
				</label>
			</div>
		<?php endforeach; ?>

		<a href="#" class="directorist-btn directorist-btn-sm directorist-btn-outline-light directorist-form-check-more">
			<?php esc_html_e( 'See More', 'directorist-custom-code' ); ?>
		</a>
	<?php endif; ?>

	<?php $listing_form->field_description_template( $data ); ?>
</div>
