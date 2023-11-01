<?php

/**
 * @author  wpwax
 * @since   1.0
 * @version 1.0
 */

class Directorist_Custom_Field_Year
{

    public function __construct()
    {
        add_filter('atbdp_form_custom_widgets', array($this, 'atbdp_form_advanced_widgets'));
        add_filter('atbdp_single_listing_content_widgets', array($this, 'atbdp_single_listing_content_widgets'));
        add_filter('directorist_field_template', array($this, 'directorist_field_template'), 10, 2);
        add_filter('directorist_single_item_template', array($this, 'directorist_single_item_template'), 10, 2);
    }

    public function atbdp_form_advanced_widgets($widgets)
    {
        $manager = new Directorist\Multi_Directory_Manager();
        $widgets['year'] = [
            'label' => 'Year',
            'icon' => 'uil uil-calender',
            'options' => [
                'type' => [
                    'type'  => 'hidden',
                    'value' => 'year',
                ],
                'label' => [
                    'type'  => 'text',
                    'label' => __( 'Label', 'directorist' ),
                    'value' => 'Year',
                ],
                'start_year' => [
                    'type'  => 'number',
                    'label' => __( 'Start Year', 'directorist' ),
                    'value' => 1900,
                ],
                'end_year' => [
                    'type'  => 'number',
                    'label' => __( 'End Year', 'directorist' ),
                    'value' => 2100,
                ],
                'field_key' => [
                    'type'   => 'text',
                    'value'  => 'year',
                    'rules' => [
                        'unique' => true,
                        'required' => true,
                    ]
                ],
                'placeholder' => [
                    'type'  => 'text',
                    'label' => __( 'Placeholder', 'directorist' ),
                    'value' => '',
                ],
                'description' => [
                    'type'  => 'text',
                    'label' => __( 'Description', 'directorist' ),
                    'value' => '',
                ],
                'required' => [
                    'type'  => 'toggle',
                    'label'  => __( 'Required', 'directorist' ),
                    'value' => false,
                ],
                'only_for_admin' => [
                    'type'  => 'toggle',
                    'label'  => __( 'Only For Admin Use', 'directorist' ),
                    'value' => false,
                ],
                'assign_to' => $manager->get_assign_to_field(),
                'category' => $manager->get_category_select_field([
                    'show_if' => [
                        'where' => "self.assign_to",
                        'conditions' => [
                            ['key' => 'value', 'compare' => '=', 'value' => 'category'],
                        ],
                    ],
                ]),
            ]

            ];
        return $widgets;
    }

    public function atbdp_single_listing_content_widgets($widgets)
    {
        $widgets['year'] = [
            'options' => [
                'icon' => [
                    'type'  => 'icon',
                    'label' => __( 'Icon', 'directorist' ),
                    'value' => 'las la-calendar',
                ],
            ]
        ];
        return $widgets;
    }

    public function directorist_field_template($template, $field_data)
    {
        if ('year' === $field_data['widget_name']) {
            $this->get_template('listing-form/custom-fields/year', $field_data);
        }
        return $template;
    }


    public function directorist_single_item_template($template, $field_data)
    {
        if ('youtube-video' === $field_data['widget_name']) {
            if (!empty($field_data['value'])) {
                $this->get_template('single/date', $field_data);
            }
        }
        return $template;
    }

    /**
     * Template Exists
     */
    public function template_exists($template_file)
    {
        $file = DIRECTORIST_CUSTOM_CODE_DIR . '/templates/' . $template_file . '.php';

        if (file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Template
     */
    public function get_template($template_file, $args = array())
    {
        if (is_array($args)) {
            extract($args);
        }
        $data = $args;

        if (isset($args['form'])) $listing_form = $args['form'];

        $file = DIRECTORIST_CUSTOM_CODE_DIR . '/templates/' . $template_file . '.php';

        if ($this->template_exists($template_file)) {
            include $file;
        }
    }
}

new Directorist_Custom_Field_Year;