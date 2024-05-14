<?php

/**
 * Add your custom php code here
 */

if( ! function_exists( 'directorist_get_custom_select_value_label' ) )
{
    function directorist_get_custom_select_value_label( $value, $options ) 
    {
        $result = "";
        if( $options ){
            foreach( $options as $option ) {
                $key = $option['option_value'];
                if( $key === $value ) {
                    $result = $option['option_label'];
                    break;
                }
            }
        }
        return $result;
    }
}

/**
 * Override validate function
 */

add_filter( 'atbdp_add_listing_form_validation_logic', function( $result, $props )
{
    $ss_field_list = [ 'custom-select', 'country-select' ];
    if( in_array( $props[ 'field_key' ], $ss_field_list ) ) return false;
    return $result;
}, 10, 2 );