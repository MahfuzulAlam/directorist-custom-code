<?php get_header(); ?>

<div class="custom-taxonomy-wrapper container" style="padding: 40px 20px;">
    <h1><?php single_term_title(); ?></h1>

    <?php
    global $wp_query;
    //e_var_dump($wp_query->query_vars);

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