/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {

  const $btn = $('.directorist__authentication__signup .directorist-authentication__form__btn');
  const $clone = $btn.clone(true); // Clone with events

  $btn.off('click').on('click', function (e) {
      e.preventDefault(); // Prevent default form submission
      $btn.addClass('directorist-btn-loading');

      // Run custom validation
      nidValidityCheck().then((shouldProceed) => {
          if (shouldProceed) {
              // Restore original behavior and trigger click
              $btn.replaceWith($clone);
              $clone.trigger('click');
          } else {
              $btn.removeClass('directorist-btn-loading');
          }
      });
  });

  function nidValidityCheck() {
      return new Promise((resolve) => {
          const nid = $('#directorist__national_id').val();

          if (!nid) {
              $('.directorist-register-error').empty().show().append('NID is required.');
              return resolve(false);
          }

          $.ajax({
              url: directorist.ajaxurl,
              type: 'POST',
              data: {
                  action: 'directorist_check_nid_availability',
                  nid: nid
              },
              cache: false
          }).done(function (response) {
              const { success, data } = response;

              if (!success) {
                  $('.directorist-register-error').empty().show().append(data.message);
                  resolve(false);
              } else {
                  resolve(true);
              }
          }).fail(function () {
              $('.directorist-register-error').empty().show().append('An error occurred. Please try again.');
              resolve(false);
          });
      });
  }

});

