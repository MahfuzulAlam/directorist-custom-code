<?php

/**
 * Keeps stored reviews and listing meta in step with Google.
 *
 * Reads always come from the database. Google is contacted only when a listing
 * has never been synced or its last sync is older than the refresh interval.
 *
 * @package Directorist_Google_Reviews
 * @since   3.0
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Sync')):

    class DGR_Sync
    {
        /**
         * Datetime (UTC) of the last successful sync.
         */
        const META_SYNCED_AT = '_dgr_synced_at';

        /**
         * Datetime (UTC) of the last failed attempt, used to back off.
         */
        const META_FAILED_AT = '_dgr_sync_failed_at';

        /**
         * Average Google rating for the listing.
         */
        const META_RATING = '_dgr_rating';

        /**
         * Total number of Google reviews for the listing.
         */
        const META_TOTAL = '_dgr_reviews_total';

        /**
         * Google Maps URL for the place.
         */
        const META_PLACE_URL = '_dgr_place_url';

        /**
         * The place the stored data actually belongs to.
         */
        const META_PLACE_ID = '_dgr_place_id';

        /**
         * How long stored data stays fresh.
         */
        const REFRESH_INTERVAL = 7 * DAY_IN_SECONDS;

        /**
         * How long to wait before retrying after a failed call.
         */
        const FAILURE_BACKOFF = 15 * MINUTE_IN_SECONDS;

        /**
         * @var DGR_Reviews_Table
         */
        protected $reviews;

        /**
         * @var DGR_Places_API
         */
        protected $api;

        public function __construct($reviews = null, $api = null)
        {
            $this->reviews = $reviews ? $reviews : new DGR_Reviews_Table();
            $this->api     = $api ? $api : new DGR_Places_API();
        }

        /**
         * Everything the single listing templates need for a listing.
         *
         * Refreshes from Google first when the data is stale, then always reads
         * the review list back out of the database.
         *
         * @param  int    $listing_id
         * @param  string $place_id
         * @return array  rating, user_ratings_total, url, reviews
         */
        public function get_listing_data($listing_id, $place_id = '')
        {
            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return $this->empty_data();
            }

            if ($place_id && $this->needs_refresh($listing_id, $place_id)) {
                $this->refresh($listing_id, $place_id);
            }

            return [
                'rating'             => (float) get_post_meta($listing_id, self::META_RATING, true),
                'user_ratings_total' => (int) get_post_meta($listing_id, self::META_TOTAL, true),
                'url'                => (string) get_post_meta($listing_id, self::META_PLACE_URL, true),
                'reviews'            => $this->reviews->get_reviews($listing_id),
            ];
        }

        /**
         * Whether a listing should be refreshed from Google.
         */
        public function needs_refresh($listing_id, $place_id = '')
        {
            $listing_id = (int) $listing_id;

            // Nothing stored yet — the first page view populates the table.
            if (! $this->reviews->has_reviews($listing_id)) {
                return ! $this->recently_failed($listing_id);
            }

            // The listing now points at a different Google place, so whatever
            // is stored belongs to the previous business and must not be shown.
            $stored_place = (string) get_post_meta($listing_id, self::META_PLACE_ID, true);

            if ($place_id && $stored_place && $stored_place !== (string) $place_id) {
                return ! $this->recently_failed($listing_id);
            }

            $synced_at = $this->meta_timestamp($listing_id, self::META_SYNCED_AT);

            if (! $synced_at) {
                return ! $this->recently_failed($listing_id);
            }

            $interval = (int) apply_filters('dgr_refresh_interval', self::REFRESH_INTERVAL, $listing_id);

            if ((time() - $synced_at) < $interval) {
                return false;
            }

            return ! $this->recently_failed($listing_id);
        }

        /**
         * Pull fresh data from Google and persist it.
         *
         * @return bool True when the listing was updated.
         */
        public function refresh($listing_id, $place_id)
        {
            $listing_id = (int) $listing_id;
            $place      = $this->api->fetch_place($place_id);

            if (null === $place) {
                // Record the failure so a Google outage cannot turn every page
                // view into another blocking request.
                update_post_meta($listing_id, self::META_FAILED_AT, gmdate('Y-m-d H:i:s'));

                return false;
            }

            $this->reviews->replace_reviews($listing_id, $place_id, $place['reviews']);

            update_post_meta($listing_id, self::META_RATING, (float) $place['rating']);
            update_post_meta($listing_id, self::META_TOTAL, (int) $place['user_ratings_total']);
            update_post_meta($listing_id, self::META_PLACE_URL, (string) $place['url']);
            update_post_meta($listing_id, self::META_PLACE_ID, (string) $place_id);
            update_post_meta($listing_id, self::META_SYNCED_AT, gmdate('Y-m-d H:i:s'));

            delete_post_meta($listing_id, self::META_FAILED_AT);

            do_action('dgr_listing_synced', $listing_id, $place_id, $place);

            return true;
        }

        /**
         * Drop everything stored for a listing.
         */
        public function purge($listing_id)
        {
            $listing_id = (int) $listing_id;

            $this->reviews->delete_reviews($listing_id);

            foreach ([self::META_SYNCED_AT, self::META_FAILED_AT, self::META_RATING, self::META_TOTAL, self::META_PLACE_URL, self::META_PLACE_ID] as $key) {
                delete_post_meta($listing_id, $key);
            }
        }

        /**
         * Whether a failed attempt happened inside the backoff window.
         */
        protected function recently_failed($listing_id)
        {
            $failed_at = $this->meta_timestamp($listing_id, self::META_FAILED_AT);

            if (! $failed_at) {
                return false;
            }

            return (time() - $failed_at) < self::FAILURE_BACKOFF;
        }

        /**
         * Read a stored UTC datetime as a unix timestamp.
         */
        protected function meta_timestamp($listing_id, $key)
        {
            $value = get_post_meta($listing_id, $key, true);

            if (! $value) {
                return 0;
            }

            // Stored as a UTC datetime string; numeric values are tolerated so
            // data written by older versions still reads correctly.
            $timestamp = is_numeric($value) ? (int) $value : strtotime($value . ' UTC');

            return $timestamp ? $timestamp : 0;
        }

        protected function empty_data()
        {
            return [
                'rating'             => 0,
                'user_ratings_total' => 0,
                'url'                => '',
                'reviews'            => [],
            ];
        }
    }

endif;
