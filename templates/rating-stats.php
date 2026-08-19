<?php

/**
 * Google Rating & Total Reviews — compact stats field.
 *
 * Reads the values stored on the listing; it never calls the API itself.
 *
 * @author  wpXplore
 * @since   3.0
 * @version 3.0
 *
 * @var array $data  Field data from the single listing layout builder.
 * @var array $place Stored listing data.
 */

if (! defined('ABSPATH')) exit;

$place = (isset($place) && is_array($place)) ? $place : array();

$rating = isset($place['rating']) ? (float) $place['rating'] : 0;
$total  = isset($place['user_ratings_total']) ? (int) $place['user_ratings_total'] : 0;

if (! $rating && ! $total) {
    return;
}
?>
<div class="dgr-reviews dgr-stats">
    <div class="dgr-stats__item">
        <span class="dgr-stats__value"><?php echo esc_html(number_format_i18n($rating, 1)); ?></span>
        <?php echo dgr_rating_stars($rating, array('size' => 14)); ?>
        <span class="dgr-stats__label"><?php esc_html_e('Average rating', 'directorist-google-reviews'); ?></span>
    </div>

    <div class="dgr-stats__item">
        <span class="dgr-stats__value"><?php echo esc_html(number_format_i18n($total)); ?></span>
        <span class="dgr-stats__label"><?php esc_html_e('Total Google reviews', 'directorist-google-reviews'); ?></span>
    </div>
</div>
