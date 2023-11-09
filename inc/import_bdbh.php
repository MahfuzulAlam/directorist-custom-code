<?php

/**
 * Import business hour data
 */



add_action( 'directorist_listing_imported', function( $post_id, $post ){

    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $bdbh_hours = [];
    $bdbh = [];

    if( $post ){
        foreach( $post as $index=>$value )
        {
            if (strpos($index, 'bdbh_') !== false) {
                $bdbh_hours[substr($index, strpos($index, "bdbh_") + 5)] = $value;
            }
        }
    }

    if( count( $bdbh_hours ) > 0 ){
        foreach( $days as $day ){
            $bdbh[ $day ] = import_bdbh_process_business_hour( $bdbh_hours, $day );
        }
    }

    if( !empty( $bdbh ) ) {
        update_post_meta( $post_id, '_bdbh', $bdbh );
    }

}, 10, 2 );

if( !function_exists( 'import_bdbh_process_business_hour' ) )
{
    function import_bdbh_process_business_hour( $bdbh_hours, $day )
    {
        if( isset( $bdbh_hours[ $day ] ) && !empty( $bdbh_hours[ $day ] ) ){
            return import_bdbh_get_business_hour( $bdbh_hours[ $day ] );
        }else{
            return import_bdbh_get_business_hour( '' );
        }
    }
}

if( !function_exists( 'import_bdbh_get_business_hour' ) )
{
    function import_bdbh_get_business_hour( $bdbh )
    {
        if( $bdbh ){
            if( $bdbh == '24' ){
                return import_bdbh_24_value();
            }else{
                return import_bdbh_process_hours( $bdbh );
            }
        }
        return import_bdbh_empty_value();
    }
}

if( !function_exists( 'import_bdbh_process_hours' ) )
{
    function import_bdbh_process_hours( $bdbh )
    {
        if( !$bdbh || empty( $bdbh ) ) return;
        $start = [];
        $close = [];
        $bdbh = str_replace(' ', '', $bdbh);
        $hours = explode( ',', $bdbh );
        
        foreach( $hours as $hour ){
            $hour = explode( '-', $hour );
            $start[] = isset($hour[0]) ? $hour[0]: '';
            $close[] = isset($hour[1]) ? $hour[1]: '';
        }
        
        return import_bdbh_exists_value( ['start'=>$start, 'close'=>$close] );
    }
}

if( !function_exists( 'import_bdbh_empty_value' ) )
{
    function import_bdbh_empty_value()
    {
        return [
            'start' => [''],
            'close' => [''],
        ];
    }
}

if( !function_exists( 'import_bdbh_24_value' ) )
{
    function import_bdbh_24_value()
    {
        return [
            'enable' => 'enable',
            'start' => [
                ''
            ],
            'close' => [
                ''
            ],
            'remain_close' => 'open'
        ];
    }
}

if( !function_exists( 'import_bdbh_exists_value' ) )
{
    function import_bdbh_exists_value( $business_hour )
    {
        return [
            'enable' => 'enable',
            'start' => $business_hour['start'],
            'close' => $business_hour['close'],
        ];
    }
}