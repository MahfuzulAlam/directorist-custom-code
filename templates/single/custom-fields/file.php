<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$done = str_replace( '|||', '', $value );
$file_path = wp_parse_url( $done, PHP_URL_PATH );
$filename = rawurldecode( basename( $file_path ? $file_path : $done ) );
$filetype = wp_check_filetype( $filename );
$is_video = ! empty( $filetype['type'] ) && 0 === strpos( $filetype['type'], 'video/' );
?>

<div class="directorist-single-info directorist-single-info-file">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon( $icon );?></span>
        <span class="directorist-single-info__label__text"><?php echo esc_html( $data['label'] ); ?></span>
    </div>

    <div class="directorist-single-info__value">
        <?php
        if ( $is_video ) {
            printf(
                '<video class="directorist-single-info__video" controls preload="metadata" style="max-width:100%%;height:auto;"><source src="%1$s" type="%2$s">%3$s <a href="%1$s" target="_blank" rel="noopener">%4$s</a></video>',
                esc_url( $done ),
                esc_attr( $filetype['type'] ),
                esc_html__( 'Your browser does not support the video tag.', 'directorist' ),
                esc_html( $filename )
            );
        } else {
            printf( '<a href="%s" target="_blank" download>%s</a>', esc_url( $done ), esc_html( $filename ) );
        }
        ?>
    </div>

</div>
