<?php

/**
 * @author  wpWax
 * @since   2.0
 * @version 2.0
 */

if (! defined('ABSPATH')) exit;

$field_value = $data['value'] ? json_decode($data['value']) : [];
$place_id = $field_value ? $field_value->place_id : '';
$place_address = $field_value ? $field_value->place_address : '';
?>

<div class="directorist-form-group">

    <?php \Directorist\Directorist_Listing_Form::instance()->field_label_template($data); ?>

    <input type="text" autocomplete="off" class="directorist-form-element" id="google_place_address" name="google_place_address" placeholder="<?php echo esc_attr($data['placeholder']); ?>" value="<?php echo esc_attr($place_address); ?>" <?php \Directorist\Directorist_Listing_Form::instance()->required($data); ?>>

    <input type="hidden" name="<?php echo esc_attr($data['field_key']); ?>" id="<?php echo esc_attr($data['field_key']); ?>" value="<?php echo esc_attr($data['value']); ?>" />

</div>