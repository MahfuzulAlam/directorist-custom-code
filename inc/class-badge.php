<?php

/**
 * @author  wpwax
 * @since   1.0
 * @version 1.0
 */

class Shanir_Akhra_Badge
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
        // Check if required attributes are set
        if ( ! isset( $this->atts['id'] ) || empty( $this->atts['id'] ) ) {
            return $fields;
        }

        foreach ( $fields as $key => $value ) {
            // setup widgets
            $widget = [
                'type'    => "badge",
                'id'      => $this->atts[ 'id' ],
                'label'   => isset( $this->atts[ 'label' ] ) ? $this->atts[ 'label' ] : '',
                'icon'    => isset( $this->atts[ 'icon' ] ) && ! empty( $this->atts[ 'icon' ] ) ? $this->atts[ 'icon' ] : "uil uil-text-fields",
                'hook'    => isset( $this->atts[ 'hook' ] ) ? $this->atts[ 'hook' ] : '',
                'options' => [],
            ];
    
            if ( 'listings_card_grid_view' === $key ) {
                // Check if the required array structure exists
                if ( isset( $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets'] ) ) {
                    $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                }
                if ( isset( $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets'] ) ) {
                    $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                }
    
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
                // Check if the required array structure exists
                if ( isset( $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets'] ) ) {
                    $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                }
                if ( isset( $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets'] ) ) {
                    $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets'][$this->atts[ 'id' ]] = $widget;
                }
    
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
        // Check if required parameters exist
        if ( ! isset( $field['widget_key'] ) || ! isset( $this->atts['id'] ) ) {
            return;
        }

        switch ( $field['widget_key'] ) {
            case $this->atts[ 'id' ]:
                if ( $this->atts[ 'id' ] == 'category-badge' ) {
                
                    // get categories names
                    $categories = wp_get_post_terms( get_the_ID(), 'at_biz_dir-category', [ 'fields' => 'names', 'hide_enpty' => false ] );

                    if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                        foreach ( $categories as $category ) {
                        ?>
                            <span id="<?php echo esc_attr( $this->atts[ 'id' ] ); ?>" class="directorist-badge directorist-info-item directorist-custom-badge-archive <?php echo esc_attr( isset( $this->atts[ 'class' ] ) ? $this->atts[ 'class' ] : '' ); ?>">
                                <?php echo esc_html( $category ); ?>
                            </span>
                        <?php
                        }
                    }

                }else if ( $this->atts[ 'id' ] == 'listing-view-badge' ) {

                    $listing_view = get_post_meta( get_the_ID(), '_listing_views', true );

                    if ( $listing_view ) {
                        ?>
                            <span id="<?php echo esc_attr( $this->atts[ 'id' ] ); ?>" class="directorist-badge directorist-info-item directorist-custom-badge-archive <?php echo esc_attr( isset( $this->atts[ 'class' ] ) ? $this->atts[ 'class' ] : '' ); ?>">
                                <?php echo directorist_icon('fa fa-eye'); ?>
                                <?php echo ' ' . esc_html( $listing_view ); ?>
                            </span>
                        <?php
                        }

                } else {
                    // Check if meta_key and meta_value are set
                    if ( isset( $this->atts[ 'meta_key' ] ) && isset( $this->atts[ 'meta_value' ] ) ) {
                        $free_trial = get_post_meta( get_the_ID(), $this->atts[ 'meta_key' ], true );
            
                        if ( $free_trial == $this->atts[ 'meta_value' ] ) {
                        ?>
                            <span id="<?php echo esc_attr( $this->atts[ 'id' ] ); ?>" class="directorist-badge directorist-info-item directorist-custom-badge-archive <?php echo esc_attr( isset( $this->atts[ 'class' ] ) ? $this->atts[ 'class' ] : '' ); ?>">
                                <?php echo esc_html( isset( $this->atts[ 'title' ] ) ? $this->atts[ 'title' ] : '' ); ?>
                            </span>
                        <?php
                        }
                    }
                }
    
            break;
        }
    }

}