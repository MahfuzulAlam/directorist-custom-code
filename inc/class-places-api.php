<?php

/**
 * Google Places Details client.
 *
 * @package Directorist_Google_Reviews
 * @since   3.0
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Places_API')):

    class DGR_Places_API
    {
        const ENDPOINT = 'https://maps.googleapis.com/maps/api/place/details/json';

        /**
         * Google Maps API key.
         *
         * @var string
         */
        protected $api_key;

        public function __construct($api_key = null)
        {
            $this->api_key = $api_key;
        }

        /**
         * Resolve the API key lazily.
         *
         * The client is constructed while plugins are still loading, and
         * Directorist may not have defined its helpers yet, so the option is
         * read on first use rather than in the constructor.
         */
        public function api_key()
        {
            if (null === $this->api_key) {
                $this->api_key = function_exists('get_directorist_option') ? get_directorist_option('map_api_key', '') : '';
            }

            return $this->api_key;
        }

        /**
         * Whether the client is usable.
         */
        public function is_configured()
        {
            return ! empty($this->api_key());
        }

        /**
         * Fetch a place, merging both review sort modes.
         *
         * A single Place Details response is capped at five reviews, and the
         * two sort modes return different sets — "newest" is often all star
         * only ratings while "most_relevant" holds the written ones. Requesting
         * both lifts the ceiling to ten and lets the display lead with reviews
         * that actually have text.
         *
         * @param  string $place_id
         * @return array|null rating, user_ratings_total, url, reviews — null on failure.
         */
        public function fetch_place($place_id)
        {
            if (! $this->is_configured() || ! $place_id) {
                return null;
            }

            $sort     = apply_filters('dgr_reviews_sort', 'most_relevant', $place_id);
            $merge    = (bool) apply_filters('dgr_merge_review_sorts', true, $place_id);
            $language = substr(get_locale(), 0, 2);

            $place = $this->request($place_id, $sort, $language);

            if (null === $place) {
                return null;
            }

            if ($merge) {
                $other     = ('newest' === $sort) ? 'most_relevant' : 'newest';
                $secondary = $this->request($place_id, $other, $language);

                if (null !== $secondary) {
                    $place['reviews'] = $this->merge_reviews($place['reviews'], $secondary['reviews']);
                }
            }

            return $place;
        }

        /**
         * Single Place Details request.
         *
         * @return array|null Normalised result, or null when the call failed.
         */
        protected function request($place_id, $sort, $language)
        {
            $url = add_query_arg(
                [
                    'place_id'     => rawurlencode($place_id),
                    'fields'       => 'rating,user_ratings_total,reviews,url',
                    'reviews_sort' => rawurlencode($sort),
                    'language'     => $language,
                    'key'          => rawurlencode($this->api_key()),
                ],
                self::ENDPOINT
            );

            $response = wp_remote_get($url, ['timeout' => 10]);

            if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
                return null;
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (! is_array($data) || empty($data['status']) || 'OK' !== $data['status']) {
                return null;
            }

            $result = isset($data['result']) && is_array($data['result']) ? $data['result'] : [];

            return [
                'rating'             => isset($result['rating']) ? (float) $result['rating'] : 0,
                'user_ratings_total' => isset($result['user_ratings_total']) ? (int) $result['user_ratings_total'] : 0,
                'url'                => isset($result['url']) ? $result['url'] : '',
                'reviews'            => isset($result['reviews']) && is_array($result['reviews']) ? $result['reviews'] : [],
            ];
        }

        /**
         * Merge two review sets, dropping duplicates.
         *
         * Reviews carrying text are ordered first so the cards lead with
         * something to read, then most recent first within each group.
         */
        protected function merge_reviews($primary, $secondary)
        {
            $merged = [];

            foreach (array_merge((array) $primary, (array) $secondary) as $review) {
                if (! is_array($review)) {
                    continue;
                }

                $author = isset($review['author_name']) ? $review['author_name'] : '';
                $time   = isset($review['time']) ? $review['time'] : '';
                $key    = ('' !== $author || '' !== $time) ? $author . '|' . $time : md5(wp_json_encode($review));

                if (! isset($merged[$key])) {
                    $merged[$key] = $review;
                }
            }

            $merged = array_values($merged);

            usort($merged, function ($a, $b) {
                $a_text = ('' !== trim(isset($a['text']) ? $a['text'] : '')) ? 1 : 0;
                $b_text = ('' !== trim(isset($b['text']) ? $b['text'] : '')) ? 1 : 0;

                if ($a_text !== $b_text) {
                    return $b_text - $a_text;
                }

                $a_time = isset($a['time']) ? (int) $a['time'] : 0;
                $b_time = isset($b['time']) ? (int) $b['time'] : 0;

                return $b_time <=> $a_time;
            });

            return $merged;
        }
    }

endif;
