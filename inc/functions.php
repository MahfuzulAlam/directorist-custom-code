<?php

/**
 * Add your custom php code here
 */

/**
 * Shortcode: [directorist_advanced_category ...any directorist_category attrs...]
 *
 * Works on a Directorist category archive where the current term slug
 * is available via get_query_var('atbdp_category').
 */

add_shortcode('directorist_advanced_category', function ($atts = [], $content = null) {
    // Keep original attributes to pass through to [directorist_category]
    $orig_atts = is_array($atts) ? $atts : [];

    // Helper: build a safe shortcode attribute string
    $build_attr_str = function(array $a) {
        $pairs = [];
        foreach ($a as $k => $v) {
            if ($v === '' || $v === null) continue;
            $k = sanitize_key($k);
            if (is_array($v)) { $v = implode(',', array_map('sanitize_text_field', $v)); }
            $pairs[] = sprintf('%s="%s"', $k, esc_attr($v));
        }
        return implode(' ', $pairs);
    };

    // Get current category slug from query var (as you required)
    $current_slug = get_query_var('atbdp_category');
    if (!$current_slug) {
        // Not on a Directorist category archive → just render the base shortcode
        return do_shortcode('[directorist_category ' . $build_attr_str($orig_atts) . ']');
    }

    $parent = get_term_by('slug', $current_slug, ATBDP_CATEGORY);
    if (!$parent || is_wp_error($parent)) {
        return do_shortcode('[directorist_category ' . $build_attr_str($orig_atts) . ']');
    }

    // Gather child term slugs that actually have listings
    $child_terms = get_terms([
        'taxonomy'   => ATBDP_CATEGORY,
        'parent'     => (int) $parent->term_id,
        'hide_empty' => false, // we'll check via query
    ]);

    if (is_wp_error($child_terms) || empty($child_terms)) {
        // No children → fall back to normal category display
        return do_shortcode('[directorist_category ' . $build_attr_str($orig_atts) . ']');
    }

    $child_slugs_with_listings = [];
    foreach ($child_terms as $child) {
        $child_slugs_with_listings[] = $child->slug;
    }

    $child_slugs_with_listings = array_values(array_unique($child_slugs_with_listings));

    // If there are qualifying children, render [directorist_all_categories] with their slugs
    if (!empty($child_slugs_with_listings)) {
        $all_cats_attr = 'slug="' . esc_attr(implode(',', $child_slugs_with_listings)) . '"';
        return do_shortcode('[directorist_all_categories view="grid" ' . $all_cats_attr . ']');
    }

    // Otherwise, render the standard [directorist_category] with all original attributes
    return do_shortcode('[directorist_category ' . $build_attr_str($orig_atts) . ']');
});

add_filter('atbdp_all_categories_argument', function($args){
    if(is_page(get_directorist_option('single_category_page'))) unset($args['parent']);
    return $args;
});