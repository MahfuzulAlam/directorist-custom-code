<?php

/**
 * Directorist form and single listing fields for Google Reviews.
 *
 * Registers the Google Place submission field plus the single listing display
 * fields, and hands rendering the data it gets from DGR_Sync.
 *
 * @package Directorist_Google_Reviews
 * @since   1.0
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Custom_Field')):

    class DGR_Custom_Field
    {
        /**
         * Meta key holding the selected Google place.
         */
        const PLACE_META = '_google_place';

        /**
         * @var DGR_Sync
         */
        protected $sync;

        public function __construct($sync = null)
        {
            $this->sync = $sync ? $sync : new DGR_Sync();

            add_filter('atbdp_form_preset_widgets', [$this, 'register_custom_field']);
            add_filter('atbdp_single_listing_content_widgets', [$this, 'single_listing_content_widgets']);
            add_filter('atbdp_single_listing_other_fields_widget', [$this, 'single_listing_other_widgets']);
            add_filter('directorist_field_template', [$this, 'directorist_field_template'], 10, 2);
            add_filter('directorist_single_item_template', [$this, 'directorist_single_item_template'], 10, 2);
        }

        /**
         * Register the Google Place field on the add listing form.
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
         * Single listing "Preset Fields" widget — the review cards.
         */
        public function single_listing_content_widgets($widgets)
        {
            $widgets['google_place'] = [
                'label'   => __('Google Reviews', 'directorist-google-reviews'),
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => __('Icon', 'directorist-google-reviews'),
                        'value' => 'la la-comment',
                    ],
                ]
            ];

            return $widgets;
        }

        /**
         * Single listing "Other Fields" widgets — the rating displays.
         *
         * These live here rather than alongside the preset form fields because
         * they are derived from stored listing meta, not from a submitted
         * value, and preset widgets are skipped when their form field is empty.
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

            $widgets['google_rating_stats'] = [
                'type'    => 'widget',
                'label'   => __('Google Rating & Total Reviews', 'directorist-google-reviews'),
                'icon'    => 'la la-chart-bar',
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => __('Icon', 'directorist-google-reviews'),
                        'value' => 'la la-chart-bar',
                    ],
                ],
            ];

            return $widgets;
        }

        /**
         * Add listing form template.
         */
        public function directorist_field_template($template, $field_data)
        {
            if (isset($field_data['widget_name']) && 'google_place' === $field_data['widget_name']) {
                $this->load_template('add-listing', ['data' => $field_data]);
            }

            return $template;
        }

        /**
         * Single listing templates.
         *
         * Directorist treats the filtered value as a template path and loads it
         * through Helper::get_template(). No core template exists for these
         * widgets, so the markup is echoed here and the path is returned intact.
         */
        public function directorist_single_item_template($template, $field_data)
        {
            $widget = isset($field_data['widget_name']) ? $field_data['widget_name'] : '';

            $templates = [
                'google_place'        => 'single-listing',
                'google_rating'       => 'rating-summary',
                'google_rating_stats' => 'rating-stats',
            ];

            if (! isset($templates[$widget])) {
                return $template;
            }

            $place = $this->get_listing_place($field_data);

            $this->load_template($templates[$widget], [
                'data'    => $field_data,
                'place'   => $place,
                'reviews' => isset($place['reviews']) ? $place['reviews'] : [],
            ]);

            return $template;
        }

        /**
         * Stored place data for the listing being rendered.
         *
         * The reviews widget carries the submitted field value, while the
         * rating widgets have none and read the listing meta instead.
         */
        public function get_listing_place($field_data)
        {
            $listing_id = ! empty($field_data['listing_id']) ? (int) $field_data['listing_id'] : 0;

            if (! $listing_id) {
                return [];
            }

            $raw = ! empty($field_data['value']) ? $field_data['value'] : get_post_meta($listing_id, self::PLACE_META, true);

            $field_value = $raw ? json_decode($raw) : null;
            $place_id    = ! empty($field_value->place_id) ? $field_value->place_id : '';

            return $this->sync->get_listing_data($listing_id, $place_id);
        }

        /**
         * Render a template, preferring a theme override.
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
    }

endif;
