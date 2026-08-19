<?php

/**
 * Google Review Carousel — horizontally scrolling review cards.
 *
 * The cards reuse the .dgr-review design from the review grid; this template
 * only arranges them in a scroll-snap track with arrow and dot controls.
 * Reads stored data only — it never calls the Google API.
 *
 * @author  wpWax
 * @since   3.2
 * @version 3.2
 *
 * @var array $data    Field data from the single listing layout builder.
 * @var array $place   Stored listing data.
 * @var array $reviews Review entries.
 */

if (! defined('ABSPATH')) exit;

$reviews = (isset($reviews) && is_array($reviews)) ? $reviews : array();

$limit   = (int) apply_filters('dgr_carousel_display_limit', 6);
$reviews = ($limit > 0) ? array_slice($reviews, 0, $limit) : $reviews;

if (! $reviews) {
    return;
}

$place     = (isset($place) && is_array($place)) ? $place : array();
$place_url = ! empty($place['url']) ? $place['url'] : '';
?>
<div class="dgr-reviews dgr-carousel">

    <div class="dgr-carousel__head">
        <?php if ($place_url) : ?>
            <a class="dgr-reviews__source" href="<?php echo esc_url($place_url); ?>" target="_blank" rel="noopener noreferrer nofollow">
                <?php echo dgr_google_mark(18); ?>
                <span><?php esc_html_e('Reviews from Google', 'directorist-google-reviews'); ?></span>
                <svg class="dgr-reviews__source-arrow" width="12" height="12" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 17L17 7M17 7H9M17 7v8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        <?php else : ?>
            <span class="dgr-reviews__source dgr-reviews__source--static">
                <?php echo dgr_google_mark(18); ?>
                <span><?php esc_html_e('Reviews from Google', 'directorist-google-reviews'); ?></span>
            </span>
        <?php endif; ?>

        <div class="dgr-carousel__nav">
            <button type="button" class="dgr-carousel__arrow dgr-carousel__arrow--prev" aria-label="<?php esc_attr_e('Previous review', 'directorist-google-reviews'); ?>" hidden>
                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="dgr-carousel__arrow dgr-carousel__arrow--next" aria-label="<?php esc_attr_e('Next review', 'directorist-google-reviews'); ?>" hidden>
                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>

    <div class="dgr-carousel__track" role="group" aria-label="<?php esc_attr_e('Google reviews', 'directorist-google-reviews'); ?>" tabindex="0">
        <?php
        foreach ($reviews as $review) :
            $text  = isset($review['text']) ? trim($review['text']) : '';
            $stars = isset($review['rating']) ? (float) $review['rating'] : 0;

            if ('' === $text && ! $stars) {
                continue;
            }

            $author     = isset($review['author_name']) ? $review['author_name'] : __('Google user', 'directorist-google-reviews');
            $author_url = isset($review['author_url']) ? $review['author_url'] : '';
            $when       = isset($review['relative_time_description']) ? $review['relative_time_description'] : '';

            if (! $when && ! empty($review['time'])) {
                /* translators: %s: human readable time difference, e.g. "2 months" */
                $when = sprintf(__('%s ago', 'directorist-google-reviews'), human_time_diff((int) $review['time'], current_time('timestamp')));
            }
        ?>
            <article class="dgr-review dgr-carousel__slide<?php echo '' === $text ? ' dgr-review--rating-only' : ''; ?>">
                <div class="dgr-review__head">
                    <?php echo dgr_review_avatar($review); ?>
                    <div class="dgr-review__identity">
                        <?php if ($author_url) : ?>
                            <a class="dgr-review__author" href="<?php echo esc_url($author_url); ?>" target="_blank" rel="noopener noreferrer nofollow"><?php echo esc_html($author); ?></a>
                        <?php else : ?>
                            <span class="dgr-review__author"><?php echo esc_html($author); ?></span>
                        <?php endif; ?>

                        <?php if ($when) : ?>
                            <span class="dgr-review__time"><?php echo esc_html($when); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($stars) : ?>
                    <?php echo dgr_rating_stars($stars, array('size' => 14)); ?>
                <?php endif; ?>

                <?php if ('' !== $text) : ?>
                    <p class="dgr-review__text"><?php
                        $paragraphs = preg_split('/\n{2,}/', $text);
                        $paragraphs = array_filter(array_map('trim', (array) $paragraphs), 'strlen');

                        foreach ($paragraphs as $paragraph) {
                            echo '<span class="dgr-review__para">' . nl2br(esc_html($paragraph)) . '</span>';
                        }
                    ?></p>
                <?php endif; ?>

                <?php if (! empty($review['translated'])) : ?>
                    <span class="dgr-review__translated"><?php esc_html_e('Translated by Google', 'directorist-google-reviews'); ?></span>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <?php /* translators: %d: review number */ ?>
    <div class="dgr-carousel__dots" data-label="<?php echo esc_attr__('Go to review %d', 'directorist-google-reviews'); ?>" hidden></div>

</div>
