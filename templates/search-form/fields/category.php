<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$data['placeholder'] = 'Select Category';

$selected_items = isset( $_GET['in_cats'] ) ? $_GET['in_cats'] : [];
// Get all categories hierarchically
$categories = get_terms( [
    'taxonomy' => ATBDP_CATEGORY,
    'hide_empty' => false,
    'parent' => 0,
] );

// Function to get hierarchical locations up to 3 levels
function get_hierarchical_categories( $parent_id = 0, $level = 0, $max_level = 2 ) {
    if ( $level > $max_level ) {
        return [];
    }
    
    $terms = get_terms( [
        'taxonomy' => ATBDP_CATEGORY,
        'hide_empty' => false,
        'parent' => $parent_id,
    ] );
    
    $result = [];
    
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $term->level = $level;
            $result[] = $term;
            
            // Get children recursively
            $children = get_hierarchical_categories( $term->term_id, $level + 1, $max_level );
            if ( ! empty( $children ) ) {
                $result = array_merge( $result, $children );
            }
        }
    }
    
    return $result;
}

$categories = get_hierarchical_categories();
?>
<div class="directorist-search-field directorist-form-group <?php echo esc_attr( $empty_label ); ?>">
    <div class="directorist-select directorist-search-category directorist-search-field__input">

        <?php if ( ! empty( $data['label'] ) ) : ?>
            <label class="directorist-search-field__label"><?php echo esc_attr( $data['label'] ); ?></label>
        <?php endif; ?>

        <select name="in_cats[]" class="<?php echo esc_attr( $searchform->category_class ); ?>" data-placeholder="<?php echo esc_attr( $data['placeholder'] ?? '' ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?> data-isSearch="true" multiple="multiple">
            <?php echo '<option value="">' . esc_html__( 'Select Category', 'directorist' ) . '</option>'; ?>
            <?php if( $categories): ?>
                <?php foreach( $categories as $category ){ ?>
                    <option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( in_array( $category->term_id, $selected_items ) ); ?>><?php echo ($category->level == 0) ? esc_attr( $category->name ) : str_repeat( '-', $category->level ) . ' ' . esc_attr( $category->name ); ?></option>
                <?php } ?>
            <?php endif; ?>   
        </select>
    </div>
    <div class="directorist-search-field__btn directorist-search-field__btn--clear">
        <?php directorist_icon( 'fas fa-times-circle' ); ?> 
    </div>
</div>