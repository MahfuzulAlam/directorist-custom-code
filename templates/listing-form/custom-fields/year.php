<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$start_year = isset($data['start_year']) && !empty($data['start_year']) ? $data['start_year']: 1900;
$end_year = isset($data['end_year']) && !empty($data['end_year']) ? $data['end_year']: 2100;

?>

<div class="directorist-form-group directorist-custom-filed-date directorist-custom-field-select">

	<?php \Directorist\Directorist_Listing_Form::instance()->field_label_template( $data );?>

    <select
    attr-placeholder="- Year -" 
    name="<?php echo esc_attr( $data['field_key'] ); ?>" 
    id="<?php echo esc_attr( $data['field_key'] ); ?>" 
    class="directorist-form-element directorist-custom-field-year" 
    <?php \Directorist\Directorist_Listing_Form::instance()->required( $data ); ?>>

        <option value=""><?php echo $data['placeholder'] ? $data['placeholder']:  'Select year'?></option>

        <?php
        for ($year = $start_year; $year <= $end_year; $year++) {
            $selected = $value == $year ? 'selected' : '';
            echo '<option value="' . $year . '" '.$selected.'>' . $year . '</option>';
        }
        ?>

    </select>

	<?php \Directorist\Directorist_Listing_Form::instance()->field_description_template( $data );?>

</div>