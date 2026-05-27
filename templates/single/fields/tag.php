<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( empty( $listing->get_tags() ) ) {
    return;
}
?>

<ul class="directorist-single-tag-list">

    <?php foreach ( $listing->get_tags() as $tag ) : ?>

        <li>
            <span><?php directorist_icon( $icon ); ?> <?php echo esc_html( $tag->name ); ?></span>
        </li>

    <?php endforeach; ?>

</ul>