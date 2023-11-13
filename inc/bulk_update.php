<?php

/**
 * Bulk Update
 *
 */

add_filter( 'bulk_edit_custom_box', 'directorist_custom_box_never_expire', 10, 2 );
function directorist_custom_box_never_expire( $column_name, $post_type ) {

    if (  ( 'atbdp_date' === $column_name ) && ( ATBDP_POST_TYPE == $post_type ) ) {
    ?>
        <fieldset class="inline-edit-col-right" style="margin-top: 0;">
            <div class="misc-pub-section">
                <div class="coupon_code">
                    <label><strong>Never Expires</strong></label>
                    <input type="text" name="coupon_code" value="" />
                </div>
                <div class="affiliate_program">
                    <label><strong>Affiliate Program</strong></label>
                    <input type="text" name="affiliate_program" value="" />
                </div>
                <div class="free_trial">
                    <label><strong>Expiry Date</strong></label>
                    <input type="text" name="free_trial" value="" />
                </div>
            </div>
        </fieldset>
<?php
    }
        ;
    }

    add_action( 'save_post', 'directorist_custom_box_never_expire_save' );
    function directorist_custom_box_never_expire_save( $post_id ) {

        if ( !is_admin() ) {
            return;
        }

        if ( get_post_type( $post_id ) !== ATBDP_POST_TYPE ) {
            return;
        }

    // if our current user can't edit this post, bail
        if ( !current_user_can( 'edit_posts' ) ) {
            return;
        }

    // Make sure that it is set.
        if ( isset( $_REQUEST['coupon_code'] ) && !empty( $_REQUEST['coupon_code'] ) ) {
            update_post_meta( $post_id, '_coupon_code', sanitize_text_field( $_REQUEST['coupon_code'] ) );
        }

        if ( isset( $_REQUEST['affiliate_program'] ) && !empty( $_REQUEST['affiliate_program'] ) ) {
            update_post_meta( $post_id, '_affiliate_program', sanitize_text_field( $_REQUEST['affiliate_program'] ) );
        }else{
            update_post_meta( $post_id, '_affiliate_program', 'yes' );
        }

        if ( isset( $_REQUEST['free_trial'] ) && !empty( $_REQUEST['free_trial'] ) ) {
            update_post_meta( $post_id, '_free_trial', sanitize_text_field( $_REQUEST['free_trial'] ) );
        }else{
            update_post_meta( $post_id, '_free_trial', 'yes' );
        }

    }

    add_action( 'admin_footer', function () {
    ?>
    <script>
        jQuery(document).ready(function($){
            $('.misc-pub-atbdp-never-expires input[name="never_expire"]').change(function(){
                if( $(this).is(":checked") ){
                    $('.misc-pub-atbdp-never-expires .atbdp_expiry_date').hide();
                    $('.misc-pub-atbdp-never-expires input[name="expiry_date"]').val('');
                }else{
                    $('.misc-pub-atbdp-never-expires .atbdp_expiry_date').show();
                }
            });
        });
    </script>
<?php
} );