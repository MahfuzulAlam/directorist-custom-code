<?php

/**
 * Add your custom php code here
 */

 add_action('atbdp_listing_expired', function($listing_id) {
	// Change pricing plan
	$free_plan_id = 45; // Please use the free plan ID here
	update_post_meta($listing_id, '_fm_plans', $free_plan_id);
	update_post_meta($listing_id, '_fm_plans_by_admin', 1);
	
	// Set the listing to never expire
	update_post_meta($listing_id, '_never_expire', 1);
	
	// Publish the listing
	wp_update_post(array(
		'ID' => $listing_id,
		'post_status' => 'publish',
		'meta_input' => [
		'_listing_status' => 'post_status',
		],
	));
});