/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here
  directorist_search_home_page_custom_code();
  directorist_add_listing_page_custom_code();
  
  // search home page custom code
  function directorist_search_home_page_custom_code()
  {
    $('.directorist-search-form #at_biz_dir-categories_parent').select2();
    $('.directorist-search-form #at_biz_dir-categories_child').select2();

    $('.directorist-search-form select[name="in_cat_parent"]').on("change", function () {
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

    $('.directorist-search-form select[name="in_cat_child"]').on("change", function (e) {
      e.preventDefault();
      var child_value = $(this).val();
      $('input[name="in_cat"]').val(child_value);
    });
  }

  // add listing page custom code
  function directorist_add_listing_page_custom_code()
  {
    $('#directorist-add-listing-form #at_biz_dir-categories_parent').select2();
    $('#directorist-add-listing-form #at_biz_dir-categories_child').select2();

    $('#directorist-add-listing-form select[name="admin_category_select_parent"]').on("change", function () {
      var parent_value = $(this).val();

      var category_children = $("#category_children").val();

      if (category_children) {
        category_children = JSON.parse(category_children);
        category_children = category_children[parent_value];
      }

      // Get a reference to the Select2 element
      var catChild = $('select[name="admin_category_select_child[]"]');

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
        $('.directorist-form-sub-categories-field').css({visibility: 'visible', height: 'auto'});
      }else{
        $('.directorist-form-sub-categories-field').css({visibility: 'hidden', height: '0'});
      }

      // Add the new options to the Select2 element
      catChild.select2({
        data: newOptions,
      });
      catChild.val(null).trigger('change');
      // Set parent value
      $('#at_biz_dir-categories').val(parent_value).trigger('change');
    });

    $('#directorist-add-listing-form #at_biz_dir-categories_child').on("change", function (e) {
      e.preventDefault();
      var child_value = $(this).val();
      $('#at_biz_dir-categories').val(child_value).trigger('change');
    });
  }
});
