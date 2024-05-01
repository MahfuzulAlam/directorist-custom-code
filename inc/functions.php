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