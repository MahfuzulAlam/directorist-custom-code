<?php

/**
 * Uninstall cleanup for Directorist - Google Reviews.
 *
 * Everything the plugin stores is a re-fetchable cache of Google data, so it
 * is removed unconditionally: the reviews table, the per-listing sync meta,
 * the schema version option and any cached transients from 2.x releases.
 *
 * The `_google_place` meta is deliberately kept — it is the place each owner
 * selected on the Add Listing form, so a reinstall picks up where it left off.
 *
 * @package Directorist_Google_Reviews
 * @since   3.3
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove everything the plugin stored on the current site.
 */
function dgr_uninstall_site()
{
    global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dgr_google_reviews");

    delete_option('dgr_db_version');

    $meta_keys = [
        '_dgr_rating',
        '_dgr_reviews_total',
        '_dgr_place_url',
        '_dgr_place_id',
        '_dgr_synced_at',
        '_dgr_sync_failed_at',
    ];

    foreach ($meta_keys as $key) {
        delete_post_meta_by_key($key);
    }

    // Cached Places responses left behind by 2.x releases.
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '\_transient\_dgr\_place\_%'
            OR option_name LIKE '\_transient\_timeout\_dgr\_place\_%'"
    );
}

if (is_multisite()) {
    $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        dgr_uninstall_site();
        restore_current_blog();
    }
} else {
    dgr_uninstall_site();
}
