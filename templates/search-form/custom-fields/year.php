<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.3
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$value = '';
$start_year = isset($data['start_year']) && !empty($data['start_year']) ? $data['start_year']: 1900;
$end_year = isset($data['end_year']) && !empty($data['end_year']) ? $data['end_year']: 2100;
?>

<div class="directorist-search-field">

	<?php if ( !empty($data['label']) ): ?>
		<label><?php echo esc_html( $data['label'] ); ?></label>
	<?php endif; ?>

	<div class="directorist-form-group directorist-search-year">
<!--         
		<input class="directorist-form-element" type="date" name="year_from<?php echo esc_attr( $data['field_key'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?>>
		<input class="directorist-form-element" type="date" name="year_to<?php echo esc_attr( $data['field_key'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?>> -->

        <div class="directorist-search-year-container directorist-select">
            <select
                name="year_from_<?php echo esc_attr( $data['field_key'] ); ?>" 
                id="year_from_<?php echo esc_attr( $data['field_key'] ); ?>" 
                class="directorist-form-element directorist-custom-field-year" >

                <option value="">Year From</option>

                <?php
                for ($year = $start_year; $year <= $end_year; $year++) {
                    $selected = $value == $year ? 'selected' : '';
                    echo '<option value="' . $year . '" '.$selected.'>' . $year . '</option>';
                }
                ?>

            </select>
        </div>
        <div class="directorist-search-year-container directorist-select">
            <select
                name="year_to_<?php echo esc_attr( $data['field_key'] ); ?>" 
                id="year_to_<?php echo esc_attr( $data['field_key'] ); ?>" 
                class="directorist-form-element directorist-custom-field-year" >

                <option value="">Year To</option>

                <?php
                for ($year = $start_year; $year <= $end_year; $year++) {
                    $selected = $value == $year ? 'selected' : '';
                    echo '<option value="' . $year . '" '.$selected.'>' . $year . '</option>';
                }
                ?>

            </select>
        </div>
	</div>

</div>