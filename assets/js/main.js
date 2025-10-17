/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here
  $('body').on('click', '.directorist-mark-as-favorite__btn', function (e) {
    e.preventDefault();   // stops default browser behavior
    e.stopPropagation();  // stops bubbling up to parent elements
    alert('hello');
    return false;         // shorthand for both lines above
  });
});