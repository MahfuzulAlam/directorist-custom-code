<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 8.6
 */

$placeholder = $data['placeholder'] ?? '';
$data_max    = $data['max_location_creation'] ?? '';
$data_new    = $data['create_new_loc'] ?? '';
$multiple    = $data['type'] === 'multiple' ? 'multiple' : '';
$lazy_load   = $data['lazy_load'];

$current_terms            = $listing_form->add_listing_terms( ATBDP_LOCATION );
$listing_location_ids     = $listing_form->add_listing_term_ids( ATBDP_LOCATION );
$location_ids_for_options = $listing_location_ids;

if ( empty( $current_terms ) && is_user_logged_in() ) {
    $default_raw = get_user_meta( get_current_user_id(), 'default_locations', true );
    if ( ! is_array( $default_raw ) ) {
        $default_raw = ! empty( $default_raw ) ? explode( ',', (string) $default_raw ) : array();
    }
    $default_ids = array_values( array_filter( array_map( 'absint', $default_raw ) ) );

    if ( '' !== $data_max ) {
        $max_n = absint( $data_max );
        if ( $max_n > 0 ) {
            $default_ids = array_slice( $default_ids, 0, $max_n );
        }
    }

    $field_type = isset( $data['type'] ) ? $data['type'] : '';
    if ( 'multiple' !== $field_type && ! empty( $default_ids ) ) {
        $default_ids = array_slice( $default_ids, 0, 1 );
    }

    $default_terms = array();
    foreach ( $default_ids as $term_id ) {
        $term = get_term( $term_id, ATBDP_LOCATION );
        if ( $term && ! is_wp_error( $term ) ) {
            $default_terms[] = $term;
        }
    }

    if ( ! empty( $default_terms ) ) {
        $current_terms            = $default_terms;
        $location_ids_for_options = array_map(
            static function ( $term ) {
                return (int) $term->term_id;
            },
            $default_terms
        );
    }
}

$current_ids = array_map(
    function( $term ) {
        return $term->term_id;
    }, $current_terms 
);

$current_labels = array_map(
    function( $term ) {
        return $term->name;
    }, $current_terms 
);

$current_ids_as_string    = implode( ',', $current_ids );
$current_labels_as_string = implode( ',', $current_labels );

// Get conditional logic attributes using centralized method
$conditional_logic_attr = $listing_form->get_conditional_logic_attributes( $data );
?>

<div class="directorist-form-group directorist-form-location-field"<?php echo $conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in get_conditional_logic_attributes() ?>>

    <?php $listing_form->field_label_template( $data ); ?>

    <select name="<?php echo esc_attr( $data['field_key'] ); ?>" class="directorist-form-element" id="at_biz_dir-location" data-selected-id="<?php echo esc_attr( $current_ids_as_string ) ?>" data-selected-label="<?php echo esc_attr( $current_labels_as_string ) ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-max="<?php echo esc_attr( $data_max ); ?>" data-allow_new="<?php echo esc_attr( $data_new ); ?>" <?php echo esc_attr( $multiple ); ?> <?php $listing_form->required( $data ); ?>>

        <?php
        if ( $data['type'] !== 'multiple' ) {
            echo '<option value="">' . esc_attr( $placeholder ) . '</option>';
        }
        if ( ! $lazy_load ) {
            $query_args = array(
                'parent'             => 0,
                'term_id'            => 0,
                'hide_empty'         => 0,
                'orderby'            => 'name',
                'order'              => 'asc',
                'show_count'         => 0,
                'single_only'        => 0,
                'pad_counts'         => true,
                'immediate_category' => 0,
                'active_term_id'     => 0,
                'ancestors'          => array(),
            );
            $locations_field = add_listing_category_location_filter( $listing_form->get_current_listing_type(), $query_args, ATBDP_LOCATION, $location_ids_for_options );
            echo directorist_kses( $locations_field, 'form_input' );
        }
        ?>

    </select>

    <?php $listing_form->field_description_template( $data ); ?>

</div>
