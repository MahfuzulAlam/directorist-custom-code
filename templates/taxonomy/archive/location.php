<?php
/**
 * Template Name: Custom Location Taxonomy Archive
 * Description: A custom template for displaying a taxonomy archive page.
 *
 * @package Directorist_Refined_Taxonomy
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<div class="custom-taxonomy-wrapper container" style="padding: 40px 20px;">
    <?php
    // Display Single term title wrapped in a filter hook
    // This allows other plugins or themes to modify the title

    ?>
    <h1><?php single_term_title(); ?></h1>

    <?php
    global $wp_query;

    $location = get_query_var('at_biz_dir-location');
    echo do_shortcode( '[directorist_all_listing location='.$location.']' );
    ?>

    <?php
    /**
     * Add custom content via hook
     */ 
    do_action( 'directorist_taxonomy_location_content' );
    ?>

</div>

<?php get_footer(); ?>