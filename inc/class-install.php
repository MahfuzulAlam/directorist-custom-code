<?php

/**
 * Schema installer for the Google Reviews store.
 *
 * @package Directorist_Google_Reviews
 * @since   3.0
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Install')):

    class DGR_Install
    {
        /**
         * Bump when the schema changes so dbDelta runs again.
         */
        const DB_VERSION = '1.0.0';

        /**
         * Option holding the installed schema version.
         */
        const VERSION_OPTION = 'dgr_db_version';

        /**
         * Fully qualified reviews table name.
         */
        public static function table_name()
        {
            global $wpdb;

            return $wpdb->prefix . 'dgr_google_reviews';
        }

        /**
         * Install the schema when it is missing or out of date.
         *
         * Runs on activation and on every load, so the table also appears for
         * installs that were already active when the plugin was updated.
         */
        public static function maybe_install()
        {
            if (self::DB_VERSION === get_option(self::VERSION_OPTION)) {
                return;
            }

            self::install();
        }

        /**
         * Create or upgrade the reviews table.
         */
        public static function install()
        {
            global $wpdb;

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $table   = self::table_name();
            $collate = $wpdb->get_charset_collate();

            // dbDelta is whitespace sensitive: one column per line, two spaces
            // before the PRIMARY KEY definition, and every key must be named.
            $sql = "CREATE TABLE {$table} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                listing_id bigint(20) unsigned NOT NULL DEFAULT 0,
                place_id varchar(191) NOT NULL DEFAULT '',
                review_key varchar(191) NOT NULL DEFAULT '',
                sort_order smallint(5) unsigned NOT NULL DEFAULT 0,
                author_name varchar(191) NOT NULL DEFAULT '',
                author_url varchar(255) NOT NULL DEFAULT '',
                profile_photo_url varchar(255) NOT NULL DEFAULT '',
                rating decimal(2,1) NOT NULL DEFAULT 0.0,
                review_text longtext NULL,
                relative_time varchar(100) NOT NULL DEFAULT '',
                review_time bigint(20) unsigned NOT NULL DEFAULT 0,
                translated tinyint(1) NOT NULL DEFAULT 0,
                synced_at datetime NULL DEFAULT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY listing_review (listing_id,review_key),
                KEY listing_id (listing_id),
                KEY place_id (place_id)
            ) {$collate};";

            dbDelta($sql);

            update_option(self::VERSION_OPTION, self::DB_VERSION);
        }

        /**
         * Whether the reviews table is present.
         */
        public static function table_exists()
        {
            global $wpdb;

            $table = self::table_name();

            return (bool) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        }
    }

endif;
