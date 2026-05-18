<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.6
 */

if ( ! defined( 'ABSPATH' ) ) exit;
// Get conditional logic attributes using centralized method
$conditional_logic_attr = $listing_form->get_conditional_logic_attributes( $data );
$p_id = $listing_form->add_listing_id;
$field_url_label = get_post_meta( $p_id, '_' . $data['field_key'] . '_label' , true );
?>

<div class="directorist-form-group directorist-custom-filed-url"<?php echo $conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in get_conditional_logic_attributes() ?>>

    <?php $listing_form->field_label_template( $data );?>

    <input type="text" name="<?php echo esc_attr( $data['field_key'] ); ?>_label" id="<?php echo esc_attr( $data['field_key'] ); ?>_label" class="directorist-form-element" value="<?php echo esc_attr( $field_url_label ); ?>" placeholder="<?php echo esc_attr( $data['label_placeholder'] ); ?>" style="margin-bottom:10px">

    <input type="url" name="<?php echo esc_attr( $data['field_key'] ); ?>" id="<?php echo esc_attr( $data['field_key'] ); ?>" class="directorist-form-element" value="<?php echo esc_attr( $data['value'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php $listing_form->required( $data ); ?>>

    <?php $listing_form->field_description_template( $data );?>

</div>