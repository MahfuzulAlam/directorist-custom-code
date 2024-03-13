<?php

class Directorist_Listing_Stat_Admin
{
    public function __construct()
    {
        add_filter( 'atbdp_add_new_listing_column', [ $this, 'listing_admin_columns' ] );
        add_filter( 'atbdp_add_new_listing_column_content', [ $this, 'listing_admin_custom_columns' ], 10, 2 );

        add_action( 'admin_menu', [ $this, 'statistics_report_admin_submenu_page' ] );
    }

    public function listing_admin_columns( $columns )
    {
        $columns['listing_total_views'] = 'Total Views';
        $columns['listing_unique_views'] = 'Unique Views';
        return $columns;
    }

    public function listing_admin_custom_columns( $column, $post_id )
    {
        if ( $column == 'listing_total_views' )
        {
            echo $this->count_total_listing_views( $post_id );
        }

        if ( $column == 'listing_unique_views' )
        {
            echo $this->count_unique_listing_views( $post_id );
        }
    }

    public function count_total_listing_views( $listing = 0 )
    {
        if( ! $listing ) return 0;

        global $wpdb;

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        // Count the number of rows with the specified "listing" value
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE listing = %d",
            $listing
        );

        $count = $wpdb->get_var($query);

        if( $count > 0 ) return $count;

        return 0;
    }

    public function count_unique_listing_views( $listing = 0 )
    {
        if( ! $listing ) return 0;

        global $wpdb;

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        // Count the number of rows with the specified "listing" value
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d",
            $listing,
            1
        );

        $count = $wpdb->get_var($query);

        if( $count > 0 ) return $count;

        return 0;
    }

    public function statistics_report_admin_submenu_page()
    {
        add_submenu_page(
            'edit.php?post_type=at_biz_dir',
            __('Statistics', 'directorist-custom-code-stats'),
            __('Statistics', 'directorist-custom-code-stats'),
            'manage_options',
            'directorist-statistics',
            [ $this, 'statistics_submenu_page' ]
        );
    }

    public function statistics_submenu_page()
    {
        include_once( DIRECTORIST_CUSTOM_CODE_STAT_DIR . 'inc/admin/submenu-statistics.php' );
    }

}

new Directorist_Listing_Stat_Admin();