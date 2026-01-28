<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$location_source = ! empty( $data['location_source'] ) && $data['location_source'] == 'from_map_api' ? 'map' : 'listing';

$data['placeholder'] = 'Select Location';

if ( $location_source == 'listing' ) {
    $selected_items = isset( $_GET['in_locs'] ) ? $_GET['in_locs'] : [];
    $seelcted_terms = [];
    // Get all locations hierarchically
    $locations = get_terms( [
        'taxonomy' => ATBDP_LOCATION,
        'hide_empty' => false,
        'parent' => 0,
    ] );
    
    // Function to get hierarchical locations up to 3 levels
    function get_hierarchical_locations( $parent_id = 0, $level = 0, $max_level = 2 ) {
        if ( $level > $max_level ) {
            return [];
        }
        
        $terms = get_terms( [
            'taxonomy' => ATBDP_LOCATION,
            'hide_empty' => false,
            'parent' => $parent_id,
        ] );
        
        $result = [];
        
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $term->level = $level;
                $result[] = $term;
                
                // Get children recursively
                $children = get_hierarchical_locations( $term->term_id, $level + 1, $max_level );
                if ( ! empty( $children ) ) {
                    $result = array_merge( $result, $children );
                }
            }
        }
        
        return $result;
    }
    
    $locations = get_hierarchical_locations();
    ?>

    <div class="directorist-search-field directorist-form-group <?php echo esc_attr( $empty_label ); ?>">
        <div class="directorist-select directorist-search-location directorist-search-field__input">

            <?php if ( ! empty( $data['label'] ) ) : ?>
                <label class="directorist-search-field__label"><?php echo esc_attr( $data['label'] ); ?></label>
            <?php endif; ?>

            <select name="in_locs[]" class="<?php echo esc_attr( $searchform->location_class ); ?>" data-placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?> data-isSearch="true" multiple="multiple">
                <?php if( $locations): ?>
                    <?php echo '<option value="">' . esc_html__( 'Select Location', 'directorist' ) . '</option>'; ?>
                    <?php foreach( $locations as $location ){ ?>
                        <option value="<?php echo esc_attr( $location->term_id ); ?>" <?php selected( in_array( $location->term_id, $selected_items ) ); ?>><?php echo ($location->level == 0) ? esc_attr( $location->name ) : str_repeat( '-', $location->level ) . ' ' . esc_attr( $location->name ); ?></option>
                    <?php } ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="directorist-search-field__btn directorist-search-field__btn--clear">
            <?php directorist_icon( 'fas fa-times-circle' ); ?> 
        </div>
    </div>

    <?php
} elseif ( $location_source == 'map' ) {
    $cityLat = isset( $_GET['cityLat'] ) ? sanitize_text_field( wp_unslash( $_GET['cityLat'] ) ) : '';
    $cityLng = isset( $_GET['cityLng'] ) ? sanitize_text_field( wp_unslash( $_GET['cityLng'] ) ) : '';
    $value   = isset( $_GET['address'] ) ? sanitize_text_field( wp_unslash( $_GET['address'] ) ) : '';
    ?>

    <div class="directorist-search-field directorist-form-group directorist-search-location directorist-icon-right <?php echo esc_attr( $empty_label ); ?>">
        <?php if ( ! empty( $data['label'] ) ) : ?>
            <label class="directorist-search-field__label" for="addressId"><?php echo esc_attr( $data['label'] ); ?></label>
        <?php endif; ?>
        <span class="directorist-input-icon directorist-filter-location-icon"><?php directorist_icon( 'fas fa-crosshairs' ); ?></span>
        <input type="text" name="address" id="addressId" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" autocomplete="off" class="directorist-form-element directorist-location-js location-name directorist-search-field__input" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?>>

        <div class="address_result location-names" style="display: none"></div>
        <input type="hidden" id="cityLat" name="cityLat" value="<?php echo esc_attr( $cityLat ); ?>" />
        <input type="hidden" id="cityLng" name="cityLng" value="<?php echo esc_attr( $cityLng ); ?>" />

        <div class="directorist-search-field__btn directorist-search-field__btn--clear">
            <?php directorist_icon( 'fas fa-times-circle' ); ?> 
        </div>
    </div>

    <?php
}