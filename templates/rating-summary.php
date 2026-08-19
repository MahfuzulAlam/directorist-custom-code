<?php

/**
 * Google Rating — standalone summary bar.
 *
 * @author  wpXplore
 * @since   2.2
 * @version 2.2
 *
 * @var array $data  Field data from the single listing layout builder.
 * @var array $place Normalised Places payload.
 */

if (! defined('ABSPATH')) exit;

$place = (isset($place) && is_array($place)) ? $place : array();

$rating = isset($place['rating']) ? (float) $place['rating'] : 0;
$total  = isset($place['user_ratings_total']) ? (int) $place['user_ratings_total'] : 0;
$url    = ! empty($place['url']) ? $place['url'] : '';

if (! $rating) {
    return;
}
?>
<div class="dgr-reviews dgr-reviews--summary-only">
    <div class="dgr-reviews__summary">
        <div class="dgr-reviews__score">
            <span class="dgr-reviews__score-value"><?php echo esc_html(number_format_i18n($rating, 1)); ?></span>
            <span class="dgr-reviews__score-meta">
                <?php echo dgr_rating_stars($rating, array('size' => 18)); ?>
                <span class="dgr-reviews__score-count">
                    <?php
                    printf(
                        /* translators: %s: total number of Google reviews */
                        esc_html(_n('%s Google review', '%s Google reviews', $total, 'directorist-google-reviews')),
                        esc_html(number_format_i18n($total))
                    );
                    ?>
                </span>
            </span>
        </div>

        <?php if ($url) : ?>
            <a class="dgr-reviews__source" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer nofollow">
                <?php echo dgr_google_mark(18); ?>
                <span><?php esc_html_e('View on Google', 'directorist-google-reviews'); ?></span>
                <svg class="dgr-reviews__source-arrow" width="12" height="12" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 17L17 7M17 7H9M17 7v8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        <?php else : ?>
            <span class="dgr-reviews__source dgr-reviews__source--static">
                <?php echo dgr_google_mark(18); ?>
                <span><?php esc_html_e('Reviews from Google', 'directorist-google-reviews'); ?></span>
            </span>
        <?php endif; ?>
    </div>
</div>
