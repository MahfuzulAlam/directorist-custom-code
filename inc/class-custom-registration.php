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
            default:
                $this->text_field( $value, $name );
                break;
        }
    }

    public function directorist_dashboard_user_custom_fields()
    {
        $value = get_user_meta( get_current_user_id(), '_'.$this->field_slug,  true);
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
            default:
                $this->text_field( $value, $name);
                break;
        }
    }

    public function user_profile_fields( $user )
    {
        ?>
            <tr>
                <th><label for="field-<?php echo $this->field_slug; ?>"><?php echo $this->field_name; ?></label></th>
                <td>
                    <?php
                        $value = get_user_meta( $user->ID, '_'.$this->field_slug,  true);
                
                        switch( $this->field_type )
                        {
                            case 'text':
                                ?>
                                    <input type="text" name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /><br />
                                <?php
                                break;
                            case 'textarea':
                                ?>
                                    <textarea name="<?php echo $this->field_slug; ?>" id="field-<?php echo $this->field_slug; ?>" rows="5" cols="30" autocomplete="chrome-off"><?php echo esc_attr( $value ); ?></textarea>
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

    public function atbdp_user_registration_completed( $user_id )
    {
        $value	=   isset( $_POST[$this->field_slug] ) && !empty( $_POST[$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST[$this->field_slug] ) ) : sanitize_text_field( trim( $_POST[$this->field_slug] ) ) ) : '';
        if( $value ) update_user_meta( $user_id, '_' . $this->field_slug, $value );
    }

    public function wp_update_user( $user_id )
    {
        if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update_user_profile' ):
            $value	=   isset( $_POST['user'][$this->field_slug] ) && !empty( $_POST['user'][$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST['user'][$this->field_slug] ) ) : sanitize_text_field( trim( $_POST['user'][$this->field_slug] ) ) ) : '';
            update_user_meta( $user_id, '_' . $this->field_slug, $value );
        endif; 
    }

    public function user_profile_options_update( $user_id )
    {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
    
        $value	=   isset( $_POST[$this->field_slug] ) && !empty( $_POST[$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST[$this->field_slug] ) ) : sanitize_text_field( trim( $_POST[$this->field_slug] ) ) ) : '';
        update_user_meta( $user_id, '_' . $this->field_slug, $value );
    }

}