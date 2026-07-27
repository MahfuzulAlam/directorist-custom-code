<?php
/**
 * Listing form custom radio field.
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
<div class="directorist-form-group directorist-custom-field-radio"<?php echo $directorist_custom_code_conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped internally by Directorist. ?>>
	<?php $listing_form->field_label_template( $data ); ?>

	<?php if ( ! empty( $directorist_custom_code_options ) ) : ?>
		<?php foreach ( $directorist_custom_code_options as $directorist_custom_code_option ) : ?>
			<?php $directorist_custom_code_unique_id = $directorist_custom_code_option['option_value'] . '-' . wp_rand(); ?>
			<div class="directorist-radio directorist-mb-10">
				<input
					type="radio"
					id="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>"
					name="<?php echo esc_attr( $data['field_key'] ); ?>"
					value="<?php echo esc_attr( $directorist_custom_code_option['option_value'] ); ?>"
					<?php checked( $directorist_custom_code_option['option_value'], $data['value'] ); ?>
				>
				<label for="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>" class="directorist-radio__label">
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
