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

if (! class_exists('Multi_Location_Custom_Field')):

    /**
     * Class Multi_Location_Custom_Field
     */
    class Multi_Location_Custom_Field
    {

        /**
         * Multi_Location_Custom_Field Constructor
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
            $widgets['multilocation'] = [
                'label'   => __('Multi Location', 'directorist-google-reviews'),
                'icon'    => 'la la-map',
                'options' => [
                    'type' => [
                        'type'  => 'hidden',
                        'value' => 'text',
                    ],
                    'field_key' => [
                        'type'  => 'hidden',
                        'value' => 'multilocation',
                        'rules' => [
                            'unique'   => true,
                            'required' => true,
                        ]
                    ],
                    'label' => [
                        'type'  => 'text',
                        'label' => __('Label', 'directorist-google-reviews'),
                        'value' => 'Multi Location',
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
            $widgets['multilocation'] = [
                'label'   => __('Multi Location', 'directorist-google-reviews'),
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => 'Icon',
                        'value' => 'la la-map',
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
            if ($field_data['widget_name'] == 'multilocation') {
                $addresses = isset($field_data['value']) && !empty($field_data['value']) ? json_decode($field_data['value'], true) : [];
                $template .= $this->load_template('templates/add-listing', ['data' => $field_data, 'addresses'=> $addresses]);
            }

            return $template;
        }

        /**
         * Directorist Single Listing Template
         */
        public function directorist_single_item_template($template, $field_data)
        {
            if ($field_data['widget_name'] == 'multilocation') {
                $addresses = isset($field_data['value']) && !empty($field_data['value']) ? json_decode($field_data['value'], true) : [];
                if( count( $addresses ) > 0 ){
                    $template .= $this->load_template('templates/single-listing', ['data' => $field_data, 'addresses'=> $addresses]);
                }
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

            $theme_template  = '/directorist-custom-code/' . $template_file . '.php';
            $plugin_template = DIRECTORIST_MLRS_DIR . $template_file . '.php';

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

    new Multi_Location_Custom_Field();

endif;