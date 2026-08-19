<?php

/**
 * Keeps stored data in step with the listing itself.
 *
 * Without this, a listing re-pointed at a different Google place would keep
 * serving the previous business's reviews until the refresh interval elapsed.
 *
 * @package Directorist_Google_Reviews
 * @since   3.1
 */

defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('DGR_Listing_Hooks')):

    class DGR_Listing_Hooks
    {
        /**
         * @var DGR_Sync
         */
        protected $sync;

        /**
         * Listing IDs whose place meta changed during this request.
         *
         * @var array
         */
        protected $pending = [];

        public function __construct($sync = null)
        {
            $this->sync = $sync ? $sync : new DGR_Sync();

            // The submission form may write the place as an add, an update, or
            // a delete followed by an add. Rather than guess, every touch is
            // flagged and the final stored value is read once at shutdown.
            add_action('added_post_meta', [$this, 'on_meta_touched'], 10, 3);
            add_action('updated_post_meta', [$this, 'on_meta_touched'], 10, 3);
            add_action('deleted_post_meta', [$this, 'on_meta_touched'], 10, 3);

            add_action('shutdown', [$this, 'process_pending'], 5);

            add_action('before_delete_post', [$this, 'on_listing_deleted']);
        }

        /**
         * Flag a listing whose Google place meta was written.
         *
         * @param int|array $meta_id
         * @param int       $object_id
         * @param string    $meta_key
         */
        public function on_meta_touched($meta_id, $object_id, $meta_key)
        {
            if (DGR_Custom_Field::PLACE_META !== $meta_key) {
                return;
            }

            $object_id = (int) $object_id;

            if ($object_id) {
                $this->pending[$object_id] = true;
            }
        }

        /**
         * Reconcile every flagged listing against its stored data.
         */
        public function process_pending()
        {
            if (! $this->pending) {
                return;
            }

            $listing_ids   = array_keys($this->pending);
            $this->pending = [];

            foreach ($listing_ids as $listing_id) {
                $this->reconcile($listing_id);
            }
        }

        /**
         * Bring one listing's stored data in line with its selected place.
         *
         * @return bool True when something was written.
         */
        public function reconcile($listing_id)
        {
            $listing_id = (int) $listing_id;

            if (! $listing_id) {
                return false;
            }

            $place_id = $this->stored_place_id($listing_id);

            // The place was cleared, so nothing stored belongs to this listing
            // any more.
            if (! $place_id) {
                $this->sync->purge($listing_id);

                return true;
            }

            // Same place as the data already on file — the refresh interval
            // governs freshness from here, no need to spend an API call.
            if ($place_id === (string) get_post_meta($listing_id, DGR_Sync::META_PLACE_ID, true)) {
                return false;
            }

            // Drop the old business's data first. If the API call then fails,
            // the listing shows nothing rather than the wrong place's reviews,
            // and the next page view retries.
            $this->sync->purge($listing_id);

            if (! apply_filters('dgr_sync_on_place_change', true, $listing_id, $place_id)) {
                return true;
            }

            return $this->sync->refresh($listing_id, $place_id);
        }

        /**
         * Clean up when a listing is deleted for good.
         */
        public function on_listing_deleted($post_id)
        {
            if (defined('ATBDP_POST_TYPE') && ATBDP_POST_TYPE !== get_post_type($post_id)) {
                return;
            }

            $this->sync->purge($post_id);
        }

        /**
         * Place ID currently selected on the listing.
         */
        protected function stored_place_id($listing_id)
        {
            $raw = get_post_meta($listing_id, DGR_Custom_Field::PLACE_META, true);

            if (! $raw) {
                return '';
            }

            $value = json_decode($raw);

            return ! empty($value->place_id) ? (string) $value->place_id : '';
        }
    }

endif;
