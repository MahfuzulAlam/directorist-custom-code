<?php
/**
 * Template Name: Custom Category Taxonomy Archive
 * Description: A custom template for displaying a taxonomy archive page.
 *
 * @package Directorist_Refined_Taxonomy
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<div class="directorist-custom-taxonomy-wrapper directorist-category-archive-wrapper">
    <h1 class="directorist-custom-taxonomy-title directorist-category-archive-title">
        <?php single_term_title(); ?>
    </h1>

    <?php
    /**
     * Add custom content via hook
     */ 
    do_action( 'directorist_taxonomy_category_before_content' );
    ?>

    <div class="directorist-custom-taxonomy-description directorist-category-archive-description">
        
    <?php
        $category = get_query_var('at_biz_dir-category');
        echo do_shortcode( '[directorist_all_listing category='.$category.']' );
    ?>

    </div>

    <?php
    /**
     * Add custom content via hook
     */ 
    do_action( 'directorist_taxonomy_category_after_content' );
    ?>

</div>

<?php get_footer(); ?>