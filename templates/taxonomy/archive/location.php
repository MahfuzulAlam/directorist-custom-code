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

<div class="directorist-custom-taxonomy-wrapper directorist-location-archive-wrapper">
    <h1 class="directorist-custom-taxonomy-title directorist-location-archive-title">
        <?php single_term_title(); ?>
    </h1>

    <?php
    /**
     * Add custom content via hook
     */ 
    do_action( 'directorist_taxonomy_location_before_content' );
    ?>

    <div class="directorist-custom-taxonomy-description directorist-location-archive-description">
        
    <?php
        $location = get_query_var('at_biz_dir-location');
        echo do_shortcode( '[directorist_all_listing location='.$location.']' );
    ?>

    </div>

    <?php
    /**
     * Add custom content via hook
     */ 
    do_action( 'directorist_taxonomy_location_after_content' );
    ?>

</div>

<?php get_footer(); ?>