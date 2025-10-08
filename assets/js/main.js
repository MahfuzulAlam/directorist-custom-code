/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  $(document).on('click', '.directorist-contact-popup', function(event){
    event.preventDefault();
    let title = $(this).data('title');
    let value = $(this).data('value');
    Swal.fire({
      title: title,
      text: value,
    });
  });
});
