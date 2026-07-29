<?php
/**
 * Search form custom checkbox field.
 *
 * Overrides Directorist's template to support optional alphabetical sorting.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( '' === $value ) {
	$value = array();
}

$directorist_custom_code_conditional_logic_attr = $searchform->get_conditional_logic_attributes( $data );
$directorist_custom_code_options                = directorist_custom_code_maybe_sort_field_options(
	isset( $data['options']['options'] ) ? $data['options']['options'] : array(),
	$data
);
?>

<div class="directorist-search-field directorist-search-form-dropdown directorist-form-group"<?php echo $directorist_custom_code_conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped internally by Directorist. ?>>
	<div class="directorist-search-basic-dropdown directorist-search-field__input">
		<?php if ( ! empty( $data['label'] ) ) : ?>
			<label class="directorist-search-field__label directorist-search-basic-dropdown-label">
				<span class="directorist-search-basic-dropdown-selected-prefix"></span>
				<?php echo esc_html( $data['label'] ); ?>
				<span class="directorist-search-basic-dropdown-selected-count"></span>
				<?php directorist_icon( 'fas fa-chevron-down' ); ?>
			</label>
		<?php endif; ?>

		<div class="directorist-search-basic-dropdown-content">
			<div class="directorist-flex directorist-flex-wrap directorist-checkbox-wrapper">
				<?php foreach ( $directorist_custom_code_options as $directorist_custom_code_option ) : ?>
					<?php $directorist_custom_code_unique_id = $directorist_custom_code_option['option_value'] . '-' . wp_rand(); ?>
					<div class="directorist-checkbox directorist-checkbox-primary">
						<input
							type="checkbox"
							id="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>"
							name="custom_field[<?php echo esc_attr( $data['field_key'] ); ?>][]"
							value="<?php echo esc_attr( $directorist_custom_code_option['option_value'] ); ?>"
							<?php checked( in_array( $directorist_custom_code_option['option_value'], (array) $value ) ); ?>
						>
						<label class="directorist-checkbox__label" for="<?php echo esc_attr( $directorist_custom_code_unique_id ); ?>">
							<?php echo esc_html( $directorist_custom_code_option['option_label'] ); ?>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="directorist-search-field__btn directorist-search-field__btn--clear">
		<?php directorist_icon( 'fas fa-times-circle' ); ?>
	</div>
</div>
