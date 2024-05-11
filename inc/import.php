<?php

class Directorist_Super_Select_Import
{
    public function __construct()
    {
        add_action( 'directorist_listing_imported', [ $this, 'process_super_select_field' ], 10, 2 );
    }

    public function process_super_select_field( $post_id, $post )
    {
        if( isset( $post ) && is_array( $post ) )
        {
            foreach( $post as $index => $value )
            {
                if( strpos( $index, 'ss_' ) !== false )
                {
                    $this->update_super_select_field_options( $index, $value );
                    $this->import_super_select_field( $post_id, $index, $value );
                }
            }
        }
    }

    public function import_super_select_field( $post_id, $key, $value )
    {
        update_post_meta( $post_id, "_" . substr($key, strpos($key, "ss_") + 3), $value );
    }

    public function update_super_select_field_options( $key, $value )
    {
        $options = get_option( substr($key, strpos($key, "ss_") + 3) . '_options' );
        
        if( ! $this->option_exists( $options, $value ) )
        {
            $options[] = [
                'option_label'  => $value,
                'option_value'  => $value
            ];
            update_option( substr($key, strpos($key, "ss_") + 3)  . '_options', $options );
        }
    }

    public function option_exists( $options, $value )
    {
        if( $options && count( $options ) > 0 ){
            foreach( $options as $option )
            {
                if( $option[ 'option_label' ] == $value )
                {
                    return true;
                }
            }
        }
        return false;
    }
}

new Directorist_Super_Select_Import();