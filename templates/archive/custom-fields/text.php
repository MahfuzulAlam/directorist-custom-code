<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$website = '';
if( isset( $data['data']['original_field']['field_key'] ) && ! empty( $data['data']['original_field']['field_key'] )){
    if( $data['data']['original_field']['field_key'] == 'company_name' ){
        $website = get_post_meta( get_the_ID(), '_website', true );
    }
}
?>

<li class="directorist-listing-card-text">
    <?php directorist_icon( $icon ); ?>
    <?php $listings->print_label( $label ); ?>
    <?php
    if( ! empty( $website ) ):
    ?>
        <a href="<?php echo esc_url( $website );?>" target="_blank" rel="noopener"><?php echo esc_html( $value );?></a>
    <?php
    else:
        echo esc_html( $value );
    endif;
    ?>
</li>