<?php

/**
 * Add your custom php code here
 */


/**
 * reCaptcha 3
 */
 
 add_action("wp_head", function () {
    ?>
    <script src="https://www.google.com/recaptcha/api.js?render=6Ld82rUdAAAAAJORJeWIt6QwBIZfjtwmdwy98m8n"></script>
    <?php
});
 
add_action("wp_footer", function () {
    if (is_singular('at_biz_dir')) :
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $(".directorist-contact-owner-form button[type=submit]").on("click", function(e) {
                    e.preventDefault();
 
                    console.log('working here!');
 
                    var $this = $(this).parents('form.directorist-contact-owner-form');
 
                    grecaptcha.ready(function() {
                        try {
                                grecaptcha.execute('6Ld82rUdAAAAAJORJeWIt6QwBIZfjtwmdwy98m8n', {
                                action: 'submit'
                            }).then(function(token) {
                                
                                console.log("Recaptcha Validated!!!");

                                $this.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
 
                                var submit_button = $this.find('button[type="submit"]');
                                var status_area = $this.find('.directorist-contact-message-display'); // Show loading message
 
                                var msg = '<div class="directorist-alert"><i class="fas fa-circle-notch fa-spin"></i> ' + directorist.waiting_msg + ' </div>';
                                status_area.html(msg);
                                var name = $this.find('input[name="atbdp-contact-name"]');
                                var contact_email = $this.find('input[name="atbdp-contact-email"]');
                                var message = $this.find('textarea[name="atbdp-contact-message"]');
                                var post_id = $this.find('input[name="atbdp-post-id"]');
                                var listing_email = $this.find('input[name="atbdp-listing-email"]'); // Post via AJAX
 
                                var data = {
                                    'action': 'atbdp_public_send_contact_email',
                                    'post_id': post_id.val(),
                                    'name': name.val(),
                                    'email': contact_email.val(),
                                    'listing_email': listing_email.val(),
                                    'message': message.val(),
                                    'directorist_nonce': directorist.directorist_nonce
                                };
                                submit_button.prop('disabled', true);
                                $.post(directorist.ajaxurl, data, function(response) {
                                    submit_button.prop('disabled', false);
 
                                    if (1 == response.error) {
                                        atbdp_contact_submitted = false; // Show error message
 
                                        var msg = '<div class="atbdp-alert alert-danger-light"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>';
                                        status_area.html(msg);
                                    } else {
                                        name.val('');
                                        message.val('');
                                        contact_email.val(''); // Show success message
 
                                        var msg = '<div class="atbdp-alert alert-success-light"><i class="fas fa-check-circle"></i> ' + response.message + '</div>';
                                        status_area.html(msg);
                                    }
 
                                    setTimeout(function() {
                                        status_area.html('');
                                    }, 5000);
                                }, 'json');
 
                            });
                        } catch (exception) {
                            console.log(exception);
                            return false;
                        }
                    });
                });
            });
        </script>
    <?php
    endif;
});
 
/**
 * reCatpcha 3
 */
 
