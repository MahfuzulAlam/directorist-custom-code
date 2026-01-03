<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$data = isset( $data['data'] ) ? $data['data'] : $data;

$value = $listing->get_custom_field_value( 'checkbox', $data );

$options = isset( $data['options'] ) ? $data['options'] : [];

$new_value = isset( $data['value'] ) ? $data['value'] : [];

?>

<div class="directorist-single-info directorist-single-info-checkbox">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon( $icon );?></span>
        <span class="directorist-single-info__label__text"><?php echo esc_html( $data['label'] ); ?></span>
    </div>

    <div class="directorist-single-info__value directorist-single-info__value-checkbox-new">
        <?php foreach ( $new_value as $value ) : ?>
            <span class="directorist-single-info__value-checkbox-new-item"><?php directorist_icon( 'fas fa-check' ); ?>
                <span class="directorist-single-info__value-checkbox-new-item-label">
                    <?php
                        // $options is an array of arrays with keys 'option_value' and 'option_label'
                        $label = $value;
                        if ( is_array( $options ) ) {
                            foreach ( $options as $opt ) {
                                if ( isset( $opt['option_value'] ) && $opt['option_value'] == $value ) {
                                    $label = $opt['option_label'];
                                    break;
                                }
                            }
                        }
                        echo esc_html( $label );
                    ?>
                </span>
            </span>
        </span>
        <?php endforeach; ?>
    </div>

</div>