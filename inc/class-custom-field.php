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
         * DGR_Custom_Field Constructor
         */
        public function __construct()
        {
            add_filter('atbdp_form_preset_widgets', [$this, 'register_custom_field']);
            add_filter('atbdp_single_listing_content_widgets', [$this, 'single_listing_content_widgets']);
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
         */
        public function directorist_single_item_template($template, $field_data)
        {
            if ($field_data['widget_name'] == 'google_place') {
                $google_api = get_directorist_option('map_api_key', '');
                $field_value = $field_data['value'] ? json_decode($field_data['value']) : [];
                $place_id = $field_value ? $field_value->place_id : '';
                $google_reviews = $google_api && $place_id ? $this->fetch_google_reviews($place_id, $google_api) : [];
                $template .= $this->load_template('single-listing', ['data' => $field_data, 'reviews' => $google_reviews]);
            }

            return $template;
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
         * Fetch Reviews
         */
        public function fetch_google_reviews($place_id, $api_key)
        {
            $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$place_id}&fields=reviews,rating,user_ratings_total&key={$api_key}";

            //   $json = file_get_contents($url);
            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                return [];
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            return $data['result']['reviews'] ?? [];
        }
    }

    new DGR_Custom_Field();

endif;
