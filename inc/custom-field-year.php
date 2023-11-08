<?php

/**
 * @author  wpwax
 * @since   1.0
 * @version 1.0
 */

class Directorist_Custom_Field_Year_Field
{

    public function __construct()
    {
        add_filter('atbdp_form_custom_widgets', array($this, 'atbdp_form_advanced_widgets'));
        add_filter('atbdp_single_listing_content_widgets', array($this, 'atbdp_single_listing_content_widgets'));
        add_filter('directorist_search_form_widgets', array($this, 'directorist_search_form_widgets'));
        add_filter('directorist_field_template', array($this, 'directorist_field_template'), 10, 2);
        add_filter('directorist_single_item_template', array($this, 'directorist_single_item_template'), 10, 2);
        add_filter('directorist_search_field_template', array($this, 'directorist_search_field_template'), 10, 2);
        add_filter('atbdp_listing_search_query_argument', array($this, 'atbdp_listing_search_query_argument'));
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

    public function directorist_search_form_widgets($widgets)
    {
        $widgets['available_widgets']['widgets']['year'] = [
            'options' => [
                'label' => [
                    'type'  => 'text',
                    'label'  => __( 'Label', 'directorist' ),
                    'value' => 'Year',
                ],
                'placeholder' => [
                    'type'  => 'text',
                    'label'  => __( 'Placeholder', 'directorist' ),
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
                'required' => [
                    'type'  => 'toggle',
                    'label'  => __( 'Required', 'directorist' ),
                    'value' => false,
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
        if ('year' === $field_data['widget_name']) {
            if (!empty($field_data['value'])) {
                $this->get_template('single/custom-fields/year', $field_data);
            }
        }
        return $template;
    }

    public function directorist_search_field_template($template, $field_data)
    {
        if ('year' === $field_data['widget_name']) {
            $this->get_template('search-form/custom-fields/year', $field_data);
        }
        return $template;
    }

    /**
     * Template Exists
     */
    public function template_exists($template_file)
    {
        $file = DIRECTORIST_CUSTOM_FIELD_YEAR_DIR . '/templates/' . $template_file . '.php';

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

        $file = DIRECTORIST_CUSTOM_FIELD_YEAR_DIR . '/templates/' . $template_file . '.php';

        if ($this->template_exists($template_file)) {
            include $file;
        }
    }

    /**
     * Seearch Query Manipulation
     */
    public function atbdp_listing_search_query_argument( $args )
    {
        // Get the query parameters from the URL
        $queryParameters = $_GET;
        $year_search = [];

        // Define the prefix to check for
        $prefix_from = 'year_from_';
        $prefix_to = 'year_to_';

        // Loop through the query parameters to check for keys that start with the specified prefix
        foreach ($queryParameters as $key => $value) {
            if (strpos($key, $prefix_from) === 0) {
                $year_from = substr($key, strlen($prefix_from));
                $year_search[$year_from]['from'] = $value;
            }
            if (strpos($key, $prefix_to) === 0) {
                $year_to = substr($key, strlen($prefix_to));
                $year_search[$year_to]['to'] = $value;
            }
        }

        if($year_search and count($year_search) > 0){
            foreach($year_search as $field_key => $search_range){
                $from = isset( $search_range['from'] ) ? $search_range['from'] : '';
                $to = isset( $search_range['to'] ) ? $search_range['to'] : '';
                if( !empty( $from ) && !empty( $to ) && $to >= $from )
                {
                    $args[ 'meta_query' ][ $field_key ] = array(
                        'key' => '_'.$field_key,
                        'value' => array( $from, $to ),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }
            }
        }

        return $args;
    }
}

new Directorist_Custom_Field_Year_Field;