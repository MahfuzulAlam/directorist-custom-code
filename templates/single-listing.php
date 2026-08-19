<?php

/**
 * Google Reviews — single listing review cards.
 *
 * The rating summary is a separate widget ("Google Rating"), so this template
 * renders only the review list.
 *
 * @author  wpXplore
 * @since   2.0
 * @version 2.2
 *
 * @var array $data    Field data from the single listing layout builder.
 * @var array $place   Normalised Places payload.
 * @var array $reviews Review entries.
 */

if (! defined('ABSPATH')) exit;

$reviews = (isset($reviews) && is_array($reviews)) ? $reviews : array();

// Google's Place Details API returns a maximum of 5 reviews per place, so this
// is an upper bound rather than a guarantee.
$limit   = (int) apply_filters('dgr_reviews_display_limit', 6);
$reviews = ($limit > 0) ? array_slice($reviews, 0, $limit) : $reviews;

if (! $reviews) {
    return;
}

$more_label = __('Read more', 'directorist-google-reviews');
$less_label = __('Show less', 'directorist-google-reviews');
?>
<div class="dgr-reviews">
    <ul class="dgr-reviews__list">
        <?php
        foreach ($reviews as $review) :
            $text  = isset($review['text']) ? trim($review['text']) : '';
            $stars = isset($review['rating']) ? (float) $review['rating'] : 0;

            // A review with neither a score nor a comment has nothing to show.
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

            // Star only ratings are common on busy places, especially when
            // sorting by newest. They stay in the list as compact cards.
            $classes = 'dgr-review' . ('' === $text ? ' dgr-review--rating-only' : '');
        ?>
            <li class="<?php echo esc_attr($classes); ?>">
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
                        // Paragraphs are spaced with CSS rather than a literal
                        // blank line, so the clamp never truncates on an empty row.
                        $paragraphs = preg_split('/\n{2,}/', $text);
                        $paragraphs = array_filter(array_map('trim', (array) $paragraphs), 'strlen');

                        foreach ($paragraphs as $paragraph) {
                            echo '<span class="dgr-review__para">' . nl2br(esc_html($paragraph)) . '</span>';
                        }
                    ?></p>

                    <button type="button" class="dgr-review__toggle" aria-expanded="false" hidden
                        data-more="<?php echo esc_attr($more_label); ?>"
                        data-less="<?php echo esc_attr($less_label); ?>"><?php echo esc_html($more_label); ?></button>
                <?php endif; ?>

                <?php if (! empty($review['translated'])) : ?>
                    <span class="dgr-review__translated"><?php esc_html_e('Translated by Google', 'directorist-google-reviews'); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
