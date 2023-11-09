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
        add_action( 'wp_update_user', [$this, 'wp_update_user'] );
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
            default:
                $this->text_field( $value, $name);
                break;
        }
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

    public function atbdp_user_registration_completed( $user_id )
    {
        $value	=   isset( $_POST[$this->field_slug] ) && !empty( $_POST[$this->field_slug] ) ? sanitize_text_field( trim( $_POST[$this->field_slug] ) ) : '';
        if( $value ) update_user_meta( $user_id, '_'.$this->field_slug, $value );
    }

    public function wp_update_user( $user_id )
    {
        $value	=   isset( $_POST['user'][$this->field_slug] ) && !empty( $_POST['user'][$this->field_slug] ) ? ( $this->field_type == 'textarea' ? sanitize_textarea_field( trim( $_POST['user'][$this->field_slug] ) ) : sanitize_text_field( trim( $_POST['user'][$this->field_slug] ) ) ) : '';
        if( $value ) update_user_meta( $user_id, '_'.$this->field_slug, $value );
    }

}