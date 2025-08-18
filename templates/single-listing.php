<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.6
 */


if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="directorist-single-info directorist-single-info-address">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon( $data['icon'] );?></span>
        <span class="directorist-single-info__label__text"><?php echo ( isset( $data['label'] ) ) ? esc_html( $data['label'] ) : ''; ?></span>
    </div>
    <div class="directorist-single-info__value directorist-single-info__addresses">
        <?php foreach( $args['addresses'] as $address ): ?>
        <span><?php echo wp_kses_post( $address['address'] ); ?></span>
        <?php endforeach; ?>
    </div>
</div>