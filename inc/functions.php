<?php

/**
 * Add your custom php code here
 */


add_action( 'atbdp_user_registration_completed', function ( $user_id ){
    $nid	=   isset( $_POST['nid'] ) && !empty( $_POST['nid'] ) ? sanitize_text_field( trim( $_POST['nid'] ) ) : '';
    if( $nid ) update_user_meta( $user_id, '_nid', $nid );
} );

add_action( 'edit_user_profile', 'dcfr_user_nid_field' );
add_action( 'show_user_profile', 'dcfr_user_nid_field' );

if(  !function_exists('dcfr_user_nid_field') )
{
    function dcfr_user_nid_field( $user )
    {
        $nid = get_user_meta( $user->ID,'_nid', true );
?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="field-nid">Número de identificación (Cédula)</label>
                </th>
                <td>
                    <input type="text" name="nid" id="field-nid" value="<?php echo esc_attr( $nid ); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
<?php
    }
}

add_action( 'personal_options_update', 'dcrf_user_profile_nid_update' );
add_action( 'edit_user_profile_update', 'dcrf_user_profile_nid_update' );

if( !function_exists('dcrf_user_profile_nid_update') )
{
    function dcrf_user_profile_nid_update( $user_id )
    {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
        $value	=   isset( $_POST[ 'nid' ] ) && !empty( $_POST[ 'nid' ] ) ? sanitize_text_field( trim( $_POST[ 'nid' ] ) ) : '';
        update_user_meta( $user_id, '_nid', $value );
    }
}

/**
 * Check NID Availibility
 */
add_action( 'wp_ajax_nopriv_directorist_check_nid_availability', 'directorist_check_nid_availability' );

function directorist_check_nid_availability()
{
    if ( ! isset( $_POST['nid'] ) ) {
        wp_send_json_error( [ 'message' => 'No NID provided' ] );
    }

    $nid = sanitize_text_field( $_POST['nid'] );

    // Query users by meta key `_nid`
    $user_query = new WP_User_Query( [
        'meta_key'   => '_nid',
        'meta_value' => $nid,
        'number'     => 1,
        'count_total' => false,
        'fields' => 'ID',
    ] );

    if ( ! empty( $user_query->get_results() ) ) {
        wp_send_json_error( [ 'message' => 'NID already exists' ] );
    }

    wp_send_json_success();
}