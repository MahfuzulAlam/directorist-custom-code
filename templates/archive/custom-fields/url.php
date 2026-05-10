<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$link_text = isset( $data['original_field']['link_text'] ) && !empty( $data['original_field']['link_text'] ) ?  $data['original_field']['link_text'] : ''; 
?>

<<?php echo tag_escape( $before ? $before : 'div' ); ?> class="directorist-listing-card-url"><?php directorist_icon( $icon ); ?><?php $listings->print_label( $label ); ?>
    <a target="_blank" rel="noopener" href="<?php echo esc_url( $value ); ?>">
        <?php echo $link_text ? esc_html( $link_text ) : esc_html( $value ); ?>
    </a>
</<?php echo tag_escape( $after ? $after : 'div' ); ?>>