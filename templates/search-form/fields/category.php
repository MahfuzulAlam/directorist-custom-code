<?php

    /**
     * @author  wpWax
     * @since   6.6
     * @version 7.3.1
     */

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    $searchform = new Directorist\Directorist_Listing_Search_Form( 'search_form', 2 );
    $selected_item = $searchform->get_selected_category_option_data();

    $parents = get_terms( [
        'taxonomy'   => ATBDP_CATEGORY,
        'hide_empty' => false,
        'parent'     => 0,
    ] );

    $children = [];
    $children_options = [];

    foreach ( $parents as $parent ) {
        $children_temrs = get_terms(
            [
                'taxonomy'   => ATBDP_CATEGORY,
                'parent'     => $parent->term_id,
                'hide_empty' => false,
            ]
        );

        if ( !empty( $children_temrs ) ) {
            $children[$parent->term_id] = $children_temrs;

            foreach ( $children_temrs as $children_term ) {
                $children_options[] = $children_term;
            }

        }

    }

?>
<div class="directorist-search-field">
    <div class="directorist-select directorist-search-category">
        <select name="in_cat_parent" class="" data-placeholder="Category" <?php echo !empty( $data['required'] ) ? 'required="required"' : ''; ?> data-isSearch="true" data-selected-id="<?php echo esc_attr( $selected_item['id'] ); ?>" data-selected-label="<?php echo esc_attr( $selected_item['label'] ); ?>">
            <?php
                echo '<option value="">' . esc_html__( 'Select Category', 'directorist' ) . '</option>';

                foreach ( $parents as $parent ) {
                ?>
                <option value="<?php echo $parent->term_id; ?>"><?php echo $parent->name ?></option>
            <?php
                }

            ?>
        </select>

    </div>
</div>

<div class="directorist-search-field directorist-search-sub-category">
    <div class="directorist-select directorist-search-category">
        <input type="hidden" id="category_children" value='<?php echo json_encode( $children ); ?>' />
        <select name="in_cat_child" class="" data-placeholder="Sub category" <?php echo !empty( $data['required'] ) ? 'required="required"' : ''; ?> data-isSearch="true" data-selected-id="<?php echo esc_attr( $selected_item['id'] ); ?>" data-selected-label="<?php echo esc_attr( $selected_item['label'] ); ?>">
            <?php
                echo '<option value="">' . esc_html__( 'Select Sub Category', 'directorist' ) . '</option>';

                foreach ( $children_options as $child_option ) {
                ?>
                <option value="<?php echo $child_option->term_id; ?>"><?php echo $child_option->name ?></option>
            <?php
                }

            ?>
        </select>

    </div>
</div>

<input type="hidden" name="in_cat" value="" />