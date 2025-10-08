<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.0.6
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$phone_args = [
    'number'    => $value,
    'whatsapp'  => $listing->has_whatsapp( $data ),
];

?>
<div class="directorist-single-info directorist-single-info-phone">
    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon( $icon );?></span>
        <span class="directorist-single-info__label__text"><?php echo esc_html( $data['label'] ); ?></span>
    </div>

    <div class="directorist-single-info__value">
        <a class="directorist-contact-popup" data-title="<?php echo esc_html( $data['label'] ); ?>" data-value="<?php echo esc_attr( $value ); ?>">View Phone Number</a>
    </div>
</div>