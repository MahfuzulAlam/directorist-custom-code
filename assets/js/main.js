/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here

    $('select[name="in_cat_parent"]').on("change", function () {
      var parent_value = $(this).val();

      var category_children = $("#category_children").val();

      if (category_children) {
        category_children = JSON.parse(category_children);
        category_children = category_children[parent_value];
      }

      // Get a reference to the Select2 element
      var catChild = $('select[name="in_cat_child"]');

      // Clear existing options from the Select2 element
      catChild.empty();

      // Create new options
      var newOptions = [];
      if (category_children) {
        category_children.forEach((child) => {
          newOptions.push({
            id: child.term_id,
            text: child.name,
          });
        });
        $('.directorist-search-sub-category').show();
      }else{
        $('.directorist-search-sub-category').hide();
      }

      // Add the new options to the Select2 element
      catChild.select2({
        data: newOptions,
      });
      catChild.val(null).trigger('change');
      // Set parent value
      $('input[name="in_cat"]').val(parent_value);
    });

    $('select[name="in_cat_child"]').on("change", function (e) {
      e.preventDefault();
      var child_value = $(this).val();
      $('input[name="in_cat"]').val(child_value);
    });
});
