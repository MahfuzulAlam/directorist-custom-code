<?php

/**
 * Data access for stored Google reviews.
 *
 * Rows are returned in the same shape the Places API uses, so templates do not
 * need to know whether a review came from the API or from the database.
 *
 * @package Directorist_Google_Reviews
 * @since   3.0
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Reviews_Table')):

    class DGR_Reviews_Table
    {
        /**
         * Reviews stored per listing.
         */
        const STORE_LIMIT = 6;

        /**
         * How many reviews to keep for a listing.
         */
        public function store_limit()
        {
            $limit = (int) apply_filters('dgr_reviews_store_limit', self::STORE_LIMIT);

            return $limit > 0 ? $limit : self::STORE_LIMIT;
        }

        /**
         * Stored reviews for a listing, newest sync order preserved.
         *
         * @param  int $listing_id
         * @param  int $limit
         * @return array List of Places shaped review arrays.
         */
        public function get_reviews($listing_id, $limit = 0)
        {
            global $wpdb;

            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return [];
            }

            $limit = $limit > 0 ? (int) $limit : $this->store_limit();
            $table = DGR_Install::table_name();

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table} WHERE listing_id = %d ORDER BY sort_order ASC, id ASC LIMIT %d",
                    $listing_id,
                    $limit
                )
            );

            if (! $rows) {
                return [];
            }

            return array_map([$this, 'to_review_array'], $rows);
        }

        /**
         * Whether a listing has any stored reviews.
         */
        public function has_reviews($listing_id)
        {
            global $wpdb;

            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return false;
            }

            $table = DGR_Install::table_name();

            return (bool) $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM {$table} WHERE listing_id = %d LIMIT 1", $listing_id)
            );
        }

        /**
         * Replace every stored review for a listing.
         *
         * A full replace rather than an upsert: Google only ever exposes a
         * small rolling window of reviews, so anything no longer in that window
         * should disappear from the listing too.
         *
         * @param  int    $listing_id
         * @param  string $place_id
         * @param  array  $reviews Places API review entries.
         * @return int    Number of rows written.
         */
        public function replace_reviews($listing_id, $place_id, $reviews)
        {
            global $wpdb;

            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return 0;
            }

            $reviews = array_slice((array) $reviews, 0, $this->store_limit());

            $this->delete_reviews($listing_id);

            $table    = DGR_Install::table_name();
            $synced   = gmdate('Y-m-d H:i:s');
            $written  = 0;

            foreach (array_values($reviews) as $order => $review) {
                if (! is_array($review)) {
                    continue;
                }

                $inserted = $wpdb->insert(
                    $table,
                    [
                        'listing_id'        => $listing_id,
                        'place_id'          => (string) $place_id,
                        'review_key'        => $this->review_key($review),
                        'sort_order'        => (int) $order,
                        'author_name'       => isset($review['author_name']) ? (string) $review['author_name'] : '',
                        'author_url'        => isset($review['author_url']) ? (string) $review['author_url'] : '',
                        'profile_photo_url' => isset($review['profile_photo_url']) ? (string) $review['profile_photo_url'] : '',
                        'rating'            => isset($review['rating']) ? (float) $review['rating'] : 0,
                        'review_text'       => isset($review['text']) ? (string) $review['text'] : '',
                        'relative_time'     => isset($review['relative_time_description']) ? (string) $review['relative_time_description'] : '',
                        'review_time'       => isset($review['time']) ? (int) $review['time'] : 0,
                        'translated'        => ! empty($review['translated']) ? 1 : 0,
                        'synced_at'         => $synced,
                    ],
                    ['%d', '%s', '%s', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%d', '%s']
                );

                if ($inserted) {
                    $written++;
                }
            }

            return $written;
        }

        /**
         * Remove every stored review for a listing.
         */
        public function delete_reviews($listing_id)
        {
            global $wpdb;

            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return 0;
            }

            return (int) $wpdb->delete(DGR_Install::table_name(), ['listing_id' => $listing_id], ['%d']);
        }

        /**
         * Stable identifier for a review, used to keep the unique index honest.
         */
        protected function review_key($review)
        {
            $author = isset($review['author_name']) ? $review['author_name'] : '';
            $time   = isset($review['time']) ? $review['time'] : '';

            if ('' === $author && '' === $time) {
                return md5(wp_json_encode($review));
            }

            return substr(md5($author . '|' . $time), 0, 32);
        }

        /**
         * Map a database row onto the Places API review shape.
         */
        protected function to_review_array($row)
        {
            return [
                'author_name'               => $row->author_name,
                'author_url'                => $row->author_url,
                'profile_photo_url'         => $row->profile_photo_url,
                'rating'                    => (float) $row->rating,
                'text'                      => (string) $row->review_text,
                'relative_time_description' => $row->relative_time,
                'time'                      => (int) $row->review_time,
                'translated'                => (bool) $row->translated,
            ];
        }
    }

endif;
