<?php

/**
 * Add your custom php code here
 */


add_action( 'atbdp_user_registration_completed', function ( $user_id ){
    $nid	=   isset( $_POST['nid'] ) && !empty( $_POST['nid'] ) ? sanitize_text_field( trim( $_POST['nid'] ) ) : '';
    if( $nid ) update_user_meta( $user_id, '_nid', $nid );
} );