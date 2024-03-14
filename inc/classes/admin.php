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

        if ( $column == 'listing_unique_views' )
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

        $stat_date_time = isset( $_POST[ 'stat_date_time' ] ) && !empty( $_POST[ 'stat_date_time' ] ) ? $_POST[ 'stat_date_time' ]: '';

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        switch( $stat_date_time ){
            case 'today';
                $today = date( 'Y-m-d' );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE(moment_gmt) = %s",
                    $today
                );
            break;
            case 'yesterday';
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE(moment_gmt) = %s",
                    $yesterday
                );
            break;
            case 'current_month';
                $current_month = date('Y-m');
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE_FORMAT(moment, '%Y-%m') = %s",
                    $current_month
                );
            break;
            case 'prev_month';
                // Calculate the starting date of the previous month
                $first_day_of_previous_month = date('Y-m-01', strtotime('first day of previous month'));
                // Calculate the ending date of the previous month
                $last_day_of_previous_month = date('Y-m-t', strtotime('last day of previous month'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE(moment) >= %s AND DATE(moment) <= %s",
                    $first_day_of_previous_month,
                    $last_day_of_previous_month
                );
            break;
            case 'cur_year';
                // Get the current year
                $current_year = date('Y');
                // Construct the start date of the current year
                $start_of_year = date('Y-01-01', strtotime($current_year));
                // Construct the end date of the current year
                $end_of_year = date('Y-12-31', strtotime($current_year));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE(moment) >= %s AND DATE(moment) <= %s",
                    $start_of_year,
                    $end_of_year
                );
            break;
            case 'prev_year';
                // Get the current year
                $current_year = date('Y');
                // Calculate the previous year
                $previous_year = $current_year - 1;
                // Construct the start date of the current year
                $start_of_year = date( 'Y-01-01', strtotime($previous_year . '-01-01') );
                // Construct the end date of the current year
                $end_of_year = date( 'Y-12-31', strtotime($previous_year . '-12-31') );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE DATE(moment) >= %s AND DATE(moment) <= %s",
                    $start_of_year,
                    $end_of_year
                );
            break;
            default:
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name"
                );
            break;
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) {
            $this->total_count = $count;
        }
    }

    public function count_unique_views()
    {
        global $wpdb;

        $stat_date_time = isset( $_POST[ 'stat_date_time' ] ) && !empty( $_POST[ 'stat_date_time' ] ) ? $_POST[ 'stat_date_time' ]: '';

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        switch( $stat_date_time ){
            case 'today';
                $today = date( 'Y-m-d' );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment_gmt) = %s",
                    1,
                    $today
                );
            break;
            case 'yesterday';
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment_gmt) = %s",
                    1,
                    $yesterday
                );
            break;
            case 'cur_month';
                $current_month = date('Y-m');
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE_FORMAT(moment, '%Y-%m') = %s",
                    1,
                    $current_month
                );
            break;
            case 'prev_month';
                // Calculate the starting date of the previous month
                $first_day_of_previous_month = date('Y-m-01', strtotime('first day of previous month'));
                // Calculate the ending date of the previous month
                $last_day_of_previous_month = date('Y-m-t', strtotime('last day of previous month'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    1,
                    $first_day_of_previous_month,
                    $last_day_of_previous_month
                );
            break;
            case 'cur_year';
                // Get the current year
                $current_year = date('Y');
                // Construct the start date of the current year
                $start_of_year = date('Y-01-01', strtotime($current_year));
                // Construct the end date of the current year
                $end_of_year = date('Y-12-31', strtotime($current_year));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    1,
                    $start_of_year,
                    $end_of_year
                );
            break;
            case 'prev_year';
                // Get the current year
                $current_year = date('Y');
                // Calculate the previous year
                $previous_year = $current_year - 1;
                // Construct the start date of the current year
                $start_of_year = date( 'Y-01-01', strtotime($previous_year . '-01-01') );
                // Construct the end date of the current year
                $end_of_year = date( 'Y-12-31', strtotime($previous_year . '-12-31') );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    1,
                    $start_of_year,
                    $end_of_year
                );
            break;
            default:
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE new = %d",
                    1
                );
            break;
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

        $stat_date_time = isset( $_POST[ 'stat_date_time' ] ) && !empty( $_POST[ 'stat_date_time' ] ) ? $_POST[ 'stat_date_time' ]: '';

        global $wpdb;

        // Your table name
        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        // Count the number of rows with the specified "listing" value
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d",
            $listing,
            1
        );

        switch( $stat_date_time ){
            case 'today';
                $today = date( 'Y-m-d' );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment_gmt) = %s",
                    $listing,
                    1,
                    $today
                );
            break;
            case 'yesterday';
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment_gmt) = %s",
                    $listing,
                    1,
                    $yesterday
                );
            break;
            case 'cur_month';
                $current_month = date('Y-m');
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE_FORMAT(moment, '%Y-%m') = %s",
                    $listing,
                    1,
                    $current_month
                );
            break;
            case 'prev_month';
                // Calculate the starting date of the previous month
                $first_day_of_previous_month = date('Y-m-01', strtotime('first day of previous month'));
                // Calculate the ending date of the previous month
                $last_day_of_previous_month = date('Y-m-t', strtotime('last day of previous month'));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    $listing,
                    1,
                    $first_day_of_previous_month,
                    $last_day_of_previous_month
                );
            break;
            case 'cur_year';
                // Get the current year
                $current_year = date('Y');
                // Construct the start date of the current year
                $start_of_year = date('Y-01-01', strtotime($current_year));
                // Construct the end date of the current year
                $end_of_year = date('Y-12-31', strtotime($current_year));
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    $listing,
                    1,
                    $start_of_year,
                    $end_of_year
                );
            break;
            case 'prev_year';
                // Get the current year
                $current_year = date('Y');
                // Calculate the previous year
                $previous_year = $current_year - 1;
                // Construct the start date of the current year
                $start_of_year = date( 'Y-01-01', strtotime($previous_year . '-01-01') );
                // Construct the end date of the current year
                $end_of_year = date( 'Y-12-31', strtotime($previous_year . '-12-31') );
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d AND DATE(moment) >= %s AND DATE(moment) <= %s",
                    $listing,
                    1,
                    $start_of_year,
                    $end_of_year
                );
            break;
            default:
                $query = $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE listing = %d AND new = %d",
                    $listing,
                    1
                );
            break;
        }

        $count = $wpdb->get_var($query);

        if( $count > 0 ) return $count;

        return 0;
    }

    public function top_ten_listings()
    {
        global $wpdb;

        $stat_date_time = isset( $_POST[ 'stat_date_time' ] ) && !empty( $_POST[ 'stat_date_time' ] ) ? $_POST[ 'stat_date_time' ]: '';

        $table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;

        $query = "SELECT 
        listing, 
        COUNT(*) as total_count,
        SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
        FROM $table_name
        GROUP BY listing
        ORDER BY total_count DESC
        LIMIT 10";

        switch( $stat_date_time ){
            case 'today';
                $today = date( 'Y-m-d' );
                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE(moment) = '". $today ."'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            case 'yesterday';
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE(moment) = '". $yesterday ."'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            case 'cur_month';
                $current_month = date('Y-m');
                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE_FORMAT(moment, '%Y-%m') = '". $current_month ."'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            case 'prev_month';
                // Calculate the starting date of the previous month
                $first_day_of_previous_month = date('Y-m-01', strtotime('first day of previous month'));
                // Calculate the ending date of the previous month
                $last_day_of_previous_month = date('Y-m-t', strtotime('last day of previous month'));

                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE(moment) >= '". $first_day_of_previous_month ."' AND DATE(moment) <= '". $last_day_of_previous_month ."'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            case 'cur_year';
                // Get the current year
                $current_year = date('Y');
                // Construct the start date of the current year
                $start_of_year = date('Y-01-01', strtotime($current_year));
                // Construct the end date of the current year
                $end_of_year = date('Y-12-31', strtotime($current_year));

                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE(moment) >= '". $start_of_year ."' AND DATE(moment) <= '" .$end_of_year. "'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            case 'prev_year';
                // Get the current year
                $current_year = date('Y');
                // Calculate the previous year
                $previous_year = $current_year - 1;
                // Construct the start date of the current year
                $start_of_year = date( 'Y-01-01', strtotime($previous_year . '-01-01') );
                // Construct the end date of the current year
                $end_of_year = date( 'Y-12-31', strtotime($previous_year . '-12-31') );

                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                WHERE DATE(moment) >= '". $start_of_year ."' AND DATE(moment) <= '". $end_of_year ."'
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
            default:
                $query = "SELECT 
                listing, 
                COUNT(*) as total_count,
                SUM(CASE WHEN new = 1 THEN 1 ELSE 0 END) as new_count
                FROM $table_name
                GROUP BY listing
                ORDER BY total_count DESC
                LIMIT 10";
            break;
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

        e_var_dump( $_POST );

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