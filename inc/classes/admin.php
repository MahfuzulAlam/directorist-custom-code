<?php

class Directorist_Listing_Stat_Admin
{
    public $total_count = 0;
    public $unique_count = 0;
    public $top_ten;

    public function __construct()
    {
        add_filter( 'atbdp_add_new_listing_column', [ $this, 'listing_admin_columns' ] );
        add_filter( 'atbdp_add_new_listing_column_content', [ $this, 'listing_admin_custom_columns' ], 10, 2 );
        add_filter( 'post_row_actions', [ $this, 'post_row_actions_statistics' ], 10, 2 );

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
        else if ( $column == 'listing_unique_views' )
        {
            echo $this->count_unique_listing_views( $post_id );
        }
    }

    public function post_row_actions_statistics( $actions, $post )
    {
        // Check if the post type is 'post'
        if ($post->post_type == 'at_biz_dir') {
            // Add your custom button
            $actions['listing_stat'] = '<a href="edit.php?post_type=at_biz_dir&page=directorist-statistics&listing_id='.$post->ID.'" class="my-custom-button-class" data-post-id="' . $post->ID . '">Statistics</a>';
        }
        return $actions;
    }

    public function count_total_views()
    {

        global $wpdb;

        $start_date = isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ]: '';
        $end_date = isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ]: '';

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        if( $start_date && $end_date ){
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE DATE(moment) >= %s AND DATE(moment) <= %s",
                $start_date,
                $end_date
            );
        }else{
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name"
            );
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) {
            $this->total_count = $count;
        }
    }

    public function count_unique_views()
    {
        global $wpdb;

        $start_date = isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ]: '';
        $end_date = isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ]: '';

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        if( $start_date && $end_date ){
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                1,
                $start_date,
                $end_date
            );
        }else{
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE new = %d",
                1
            );
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) {
            $this->unique_count = $count;
        }
    }

    public function count_total_listing_views( $listing = 0 )
    {
        if( ! $listing ) return 0;

        $start_date = isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ]: '';
        $end_date = isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ]: '';

        global $wpdb;

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        if( $start_date && $end_date ){
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                $listing,
                $start_date,
                $end_date
            );
        }else{
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE listing = %d",
                $listing
            );
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) return $count;

        return 0;
    }

    public function count_unique_listing_views( $listing = 0 )
    {
        if( ! $listing ) return 0;

        $start_date = isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ]: '';
        $end_date = isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ]: '';

        global $wpdb;

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        if( $start_date && $end_date ){
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                $listing,
                1,
                $start_date,
                $end_date
            );
        }else{
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d",
                $listing,
                1
            );
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) return $count;

        return 0;
    }

    public function top_ten_listings()
    {
        global $wpdb;

        $start_date = isset( $_POST[ 'start_date' ] ) && !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ]: '';
        $end_date = isset( $_POST[ 'end_date' ] ) && !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ]: '';

        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        if( $start_date && $end_date ){
            $query = "SELECT 
            listing, 
            COUNT(*) as total_count,
            SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
            FROM $table_name
            WHERE DATE(moment) >= '$start_date' AND DATE(moment) <= '$end_date'
            GROUP BY listing
            ORDER BY total_count DESC
            LIMIT 10";
        }else{
            $query = "SELECT 
            listing, 
            COUNT(*) as total_count,
            SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
            FROM $table_name
            GROUP BY listing
            ORDER BY total_count DESC
            LIMIT 10";
        }

        $results = $wpdb->get_results( $query );

        if ( $results ) {
            $this->top_ten = $results;
        }
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
        $listing_id = isset( $_GET['listing_id'] ) && !empty( $_GET['listing_id'] ) ? $_GET['listing_id'] : 0;

        if( $listing_id ){
            $data = [ 'total' => $this->count_total_listing_views( $listing_id ), 'unique' => $this->count_unique_listing_views( $listing_id ), 'listing_id' => $listing_id, 'listing_title' => get_the_title( $listing_id ) ];
            $this->get_admin_template( 'listing-statistics', $data );
        }else{
            $this->count_total_views();
            $this->count_unique_views();
            $this->top_ten_listings();
            $data = [ 'total' => $this->total_count, 'unique' => $this->unique_count, 'top_ten' => $this->top_ten ];
            $this->get_admin_template( 'submenu-statistics', $data );
        }

    }

    /**
     * Get Admin Template
     */
    public function get_admin_template( $template_file, $data = array() )
    {
        include DIRECTORIST_CUSTOM_CODE_STAT_DIR . '/templates/admin/' . $template_file . '.php';
    }

}

new Directorist_Listing_Stat_Admin();