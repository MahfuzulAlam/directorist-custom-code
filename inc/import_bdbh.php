<?php

/**
 * Import business hour data
 */

add_action( 'directorist_listing_imported', function( $post_id, $post ){

    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $bdbh_hours = [];
    $bdbh = [];

    $working_hours = isset( $post[ 'working_hours' ] ) && !empty( $post[ 'working_hours' ] ) ? json_decode( $post[ 'working_hours' ] ): [];

    if( $working_hours )
    {
        foreach( $working_hours as $day => $working_hour )
        {   
            $bdbh_hours[ strtolower( $day ) ] = $working_hour;
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

    //if( $bdbh ) file_put_contents( __DIR__ . '/data.json', json_encode( $bdbh ) );

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
            }elseif( $bdbh == 'Closed' ){
                return import_bdbh_empty_value();
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
        $hours = explode( '-', $bdbh );

        $format = get_bdbh_import_time_format();
         
        $start[] = isset($hours[0]) ? ( $format == '24' ? $hours[0] : bdbh_12_to_24_hour($hours[0]) ) : '';
        $close[] = isset($hours[1]) ? ( $format == '24' ? $hours[1] : bdbh_12_to_24_hour($hours[1]) ) : '';
        
        
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

if( !function_exists( 'get_bdbh_import_time_format' ) )
{
    function get_bdbh_import_time_format()
    {
        return get_directorist_option( 'atbh_import_time_format', '12' );
    }
}

if( !function_exists( 'bdbh_12_to_24_hour' ) )
{
    function bdbh_12_to_24_hour( $time )
    {
        if( $time )
        {
            $timestamp = strtotime($time);
            return date('H:i', $timestamp);
        }
        return '';
    }
}