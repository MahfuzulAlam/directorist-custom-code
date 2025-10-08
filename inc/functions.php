<?php

/**
 * Add your custom php code here
 */


add_action('wp_head', function(){
    if( ! gluvega_is_paid_user() ):
    ?>
    <style>
        /* .single-at_biz_dir
        .directorist-action-save-wrap,
        .directorist-mark-as-favorite__btn,
        .directorist-added-to-favorite, */
        .directorist-search-field-paid-only
        {
            display: none !important;
        }
    </style>
    <?php
    endif;
});


add_action( 'wp_footer', function(){
    if( ! gluvega_is_paid_user() ):
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('body').on('click', '.directorist-mark-as-favorite__btn', function (event) {
                event.preventDefault();
                window.location.href = 'https://gluvega.com/log-in/';
            });
        });
    </script>
    <?php
    endif;
} );


if( ! function_exists('gluvega_is_paid_user') ){
    function gluvega_is_paid_user($user_id = 0){
        // get orders list post type = atbdp_orders active status payment_status completed
        // post_status publish

        $paid_pricing_plans = gluvega_get_paid_pricing_plans();

        if( empty($paid_pricing_plans) ){
            return false;
        }

        $user_id = $user_id ? $user_id : get_current_user_id();
        if ( empty( $user_id ) ) {
            return [];
        }

        $orders = gluvega_get_paid_orders( $user_id, $paid_pricing_plans );

        if ( $orders->have_posts() ) {
            foreach($orders->posts as $order){
                // get pricing plan id from the order meta _fm_plan_ordered
                $pricing_plan_id = get_post_meta($order, '_fm_plan_ordered', true);
                
                // check the period and find the exired date _recurrence_period_term fm_length[month, year, day]
                $period = get_post_meta($pricing_plan_id, '_recurrence_period_term', true);
                $length = get_post_meta($pricing_plan_id, 'fm_length', true); //month, year, day - convert to days
                $days = gluvega_convert_period_to_days($period, $length);
                
                // Get order submit date. Its the publish date
                $order_date = get_the_date('Y-m-d', $order);
                $expired_date = date('Y-m-d', strtotime($order_date . ' + ' . $days . ' days'));

                // check if the expired date is greater than the current date
                if($expired_date > date('Y-m-d')){
                    return true;
                }

            }
        }

        return false;
    }
}

function gluvega_convert_period_to_days($period, $length){
    switch($period){
        case 'month':
            return $length * 30;
        case 'year':
            return $length * 365;
        case 'week':
            return $length * 7;
        case 'day':
            return $length;
        default:
            return $length;
    }
    return $length;
}

function gluvega_get_paid_orders( $user_id = 0, $paid_pricing_plans = [] ){
    $args = [
        'post_type'      => 'atbdp_orders',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'author'         => $user_id,
        'fields'         => 'ids',
    ];

    $meta = [];

    $meta['plan_status'] = [
        'relation' => 'AND',
        [
            'key'     => '_fm_plan_ordered',
            'value'   => $paid_pricing_plans,
            'compare' => 'IN',
        ],
        [
            'key'     => '_payment_status',
            'value'   => 'completed',
            'compare' => '=',
        ],
    ];

    $meta['order_status'] = [
        'relation' => 'OR',
        [
            'key'     => '_order_status',
            'value'   => 'exit',
            'compare' => '!=',
        ],
        [
            'key'     => '_order_status',
            'compare' => 'NOT EXISTS',
        ],
    ];

    $metas = count( $meta );
    if ( $metas ) {
        $args['meta_query'] = ( $metas > 1 ) ? array_merge( ['relation' => 'AND'], $meta ) : $meta;
    }

    return new WP_Query( $args );
}

// Get the Pricing Plans that are paid, you can check by meta_key fm_price and its not 0 or empty
function gluvega_get_paid_pricing_plans(){
    $args = [
        'post_type' => 'atbdp_pricing_plans',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'fm_price',
                'value' => 0,
                'compare' => '>',
            ],
        ],
        'fields' => 'ids',
    ];
    $atbdp_query = new WP_Query( $args );
    if ( $atbdp_query->have_posts() ) {
        return $atbdp_query->posts;
    } else {
        return [];
    }
}


add_filter('directorist_search_form_widgets', function( $widgets ){
    foreach( $widgets[ 'available_widgets' ][ 'widgets' ] as $key => $widget ){
        $widgets[ 'available_widgets' ][ 'widgets' ][ $key ][ 'options' ]['class'] = [
            'type'  => 'text',
            'label' => 'Class',
            'value' => ''
        ];
    }
    return $widgets;
});