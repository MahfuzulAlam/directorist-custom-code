/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {

  // Open the modal within the same listing holder
  $('body').on('click', '.directorist-btn-custom-modal-js', function (e) {
    e.preventDefault();
    var $holder = $(this).closest('.directorist-claim-listing-holder');
    $holder.find('.directorist-claim-listing-modal').addClass('directorist-show');
  });

  // Close modal (X button)
  $('body').on('click', '.directorist-modal-close-js', function (e) {
    e.preventDefault();
    $(this).closest('.directorist-modal-js').removeClass('directorist-show');
  });

  // Click outside modal to close (per modal instance)
  $('body').on('click', '.directorist-modal-js', function (e) {
    if (e.target === this) {
      $(this).removeClass('directorist-show');
    }
  });

  // Delegated submit for each form instance
  $('body').on('submit', '.directorist-claimer__form_custom', function (e) {
    e.preventDefault();

    var $form = $(this);
    var $modal = $form.closest('.directorist-modal-js');
    var $holder = $form.closest('.directorist-claim-listing-holder');

    // Optional: show loading state on submit button inside this form only
    var $submitBtn = $form.find('.directorist-modal__footer .directorist-btn');
    //$submitBtn.addClass('directorist-loader');

    // Collect values scoped to this form/holder
    var listing_type = $form.find('input[type="radio"][name=listing_type]:checked').val();
    var plan_id = $form.find('#directorist-claimer_plan').find(':selected').val();
    var active_elm = $form.find('.dpp-order-select-dropdown ul .active');

    var formData = new FormData();
    formData.append('action', 'dcl_submit_claim');

    var postId = $holder.find('#directorist__post-id').val() || $form.find('.directorist__custom-post-id').val();
    if (postId) {
      formData.append('post_id', postId);
    }

    var claimerName = $form.find('#directorist-claimer__name').val();
    if (claimerName) formData.append('claimer_name', claimerName);

    var claimerPhone = $form.find('#directorist-claimer__phone').val();
    if (claimerPhone) formData.append('claimer_phone', claimerPhone);

    var claimerDetails = $form.find('#directorist-claimer__details').val();
    if (claimerDetails) formData.append('claimer_details', claimerDetails);

    if (plan_id) formData.append('plan_id', plan_id);
    if (typeof dcl_main !== 'undefined' && dcl_main.nonce) formData.append('nonce', dcl_main.nonce);
    if (listing_type) formData.append('type', listing_type);
    if (active_elm.length) formData.append('order_id', active_elm.attr('data-value'));

    console.log(formData);

    $.ajax({
      method: 'POST',
      processData: false,
      contentType: false,
      url: (typeof dcl_main !== 'undefined' ? dcl_main.ajaxurl : (window.ajaxurl || '')),
      data: formData,
      success: function (response) {
        $submitBtn.removeClass('directorist-loader');
        if (response && response.take_payment) {
          window.location.href = response.checkout_url;
        } else {
          $form.find('#directorist-claimer__name').val('');
          $form.find('#directorist-claimer__phone').val('');
          $form.find('#directorist-claimer__details').val('');
          $form.find('#directorist-claimer__submit-notification').addClass('text-success').html(response && response.message ? response.message : 'Submitted');
          setTimeout(function () {
            $form.find('#directorist-claimer__submit-notification').html("");
          }, 5000);
        }
        if (response && response.error_msg) {
          $form.find('#directorist-claimer__warning-notification').addClass('text-warning').html(response.error_msg);
          setTimeout(function () {
            $form.find('#directorist-claimer__warning-notification').html("");
          }, 5000);
        }
      },
      error: function () {
        $submitBtn.removeClass('directorist-loader');
      }
    });
  });
});
