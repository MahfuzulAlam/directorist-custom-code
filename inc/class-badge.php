<?php

/**
 * @author  wpwax
 * @since   1.0
 * @version 1.0
 */

class Directorist_Badge
{
    public $atts;

    public function __construct( $atts = [] )
    {
        $this->atts = $atts;
        $this->render();
    }

    public function render()
    {
        add_filter( 'atbdp_listing_type_settings_field_list', [ $this, 'atbdp_listing_type_settings_field_list' ] );
        add_action( 'atbdp_all_listings_badge_template', [ $this, 'atbdp_all_listings_badge_template' ] );
    }

    public function atbdp_listing_type_settings_field_list( $fields )
    {
        foreach ( $fields as $key => $value ) {
            // setup widgets
            $widget = [
                'type'    => "badge",
                'id'      => $this->atts[ 'id' ],
                'label'   => $this->atts[ 'label' ],
                'icon'    => $this->atts[ 'icon' ] ? $this->atts[ 'icon' ]: "uil uil-text-fields",
                'hook'    => $this->atts[ 'hook' ],
                'options' => [],
            ];
    
            if ( 'listings_card_grid_view' === $key ) {
                // register widget
                $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
    
                // grid with preview image
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_left']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_right']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_left']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], $this->atts[ 'id' ] );
    
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['right']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['left']['acceptedWidgets'], $this->atts[ 'id' ] );
    
                // grid without preview image
                array_push( $fields[$key]['card_templates']['grid_view_without_thumbnail']['layout']['body']['quick_info']['acceptedWidgets'], $this->atts[ 'id' ] );
            }
    
            if ( 'listings_card_list_view' === $key ) {
                // register widget
                $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
    
                // grid with preview image
                array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['right']['acceptedWidgets'], $this->atts[ 'id' ] );
    
                // grid without preview image
                array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['top']['acceptedWidgets'], $this->atts[ 'id' ] );
                array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['right']['acceptedWidgets'], $this->atts[ 'id' ] );
            }
    
        }

        return $fields;

    }

    public function atbdp_all_listings_badge_template( $field )
    {
        e_var_dump($field['id']);
    }

}