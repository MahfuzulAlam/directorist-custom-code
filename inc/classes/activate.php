<?php


class Activate_Directorist_Statistics
{
	public function __construct()
    {
		flush_rewrite_rules();
        $this->create_database_listing();
        $this->create_database_taxonomy();
	}

    public function create_database_listing()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'directorist_listing_stats';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(255),
            dir_type BIGINT(20),
            listing BIGINT(20),
            user BIGINT(20),
            new TINYINT(1) NOT NULL,
            moment DATETIME,
            moment_gmt DATETIME,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_database_taxonomy()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'directorist_taxonomy_stats';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            source BIGINT(20),
            taxonomy TINYINT(20),
            term BIGINT(20),
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_database_search()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'directorist_search_stats';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(255),
            category BIGINT(20),
            location BIGINT(20),
            dir_type BIGINT(20),
            listing BIGINT(20),
            user BIGINT(20),
            new TINYINT(1) NOT NULL,
            moment DATETIME,
            moment_gmt DATETIME,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

new Activate_Directorist_Statistics();