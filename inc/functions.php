<?php

/**
 * Add your custom php code here
 */


// add_action(
//     'wp_footer',
//     function()
//     {
//         $bdbh = [
//             'monday' => [
//                 'enable' => 'enable',
//                 'start' => [
//                     '09:30'
//                 ],
//                 'close' => [
//                     '12:30'
//                 ]
//                 ],
//                 'tuesday' => [
//                     'enable' => 'enable',
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                     'remain_close' => 'open'
//                 ],
//                 'wendesday' => [
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                 ],
//                 'thursday' => [
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                 ],
//                 'friday' => [
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                 ],
//                 'saturday' => [
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                 ],
//                 'sunday' => [
//                     'start' => [
//                         ''
//                     ],
//                     'close' => [
//                         ''
//                     ],
//                 ]
//         ];
//         update_post_meta( 957, '_bdbh', $bdbh );
//     }
// );


add_action( 'directorist_listing_imported', function( $post_id, $post ){
    //file_put_contents(__DIR__.'/data.json', json_encode( $post ));
    $bdbh_hours = [];
    if( $post ){
        foreach( $post as $index=>$value )
        {
            if (strpos($index, 'bdbh_') !== false) {
                $bdbh_hours[substr($index, strpos($index, "bdbh_") + 5)] = $value;
            }
        }
    }
    if( count( $bdbh_hours ) > 0 ){
        
    }
    file_put_contents(__DIR__.'/data.json', json_encode( $bdbh_hours ));
}, 10, 2 );

if( !function_exists( 'import_bdbh_process_business_hour' ) )
{
    function import_bdbh_process_business_hour( $bdbh_hours )
    {

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
        
        return ['start'=>$start, 'close'=>$close];
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
            'remain_close' => 'open'
        ];
    }
}