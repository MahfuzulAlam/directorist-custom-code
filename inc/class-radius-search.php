<?php

/**
 * Class Multi_Location_Radius_Search
 * 
 * Radius search for JSON encoded addresses stored in post meta (_multilocation)
 * 
 * @package     Directorist_Google_Reviews
 * @since       1.0
 */

// Exit if accessed directly.
defined('ABSPATH') || die('Direct access is not allowed.');

if (! class_exists('Multi_Location_Radius_Search')):
    class Multi_Location_Radius_Search
    {
        protected $lat;
        protected $lng;
        protected $min_distance;
        protected $max_distance;
        protected $units;
        protected $meta_key;

        public function __construct()
        {
            add_filter('atbdp_listing_search_query_argument', [ $this, 'multiple_radius_search_processor' ]);
        }

        public function multiple_radius_search_processor($args)
        {
            if (! isset($args['atbdp_geo_query']) || empty($args['atbdp_geo_query'])) return $args;

            $default_posts = $this->default_query_results($args);
            $multi_zip_search_posts = $this->multiple_radius_search_results($args);

            $all_post_ids = array_merge($default_posts, $multi_zip_search_posts);

            if (!empty($all_post_ids)) {
                unset($args['atbdp_geo_query']);
                $args['post__in'] = $all_post_ids;
            }

            return $args;
        }

        public function default_query_results($args = [])
        {
            $args['fields'] = 'ids';
            $args['posts_per_page'] = -1;
            $posts = new WP_Query($args);
            return !empty($posts->have_posts()) ? $posts->posts : [];
        }

        public function multiple_radius_search_results($args = [])
        {
            $args['fields'] = 'ids';
            $args['posts_per_page'] = -1;
            $this->set_geo_query_parameters($args['atbdp_geo_query']);
            unset($args['atbdp_geo_query']);
            return $this->get_zip_search_posts($args);
        }

        public function set_geo_query_parameters($geo_query)
        {
            $this->lat = isset($geo_query['latitude']) ? (float) $geo_query['latitude'] : 0;
            $this->lng          = isset($geo_query['longitude']) ? (float) $geo_query['longitude'] : 0;
            $this->min_distance = isset($geo_query['min_distance']) ? (float) $geo_query['min_distance'] : 0;
            $this->max_distance = isset($geo_query['max_distance']) ? (float) $geo_query['max_distance'] : 0;
            $this->units        = isset($geo_query['units']) && strtolower($geo_query['units']) === 'miles' ? 'miles' : 'kms';
            $this->meta_key     = "_multilocation";
        }

        /**
         * Run query and filter posts within radius
         */
        public function get_zip_search_posts($args = [])
        {
            $defaults = [
                'post_type'    => 'at_biz_dir', // change to your CPT
                'post_status'  => 'publish',
                'posts_per_page' => 6,
            ];

            $query_args = wp_parse_args($args, $defaults);
            $query      = new WP_Query($query_args);

            $filtered = [];
            foreach ($query->posts as $post) {
                $addresses = get_post_meta($post, $this->meta_key, true);
                $addresses = is_string($addresses) ? json_decode($addresses, true) : $addresses;
                
                if (! empty($addresses)) {
                    $distance = $this->get_closest_distance($addresses);
                    if ($distance !== false && $distance >= $this->min_distance && $distance <= $this->max_distance) {
                        $filtered[] = $post;
                    }
                }
            }

            // Sort posts by nearest distance
            usort($filtered, function ($a, $b) {
                return $a->distance <=> $b->distance;
            });

            return $filtered;
        }

        /**
         * Find the closest distance among multiple addresses
         */
        protected function get_closest_distance($addresses)
        {
            $closest = false;

            foreach ($addresses as $address) {
                if (isset($address['latitude'], $address['longitude'])) {
                    $distance = $this->haversine_distance(
                        $this->lat,
                        $this->lng,
                        (float) $address['latitude'],
                        (float) $address['longitude']
                    );

                    if ($closest === false || $distance < $closest) {
                        $closest = $distance;
                    }
                }
            }

            return $closest;
        }

        /**
         * Haversine formula: distance in KM or Miles
         */
        protected function haversine_distance($lat1, $lon1, $lat2, $lon2)
        {
            $earth_radius = ($this->units === 'miles') ? 3959 : 6371; // miles or km
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earth_radius * $c;
        }
    }

    new Multi_Location_Radius_Search();

endif;
