<?php

/**
 * @author  wpwax
 * @since   1.0
 * @version 1.0
 */

class Directorist_Custom_Registration_Field
{
    public $field_name;
    public $field_slug;
    public $field_type;
    public $field_required;

    public function __construct( $name, $slug, $type = 'text', $required = false )
    {
        $this->field_name = $name;
        $this->field_slug = $slug;
        $this->field_type = $type;
        $this->field_required = $required;
        $this->render();
    }

    public function render()
    {
        add_action( 'directorist_registration_form_custom_fields', [ $this, 'directorist_registration_form_custom_fields' ] );
        add_action( 'atbdp_user_registration_completed', [ $this, 'atbdp_user_registration_completed' ] );
        add_action( 'directorist_dashboard_user_custom_fields', [ $this, 'directorist_dashboard_user_custom_fields' ] );
        add_action( 'wp_update_user', [ $this, 'wp_update_user' ] );

        add_action( 'dcrf_user_admin_profile_fields', [ $this, 'user_profile_fields' ] );
        add_action( 'personal_options_update', [ $this ,'user_profile_options_update' ] );
        add_action( 'edit_user_profile_update', [ $this, 'user_profile_options_update' ] );
    }

    public function directorist_registration_form_custom_fields()
    {
        $value = isset( $_REQUEST[$this->field_slug]) ? esc_url( sanitize_text_field( wp_unslash( $_REQUEST[$this->field_slug] ) ) ) : '';
        $name = $this->field_slug;

        switch( $this->field_type )
        {
            case 'text':
                $this->text_field( $value, $name );
                break;
            case 'textarea':
                $this->texarea_field( $value, $name );
                break;
            case 'url':
                $this->url_field( $value, $name );
                break;
            case 'number':
                $this->number_field( $value, $name );
                break;
            case 'file':
                $this->upload_field( $value, $name );
                break;
            default:
                $this->text_field( $value, $name );
                break;
        }
    }

    public function directorist_dashboard_user_custom_fields()
    {
        if($this->field_slug == 'pro_pic') return;
        $value = get_user_meta( get_current_user_id(), $this->field_slug,  true);
        $name = 'user['.$this->field_slug.']';

        switch( $this->field_type )
        {
            case 'text':
                $this->text_field( $value, $name );
                break;
            case 'textarea':
                $this->texarea_field( $value, $name );
                break;
            case 'url':
                $this->url_field( $value, $name );
                break;
            case 'number':
                $this->number_field( $value, $name );
                break;
            case 'file':
                $this->upload_field( $value, $name );
                break;
            default:
                $this->text_field( $value, $name);
                break;
        }
    }

    public function user_profile_fields( $user ) {
    ?>
        <tr>
            <th><label for="field-<?php echo $this->field_slug; ?>"><?php echo $this->field_name; ?></label></th>
            <td>
                <?php
                    $value = get_user_meta( $user->ID, $this->field_slug, true );

                    switch( $this->field_type ) {
                        case 'text':
                            ?>
                                <input type="text" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /><br />
                            <?php
                            break;

                        case 'textarea':
                            ?>
                                <textarea name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" rows="5" cols="30"><?php echo esc_attr( $value ); ?></textarea>
                            <?php
                            break;

                        case 'url':
                            ?>
                                <input type="url" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /><br />
                            <?php
                            break;

                        case 'number':
                            ?>
                                <input type="number" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /><br />
                            <?php
                            break;

                        case 'file':
                            ?>
                                <input type="file" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" /><br />
                            <?php
                            break;

                        default:
                            ?>
                                <input type="text" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /><br />
                            <?php
                            break;
                    }
                ?>
            </td>
        </tr>
    <?php
}


    public function text_field( $value = '', $name = '' )
    {
        ?>
            <div class="directorist-form-group directorist-mb-15">
                <label for="<?php echo $this->field_slug; ?>">
                    <?php echo $this->field_name; ?>
                    <?php echo ( $this->field_required ? '<strong class="directorist-form-required">*</strong>' : '' ); ?>
                </label>
                <input id="<?php echo $this->field_slug; ?>" class="directorist-form-element" type="text" name="<?php echo $name; ?>" value="<?php echo  $value; ?>" <?php echo ( $this->field_required ? 'required' : '' ); ?> >
            </div>
        <?php
    }

    public function texarea_field( $value = '', $name = '' )
    {
        ?>
            <div class="directorist-form-group directorist-mb-15">
                <label for="<?php echo $this->field_slug; ?>">
                    <?php echo $this->field_name; ?>
                    <?php echo ( $this->field_required ? '<strong class="directorist-form-required">*</strong>' : '' ); ?>
                </label>
                <textarea id="<?php echo $this->field_slug; ?>" class="directorist-form-element" type="text" name="<?php echo $name; ?>" <?php echo ( $this->field_required ? 'required' : '' ); ?> ><?php echo $value; ?></textarea>
            </div>
        <?php
    }

    public function url_field( $value = '', $name = '' )
    {
        ?>
            <div class="directorist-form-group directorist-mb-15">
                <label for="<?php echo $this->field_slug; ?>">
                    <?php echo $this->field_name; ?>
                    <?php echo ( $this->field_required ? '<strong class="directorist-form-required">*</strong>' : '' ); ?>
                </label>
                <input id="<?php echo $this->field_slug; ?>" class="directorist-form-element" type="url" name="<?php echo $name; ?>" value="<?php echo  $value; ?>" <?php echo ( $this->field_required ? 'required' : '' ); ?> >
            </div>
        <?php
    }

    public function number_field( $value = '', $name = '' )
    {
        ?>
            <div class="directorist-form-group directorist-mb-15">
                <label for="<?php echo $this->field_slug; ?>">
                    <?php echo $this->field_name; ?>
                    <?php echo ( $this->field_required ? '<strong class="directorist-form-required">*</strong>' : '' ); ?>
                </label>
                <input id="<?php echo $this->field_slug; ?>" class="directorist-form-element" type="number" name="<?php echo $name; ?>" value="<?php echo  $value; ?>" <?php echo ( $this->field_required ? 'required' : '' ); ?> >
            </div>
        <?php
    }

    public function upload_field( $value = '', $name = '' )
    {
        ?>
            <div class="directorist-form-group directorist-mb-15">
                <label for="<?php echo $this->field_slug; ?>">
                    <?php echo $this->field_name; ?>
                    <?php echo ( $this->field_required ? '<strong class="directorist-form-required">*</strong>' : '' ); ?>
                </label>
                <input type="hidden" name="image-upload" id="image-upload">
                <input id="<?php echo $this->field_slug; ?>" class="directorist-form-element" type="file" name="<?php echo $name; ?>"  >
            </div>
        <?php
    }

    public function upload_image( $image_url )
    {
        $upload_dir = wp_upload_dir();

        $image_data = file_get_contents( $image_url );

        $filename = basename( $image_url );

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
        }
        else {
        $file = $upload_dir['basedir'] . '/' . $filename;
        }

        //file_put_contents( $file, $image_data );

        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

    public function atbdp_user_registration_completed( $user_id ) {
        // Handle normal text/textarea fields
        if ( isset( $_POST[ $this->field_slug ] ) && !empty( $_POST[ $this->field_slug ] ) && $this->field_type !== 'file' ) {
            $value = ( $this->field_type === 'textarea' ) 
                ? sanitize_textarea_field( trim( $_POST[ $this->field_slug ] ) ) 
                : sanitize_text_field( trim( $_POST[ $this->field_slug ] ) );
            
            if ( $value && ! empty( $value ) ) {
                update_user_meta( $user_id, $this->field_slug, $value );
            }
        }

        // Handle file uploads
        if ( $this->field_type === 'file' && isset( $_FILES[ $this->field_slug ] ) && ! empty( $_FILES[ $this->field_slug ]['name'] ) ) {

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Upload the file and create attachment
            $attachment_id = media_handle_upload( $this->field_slug, 0 );

            if ( ! is_wp_error( $attachment_id ) ) {
                // Save the attachment ID as user meta
                update_user_meta( $user_id, $this->field_slug, $attachment_id );
            }
        }
    }


    public function wp_update_user( $user_id )
    {
        //file_put_contents( __DIR__. '/data.json', json_encode( $_FILES ) );
        if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update_user_profile' ):
            $value	=   isset( $_POST['user'][$this->field_slug] ) && !empty( $_POST['user'][$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST['user'][$this->field_slug] ) ) : sanitize_text_field( trim( $_POST['user'][$this->field_slug] ) ) ) : '';
            update_user_meta( $user_id, $this->field_slug, $value );
        endif; 
    }

    public function user_profile_options_update( $user_id )
    {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
    
        $value	=   isset( $_POST[$this->field_slug] ) && !empty( $_POST[$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST[$this->field_slug] ) ) : sanitize_text_field( trim( $_POST[$this->field_slug] ) ) ) : '';
        update_user_meta( $user_id, $this->field_slug, $value );
    }

}