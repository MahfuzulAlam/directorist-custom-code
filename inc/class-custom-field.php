<?php

/**
 * Directorist_Google_Reviews DGR_Custom_Field
 *
 * This class is for preparing the custom field for the google reviews
 *
 * @package     Directorist_Google_Reviews
 * @since       1.0
 */

// Exit if accessed directly.
defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Custom_Field')):

    /**
     * Class DGR_Custom_Field
     */
    class DGR_Custom_Field
    {

        /**
         * Bump to invalidate every cached place, e.g. after changing the query.
         */
        const CACHE_VERSION = '2.3';

        /**
         * How long a successful Places response is cached for.
         */
        const CACHE_TTL = 12 * HOUR_IN_SECONDS;

        /**
         * How long a failed Places response is cached for.
         */
        const ERROR_CACHE_TTL = 15 * MINUTE_IN_SECONDS;

        /**
         * DGR_Custom_Field Constructor
         */
        public function __construct()
        {
            add_filter('atbdp_form_preset_widgets', [$this, 'register_custom_field']);
            add_filter('atbdp_single_listing_content_widgets', [$this, 'single_listing_content_widgets']);
            add_filter('atbdp_single_listing_other_fields_widget', [$this, 'single_listing_other_widgets']);
            add_filter('directorist_field_template', [$this, 'directorist_field_template'], 10, 2);
            add_filter('directorist_single_item_template', [$this, 'directorist_single_item_template'], 10, 2);
        }


        /**
         * Register a custom field
         */
        public function register_custom_field($widgets)
        {
            $widgets['google_place'] = [
                'label'   => __('Google Place', 'directorist-google-reviews'),
                'icon'    => 'la la-comment',
                'options' => [
                    'type' => [
                        'type'  => 'hidden',
                        'value' => 'text',
                    ],
                    'field_key' => [
                        'type'  => 'hidden',
                        'value' => 'google_place',
                        'rules' => [
                            'unique'   => true,
                            'required' => true,
                        ]
                    ],
                    'label' => [
                        'type'  => 'text',
                        'label' => __('Label', 'directorist-google-reviews'),
                        'value' => 'Google Place',
                    ],
                    'placeholder' => [
                        'type'  => 'text',
                        'label' => __('Placeholder', 'directorist'),
                        'value' => __('Select a place from google', 'directorist-google-reviews'),
                    ],
                    'required' => [
                        'type'  => 'toggle',
                        'label' => __('Required', 'directorist-google-reviews'),
                        'value' => false,
                    ],
                    'only_for_admin' => [
                        'type'  => 'toggle',
                        'label' => __('Admin Only', 'directorist-google-reviews'),
                        'value' => false,
                    ],
                ],
            ];

            return $widgets;
        }

        /**
         * Single listing content widget
         */
        public function single_listing_content_widgets($widgets)
        {
            $widgets['google_place'] = [
                'label'   => __('Google Reviews', 'directorist-google-reviews'),
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => 'Icon',
                        'value' => 'la la-comment',
                    ],
                ]
            ];
            return $widgets;
        }

        /**
         * Single listing "Other Fields" widget.
         *
         * The rating summary lives here rather than alongside the preset form
         * fields: it is derived from the Places response, not from a value the
         * user submitted, and preset widgets are skipped when their submission
         * field is empty.
         */
        public function single_listing_other_widgets($widgets)
        {
            $widgets['google_rating'] = [
                'type'    => 'widget',
                'label'   => __('Google Rating', 'directorist-google-reviews'),
                'icon'    => 'la la-star',
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => __('Icon', 'directorist-google-reviews'),
                        'value' => 'la la-star',
                    ],
                ],
            ];

            return $widgets;
        }

        /**
         * Directorist Field Template
         */
        public function directorist_field_template($template, $field_data)
        {
            if ($field_data['widget_name'] == 'google_place') {
                $template .= $this->load_template('add-listing', ['data' => $field_data]);
            }

            return $template;
        }

        /**
         * Directorist Single Listing Template
         *
         * Directorist treats the filtered value as a template path and loads it
         * through Helper::get_template(). No such core template exists for this
         * widget, so the markup is echoed here and the path is returned intact.
         */
        public function directorist_single_item_template($template, $field_data)
        {
            $widget = isset($field_data['widget_name']) ? $field_data['widget_name'] : '';

            if ('google_place' !== $widget && 'google_rating' !== $widget) {
                return $template;
            }

            $place = $this->get_listing_place($field_data);

            if ('google_rating' === $widget) {
                $this->load_template('rating-summary', [
                    'data'  => $field_data,
                    'place' => $place,
                ]);

                return $template;
            }

            $this->load_template('single-listing', [
                'data'    => $field_data,
                'place'   => $place,
                'reviews' => isset($place['reviews']) ? $place['reviews'] : [],
            ]);

            return $template;
        }

        /**
         * Resolve the Places payload for the listing being rendered.
         *
         * The reviews widget carries the submitted field value, while the
         * standalone rating widget has none and reads the listing meta instead.
         */
        public function get_listing_place($field_data)
        {
            $google_api = get_directorist_option('map_api_key', '');

            if (! $google_api) {
                return [];
            }

            $raw = ! empty($field_data['value']) ? $field_data['value'] : '';

            if (! $raw && ! empty($field_data['listing_id'])) {
                $raw = get_post_meta($field_data['listing_id'], '_google_place', true);
            }

            $field_value = $raw ? json_decode($raw) : null;
            $place_id    = ! empty($field_value->place_id) ? $field_value->place_id : '';

            return $place_id ? $this->fetch_google_place($place_id, $google_api) : [];
        }

        /**
         * Load Template
         */
        public function load_template($template_file, $args = array())
        {
            if (is_array($args)) {
                extract($args);
            }

            $theme_template  = '/directorist-google-reviews/' . $template_file . '.php';
            $plugin_template = DIRECTORIST_GOOGLE_REVIEWS_PATH . $template_file . '.php';

            if (file_exists(get_stylesheet_directory() . $theme_template)) {
                $file = get_stylesheet_directory() . $theme_template;
            } elseif (file_exists(get_template_directory() . $theme_template)) {
                $file = get_template_directory() . $theme_template;
            } else {
                $file = $plugin_template;
            }

            if (file_exists($file)) {
                include $file;
            }
        }

        /**
         * Fetch a place from the Places Details API.
         *
         * Google caps a single Place Details response at five reviews, and the
         * two sort modes return different sets — "newest" is often all star
         * only ratings, while "most_relevant" holds the written ones. Both are
         * requested and merged so the widget can show more than five cards and
         * still lead with reviews that have text.
         *
         * Results are cached in a transient. Without it every single listing
         * view would make a blocking HTTP call to Google, which both slows the
         * page down and bills a Places request per pageview.
         *
         * @param  string $place_id
         * @param  string $api_key
         * @return array  rating, user_ratings_total, reviews, url — or [] on failure.
         */
        public function fetch_google_place($place_id, $api_key)
        {
            $sort     = apply_filters('dgr_reviews_sort', 'most_relevant', $place_id);
            $merge    = (bool) apply_filters('dgr_merge_review_sorts', true, $place_id);
            $language = substr(get_locale(), 0, 2);

            // Everything that changes the response is part of the key, so a
            // settings change takes effect immediately instead of after the TTL.
            $cache_key = 'dgr_place_' . md5(implode('|', [self::CACHE_VERSION, $place_id, $api_key, $sort, $merge ? 'merged' : 'single', $language]));
            $cached    = get_transient($cache_key);

            if (is_array($cached)) {
                return $cached;
            }

            $ttl     = (int) apply_filters('dgr_place_cache_ttl', self::CACHE_TTL, $place_id);
            $primary = $this->request_place_details($place_id, $api_key, $sort, $language);

            if (null === $primary) {
                // Cache failures briefly too, so an outage cannot hammer the API.
                set_transient($cache_key, [], self::ERROR_CACHE_TTL);
                return [];
            }

            if ($merge) {
                $other     = ('newest' === $sort) ? 'most_relevant' : 'newest';
                $secondary = $this->request_place_details($place_id, $api_key, $other, $language);

                if (null !== $secondary) {
                    $primary['reviews'] = $this->merge_reviews($primary['reviews'], $secondary['reviews']);
                }
            }

            set_transient($cache_key, $primary, $ttl);

            return $primary;
        }

        /**
         * Single Place Details request.
         *
         * @return array|null Normalised result, or null when the call failed.
         */
        protected function request_place_details($place_id, $api_key, $sort, $language)
        {
            $url = add_query_arg([
                'place_id'     => rawurlencode($place_id),
                'fields'       => 'rating,user_ratings_total,reviews,url',
                'reviews_sort' => rawurlencode($sort),
                'language'     => $language,
                'key'          => rawurlencode($api_key),
            ], 'https://maps.googleapis.com/maps/api/place/details/json');

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
                'reviews'            => isset($result['reviews']) && is_array($result['reviews']) ? $result['reviews'] : [],
                'url'                => isset($result['url']) ? $result['url'] : '',
            ];
        }

        /**
         * Merge two review sets, dropping duplicates.
         *
         * Reviews that carry text are ordered first so the cards lead with
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
                $key    = ($time || $author) ? $author . '|' . $time : md5(serialize($review));

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

        /**
         * Fetch Reviews
         *
         * @deprecated 2.1 Use fetch_google_place() instead.
         */
        public function fetch_google_reviews($place_id, $api_key)
        {
            $place = $this->fetch_google_place($place_id, $api_key);

            return isset($place['reviews']) ? $place['reviews'] : [];
        }
    }

    new DGR_Custom_Field();

endif;
