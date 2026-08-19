<?php

/**
 * Presentation helpers for the Google Reviews display.
 *
 * @package Directorist_Google_Reviews
 * @since   2.1
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! function_exists('dgr_rating_stars')) {

    /**
     * Render a 5 star row with fractional fill.
     *
     * The row is painted twice: a muted track and a clipped, coloured overlay
     * whose width is driven by the --dgr-stars-fill custom property. That keeps
     * half stars crisp at any size without needing extra glyphs or images.
     *
     * @param  float $rating Rating between 0 and 5.
     * @param  array $args   size (px) and an optional accessible label.
     * @return string
     */
    function dgr_rating_stars($rating, $args = array())
    {
        $args = wp_parse_args($args, array(
            'size'  => 16,
            'label' => '',
        ));

        $rating = max(0, min(5, (float) $rating));
        $size   = absint($args['size']) ? absint($args['size']) : 16;

        $star = sprintf(
            '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2.4l2.9 5.88 6.5.95-4.7 4.58 1.11 6.47L12 17.22l-5.81 3.06 1.11-6.47-4.7-4.58 6.5-.95L12 2.4z"/></svg>',
            $size
        );

        $row = str_repeat($star, 5);

        $label = $args['label'] ? $args['label'] : sprintf(
            /* translators: %s: rating value, e.g. 4.5 */
            __('Rated %s out of 5', 'directorist-google-reviews'),
            number_format_i18n($rating, 1)
        );

        return sprintf(
            '<span class="dgr-stars" style="--dgr-stars-fill:%1$s%%" role="img" aria-label="%2$s"><span class="dgr-stars__track" aria-hidden="true">%3$s</span><span class="dgr-stars__fill" aria-hidden="true">%3$s</span></span>',
            esc_attr(round(($rating / 5) * 100, 2)),
            esc_attr($label),
            $row
        );
    }
}

if (! function_exists('dgr_review_avatar')) {

    /**
     * Render a reviewer avatar.
     *
     * The tinted initial always sits underneath the photo so a blocked or
     * broken Google profile image degrades to a coloured monogram instead of a
     * grey box. The hue is derived from the name so it stays stable per person.
     *
     * @param  array $review Single review entry from the Places API.
     * @return string
     */
    function dgr_review_avatar($review)
    {
        $name    = isset($review['author_name']) ? trim($review['author_name']) : '';
        $photo   = isset($review['profile_photo_url']) ? $review['profile_photo_url'] : '';
        $initial = $name ? mb_strtoupper(mb_substr($name, 0, 1)) : '?';
        $hue     = $name ? abs(crc32($name)) % 360 : 210;

        $image = $photo ? sprintf(
            '<img src="%s" alt="" width="44" height="44" loading="lazy" decoding="async" referrerpolicy="no-referrer">',
            esc_url($photo)
        ) : '';

        return sprintf(
            '<span class="dgr-review__avatar" style="--dgr-avatar-hue:%1$d" aria-hidden="true"><span class="dgr-review__initial">%2$s</span>%3$s</span>',
            (int) $hue,
            esc_html($initial),
            $image
        );
    }
}

if (! function_exists('dgr_google_mark')) {

    /**
     * The Google "G" mark, required attribution for displayed Places data.
     *
     * @param  int $size Icon size in px.
     * @return string
     */
    function dgr_google_mark($size = 18)
    {
        $size = absint($size) ? absint($size) : 18;

        return sprintf(
            '<svg class="dgr-google-mark" width="%1$d" height="%1$d" viewBox="0 0 48 48" aria-hidden="true" focusable="false">' .
                '<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>' .
                '<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>' .
                '<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>' .
                '<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>' .
            '</svg>',
            $size
        );
    }
}
