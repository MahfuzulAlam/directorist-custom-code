<?php

/**
 * Add your custom php code here
 */


add_action( 'wp_update_user', function( $user_id ){
    $value	=   isset( $_POST['user']['instagram'] ) && !empty( $_POST['user']['instagram'] ) ? sanitize_url( trim( $_POST['user']['instagram'] ) ) : '';
    update_user_meta( $user_id, 'atbdp_instagram', $value );
} );