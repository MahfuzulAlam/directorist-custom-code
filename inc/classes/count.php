<?php

class Listing_View_Count
{
    public $table_name;
    public $taxonomy_table_name;
    public $ip_address;
    public $dir_type;
    public $categories;
    public $locations;
    public $listing;
    public $user;
    public $new;
    public $moment;
    public $moment_gmt;
    public $source;

    public function __construct()
    {
        add_action( 'wp', [ $this, 'init' ] );
	}

    public function init()
    {
        if( is_singular( 'at_biz_dir' ) ):
            $this->set_table_name();
            $this->set_variables();
            $this->is_same_day();
            $this->listing_view_count();
        endif;
    }

    public function set_table_name()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . DIRECTORIST_LISTING_STAT_TABLE;
        $this->taxonomy_table_name = $wpdb->prefix . DIRECTORIST_TAXONOMY_STAT_TABLE;
    }

    public function set_variables()
    {
        $this->set_ip_address();
        $this->set_listing();
        $this->set_dir_type();
        $this->set_categories();
        $this->set_locations();
        $this->set_user();
        $this->set_new();
        $this->set_time();
    }

    public function set_ip_address()
    {
        if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip_address = isset( $_SERVER['REMOTE_ADDR'] ) && !empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR']: '';
        }
    }

    public function set_dir_type()
    {
        $this->dir_type = get_post_meta( $this->listing, '_directory_type', true );
    }

    public function set_categories()
    {

    }

    public function set_locations()
    {
        
    }

    public function set_listing()
    {
        $this->listing = get_the_ID();
    }

    public function set_user()
    {
        $this->user = is_user_logged_in() ? get_current_user_id() : 0;
    }

    public function set_new()
    {
        $this->new = 1;
    }

    public function set_time()
    {
        $wpTimeZone = get_option('timezone_string');
        date_default_timezone_set($wpTimeZone);

        $this->moment = date('Y-m-d H:i:s');
        $this->moment_gmt = gmdate('Y-m-d H:i:s');
    }

    public function listing_view_count()
    {
        //file_put_contents( __DIR__. '/data.json', json_encode( [ $this ] ) );
        $this->insert_count_to_database();
    }

    public function is_same_day()
    {
        if( $this->user ) $this->is_user_same_day();
        if( $this->new ) $this->is_ip_same_day();
    }

    public function is_user_same_day()
    {
        global $wpdb;

        // Get the current date
        $current_date = current_time('Y-m-d');

        // Check if the user has an entry for the current date
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $this->table_name WHERE user = %d AND DATE(moment) = %s",
            $this->user,
            $current_date
        );

        $count = $wpdb->get_var($query);

        //file_put_contents( __DIR__. '/data.json', json_encode( $count ) );

        if ($count > 0) {
            $this->new = 0;
        }

    }

    public function is_ip_same_day()
    {
        global $wpdb;

        // Get the current date
        $current_date = current_time('Y-m-d');

        // Check if the user has an entry for the current date
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $this->table_name WHERE ip_address = %s AND DATE(moment) = %s",
            $this->ip_address,
            $current_date
        );

        $count = $wpdb->get_var($query);

        //file_put_contents( __DIR__. '/data.json', json_encode( $count ) );

        if ($count > 0) {
            $this->new = 0;
        }

    }

    public function insert_count_to_database()
    {
        $this->insert_listing_count_to_database();
        $this->insert_taxonomy_count_to_database();
        
    }

    public function insert_listing_count_to_database()
    {
        global $wpdb;
        // Insert data into the custom table
        $wpdb->insert(
            $this->table_name,
            array(
                'ip_address' => $this->ip_address,
                'dir_type' => $this->dir_type,
                'listing' => $this->listing,
                'user' => $this->user,
                'new' => $this->new,
                'moment' => $this->moment,
                'moment_gmt' => $this->moment_gmt,
            ),
            array(
                '%s', // 'ip_address' is a string
                '%d', // 'dir_type' is an integer
                '%d', // 'listing' is an integer
                '%d', // 'user' is an integer
                '%d', // 'new' is an integer
                '%s', // 'moment' is a string
                '%s', // 'moment_gmt' is a string
            )
        );

        //file_put_contents( __DIR__. '/data.json', json_encode( $wpdb->last_error ) );
        
    }

    public function process_taxonomy ()
    {

    }

    public function insert_taxonomy_count_to_database( $taxonomy = 0, $term = 0 )
    {

        if( ! $taxonomy || ! $term ) return;

        global $wpdb;

        // Insert data into the custom table
        $wpdb->insert(
            $this->taxonomy_table_name,
            array(
                'source' => $this->source,
                'taxonomy' => $taxonomy,
                'term' => $term,
            ),
            array(
                '%d', // 'source' is a integer
                '%d', // 'taxonomy' is an integer
                '%d', // 'term' is an integer
            )
        );

        //file_put_contents( __DIR__. '/data.json', json_encode( $wpdb->last_error ) );
        
    }
}

new Listing_View_Count();