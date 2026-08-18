<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$conditional_logic_attr   = $searchform->get_conditional_logic_attributes( $data );
$location_taxonomy        = directorist_custom_code_location_taxonomy();
$is_logged_in             = is_user_logged_in();
$profile_defaults         = $is_logged_in ? directorist_custom_code_get_profile_search_location_defaults( get_current_user_id() ) : array();
$profile_components       = directorist_custom_code_get_profile_search_request_components( $profile_defaults );
$location_state           = directorist_custom_code_get_search_location_state();
$has_explicit_location    = directorist_custom_code_has_explicit_search_location( $profile_defaults, $profile_components );
$profile_taxonomy_default = false;
$profile_address_default  = false;
$selected_item            = array(
    'id'    => '',
    'label' => '',
);
$cityLat                  = directorist_custom_code_get_request_scalar( 'cityLat' );
$cityLng                  = directorist_custom_code_get_request_scalar( 'cityLng' );
$value                    = directorist_custom_code_get_request_scalar( 'address' );
$requested_location       = directorist_custom_code_get_request_scalar( 'in_loc' );
$requested_location_by_id = directorist_custom_code_get_request_scalar( 'loc_id' );
$requested_location       = '' !== $requested_location ? $requested_location : $requested_location_by_id;
$requested_location_ids   = wp_parse_id_list( $requested_location );
$requested_location_id    = ! empty( $requested_location_ids ) ? (int) reset( $requested_location_ids ) : 0;
$archive_location_slug    = get_query_var( 'atbdp_location' );
$archive_location_slug    = is_scalar( $archive_location_slug ) ? sanitize_title( (string) $archive_location_slug ) : '';
$selected_location        = $requested_location_id ? get_term( $requested_location_id, $location_taxonomy ) : null;

if ( ! $selected_location && $archive_location_slug ) {
    $selected_location = get_term_by( 'slug', $archive_location_slug, $location_taxonomy );
}

if ( $selected_location && ! is_wp_error( $selected_location ) ) {
    $selected_item = array(
        'id'    => (int) $selected_location->term_id,
        'label' => directorist_custom_code_get_location_path( $selected_location ),
    );
}

if ( ! $has_explicit_location && $is_logged_in ) {
    if ( '' === $location_state ) {
        if ( ! empty( $profile_defaults['location_id'] ) ) {
            $profile_components[] = 'taxonomy';
        }

        if ( ! empty( $profile_defaults['address'] ) ) {
            $profile_components[] = 'address';
        }
    }

    if ( in_array( 'taxonomy', $profile_components, true ) ) {
        $profile_location = get_term( $profile_defaults['location_id'], $location_taxonomy );

        if ( $profile_location && ! is_wp_error( $profile_location ) ) {
            $selected_item = array(
                'id'    => (int) $profile_location->term_id,
                'label' => directorist_custom_code_get_location_path( $profile_location ),
            );
            $profile_taxonomy_default = true;
        }
    }

    if ( in_array( 'address', $profile_components, true ) ) {
        $value                   = $profile_defaults['address'];
        $cityLat                 = $profile_defaults['latitude'];
        $cityLng                 = $profile_defaults['longitude'];
        $profile_address_default = true;
    }
}

$location_state_value = '';
if ( $is_logged_in ) {
    if ( $profile_taxonomy_default || $profile_address_default ) {
        $active_components = array();

        if ( $profile_taxonomy_default ) {
            $active_components[] = 'taxonomy';
        }

        if ( $profile_address_default ) {
            $active_components[] = 'address';
        }

        $location_state_value = implode( ',', $active_components );
    } elseif ( $has_explicit_location ) {
        $location_state_value = in_array( $location_state, array( 'cleared', 'custom' ), true ) ? $location_state : 'custom';
    }
}

$profile_taxonomy_origin = $profile_taxonomy_default;
$profile_address_origin  = $profile_address_default;

$locations_fields = '';
if ( empty( $data['lazy_load'] ) ) {
    $locations_fields = (string) $searchform->locations_fields;

    if ( ! empty( $selected_item['id'] ) ) {
        $selected_id_pattern = '/<option\b[^>]*\svalue\s*=\s*(["\'])' . preg_quote( (string) $selected_item['id'], '/' ) . '\1[^>]*>/i';
        $selected_id_exists  = (bool) preg_match( $selected_id_pattern, $locations_fields );
        $locations_fields    = preg_replace_callback(
            '/<option\b[^>]*>/i',
            static function ( $matches ) use ( $selected_id_pattern ) {
                $option = preg_replace( "/\\sselected(?:\\s*=\\s*(?:\"selected\"|'selected'|selected))?(?=\\s|>)/i", '', $matches[0] );

                if ( preg_match( $selected_id_pattern, $option ) ) {
                    $option = rtrim( substr( $option, 0, -1 ) ) . ' selected="selected">';
                }

                return $option;
            },
            $locations_fields
        );

        if ( ! $selected_id_exists ) {
            $locations_fields .= sprintf(
                '<option value="%1$d" selected="selected">%2$s</option>',
                (int) $selected_item['id'],
                esc_html( $selected_item['label'] )
            );
        }
    }
}
?>

<div class="directorist-search-field directorist-form-group <?php echo esc_attr( $empty_label ); ?>"<?php echo $conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in get_conditional_logic_attributes() ?>>
    <div class="directorist-select directorist-search-location directorist-search-field__input">

        <?php if ( ! empty( $data['label'] ) ) : ?>
            <label class="directorist-search-field__label"><?php echo esc_attr( $data['label'] ); ?></label>
        <?php endif; ?>

        <select name="in_loc" class="<?php echo esc_attr( $searchform->location_class ); ?> dcc-search-location-taxonomy" data-placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" data-isSearch="true" data-selected-id="<?php echo esc_attr( $selected_item['id'] ); ?>" data-selected-label="<?php echo esc_attr( $selected_item['label'] ); ?>" data-dcc-profile-location-default="<?php echo esc_attr( $profile_taxonomy_origin ? 'taxonomy' : '' ); ?>" data-dcc-profile-location-value="<?php echo esc_attr( $profile_taxonomy_origin ? $selected_item['id'] : '' ); ?>">
            <?php
            echo '<option value="">' . esc_html__( 'Select Location', 'directorist' ) . '</option>';

            if ( empty( $data['lazy_load'] ) ) {
                echo directorist_kses( $locations_fields, 'form_input' );
            }

            ?>
        </select>
    </div>
    <div class="directorist-search-field__btn directorist-search-field__btn--clear">
        <?php directorist_icon( 'fas fa-times-circle' ); ?>
    </div>
</div>

<div class="directorist-search-field directorist-form-group directorist-search-location directorist-icon-right <?php echo esc_attr( $empty_label ); ?>"<?php echo $conditional_logic_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in get_conditional_logic_attributes() ?>>
    <span class="directorist-input-icon directorist-filter-location-icon"><?php directorist_icon( 'fas fa-crosshairs' ); ?></span>
    <input type="text" name="address" id="addressId" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" autocomplete="off" class="directorist-form-element directorist-location-js location-name directorist-search-field__input dcc-search-location-address" data-dcc-profile-location-default="<?php echo esc_attr( $profile_address_origin ? 'address' : '' ); ?>" data-dcc-profile-location-value="<?php echo esc_attr( $profile_address_origin ? $value : '' ); ?>" data-dcc-location-required="<?php echo esc_attr( ! empty( $data['required'] ) ? '1' : '0' ); ?>">

    <div class="address_result location-names" style="display: none"></div>
    <input type="hidden" id="cityLat" name="cityLat" value="<?php echo esc_attr( $cityLat ); ?>" class="directorist-form-element" />
    <input type="hidden" id="cityLng" name="cityLng" value="<?php echo esc_attr( $cityLng ); ?>" class="directorist-form-element" />

    <div class="directorist-search-field__btn directorist-search-field__btn--clear">
        <?php directorist_icon( 'fas fa-times-circle' ); ?>
    </div>
</div>

<?php if ( '' !== $location_state_value ) : ?>
    <input type="hidden" name="dcc_profile_location_state" value="<?php echo esc_attr( $location_state_value ); ?>" class="dcc-profile-location-state" />
<?php endif; ?>
