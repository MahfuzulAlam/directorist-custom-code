<?php

/**
 * Add your custom php code here
 */

 /**
  * Free Trial Badge
  */

add_filter( 'atbdp_listing_type_settings_field_list', 'free_trial_custom_atbdp_listing_type_settings_field_list' );

function free_trial_custom_atbdp_listing_type_settings_field_list( $fields ) {

    foreach ( $fields as $key => $value ) {
        // setup widgets
        $free_trial_widget = [
            'type'    => "badge",
            'id'      => "free_trial_badge",
            'label'   => "Free Trial",
            'icon'    => "uil uil-text-fields",
            'hook'    => "atbdp_free_trial_badge",
            'options' => [],
        ];

        if ( 'listings_card_grid_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets']['free_trial_badge'] = $free_trial_widget;
            $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets']['free_trial_badge'] = $free_trial_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_left']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_right']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_left']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'free_trial_badge' );

            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['right']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['left']['acceptedWidgets'], 'free_trial_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['grid_view_without_thumbnail']['layout']['body']['quick_info']['acceptedWidgets'], 'free_trial_badge' );
        }

        if ( 'listings_card_list_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets']['free_trial_badge'] = $free_trial_widget;
            $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets']['free_trial_badge'] = $free_trial_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'free_trial_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'free_trial_badge' );
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'free_trial_badge' );
        }

    }

    return $fields;
}


add_action( 'atbdp_all_listings_badge_template', 'free_trial_custom_atbdp_all_listings_badge_template' );

function free_trial_custom_atbdp_all_listings_badge_template( $field ) {

    switch ($field['widget_key']) {
        case 'free_trial_badge':

            $free_trial = get_post_meta(get_the_ID(), '_free_trial', true);

            if ( $free_trial == 'yes' ):
            ?>
                <span class="directorist-badge directorist-info-item directorist-badge-free-trial">Free Trial</span>
            <?php
            endif;

        break;
    }
}


add_filter( 'atbdp_listing_type_settings_field_list', 'affiliate_custom_atbdp_listing_type_settings_field_list' );

function affiliate_custom_atbdp_listing_type_settings_field_list( $fields ) {

    foreach ( $fields as $key => $value ) {
        // setup widgets
        $affiliate_program_widget = [
            'type'    => "badge",
            'id'      => "affiliate_program_badge",
            'label'   => "Affiliate Program",
            'icon'    => "uil uil-text-fields",
            'hook'    => "atbdp_affiliate_program_badge",
            'options' => [],
        ];

        if ( 'listings_card_grid_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets']['affiliate_program_badge'] = $affiliate_program_widget;
            $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets']['affiliate_program_badge'] = $affiliate_program_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_left']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_right']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_left']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'affiliate_program_badge' );

            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['right']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['left']['acceptedWidgets'], 'affiliate_program_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['grid_view_without_thumbnail']['layout']['body']['quick_info']['acceptedWidgets'], 'affiliate_program_badge' );
        }

        if ( 'listings_card_list_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets']['affiliate_program_badge'] = $affiliate_program_widget;
            $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets']['affiliate_program_badge'] = $affiliate_program_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'affiliate_program_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'affiliate_program_badge' );
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'affiliate_program_badge' );
        }

    }

    return $fields;
}

/**
 * Affiliate Program Badge
 */

add_action( 'atbdp_all_listings_badge_template', 'affiliate_atbdp_all_listings_badge_template' );

function affiliate_atbdp_all_listings_badge_template( $field ) {

    switch ($field['widget_key']) {
        case 'affiliate_program_badge':

            $affiliate_program = get_post_meta(get_the_ID(), '_affiliate_program', true);

            if ( $affiliate_program == 'yes' ):
            ?>
                <span class="directorist-badge directorist-info-item directorist-badge-affiliate-program">Affiliate Program</span>
            <?php
            endif;

        break;
    } 

}

/**
 * Coupon Code Badge
 */

add_filter( 'atbdp_listing_type_settings_field_list', 'coupon_code_custom_atbdp_listing_type_settings_field_list' );

function coupon_code_custom_atbdp_listing_type_settings_field_list( $fields ) {

    foreach ( $fields as $key => $value ) {
        // setup widgets
        $coupon_code_widget = [
            'type'    => "badge",
            'id'      => "coupon_code_badge",
            'label'   => "Coupon Code",
            'icon'    => "uil uil-text-fields",
            'hook'    => "atbdp_coupon_code_badge",
            'options' => [],
        ];

        if ( 'listings_card_grid_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['grid_view_with_thumbnail']['widgets']['coupon_code_badge'] = $coupon_code_widget;
            $fields[$key]['card_templates']['grid_view_without_thumbnail']['widgets']['coupon_code_badge'] = $coupon_code_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['top_left']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_right']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['thumbnail']['bottom_left']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'coupon_code_badge' );

            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['right']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['grid_view_with_thumbnail']['layout']['footer']['left']['acceptedWidgets'], 'coupon_code_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['grid_view_without_thumbnail']['layout']['body']['quick_info']['acceptedWidgets'], 'coupon_code_badge' );
        }

        if ( 'listings_card_list_view' === $key ) {
            // register widget
            $fields[$key]['card_templates']['list_view_with_thumbnail']['widgets']['coupon_code_badge'] = $coupon_code_widget;
            $fields[$key]['card_templates']['list_view_without_thumbnail']['widgets']['coupon_code_badge'] = $coupon_code_widget;

            // grid with preview image
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['thumbnail']['top_right']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['list_view_with_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'coupon_code_badge' );

            // grid without preview image
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['top']['acceptedWidgets'], 'coupon_code_badge' );
            array_push( $fields[$key]['card_templates']['list_view_without_thumbnail']['layout']['body']['right']['acceptedWidgets'], 'coupon_code_badge' );
        }

    }

    return $fields;
}


add_action( 'atbdp_all_listings_badge_template', 'coupon_code_atbdp_all_listings_badge_template' );

function coupon_code_atbdp_all_listings_badge_template( $field ) {

    switch ($field['widget_key']) {
        case 'coupon_code_badge':

            $coupon_code = get_post_meta(get_the_ID(), '_coupon_code', true);

            if ( $coupon_code && !empty($coupon_code) ):
            ?>
                <span class="directorist-badge directorist-info-item directorist-badge-coupon-code">Coupon Code</span>
            <?php
            endif;

        break;
    } 

}

/**
 * Enable the Custom Field Meta Key
 */
add_filter('directorist_custom_field_meta_key_field_args', function ($args) {
	$args['type'] = 'text';
	return $args;
});