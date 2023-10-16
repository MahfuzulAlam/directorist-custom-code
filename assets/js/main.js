/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here
  if ($(".directorist-search-contents").length) {
    $("body").off("change", ".directorist-category-select");
    $("body").on("change", ".directorist-category-select", function (event) {
      var $this = $(this);
      var $container = $this.parents("form");
      var cat_id = $this.val();
      var directory_type = $container.find(".listing_type").val();
      var $search_form_box = $container.find(
        ".directorist-search-form-box-wrap"
      );
      var form_data = new FormData();
      form_data.append("action", "directorist_category_custom_field_search");
      form_data.append("nonce", directorist.directorist_nonce);
      form_data.append("listing_type", directory_type);
      form_data.append("cat_id", cat_id);
      form_data.append("atts", JSON.stringify($container.data("atts")));
      $search_form_box.addClass("atbdp-form-fade");
      $.ajax({
        method: "POST",
        processData: false,
        contentType: false,
        url: directorist.ajax_url,
        data: form_data,
        success: function success(response) {
          if (response) {
            $search_form_box.html(response["search_form"]);
            $container
              .find(".directorist-category-select option")
              .data("custom-field", 1);
            $container.find(".directorist-category-select").val(cat_id);
            [
              new CustomEvent("directorist-search-form-nav-tab-reloaded"),
              new CustomEvent("directorist-reload-select2-fields"),
              new CustomEvent("directorist-reload-map-api-field"),
              new CustomEvent("triggerSlice"),
            ].forEach(function (event) {
              document.body.dispatchEvent(event);
              window.dispatchEvent(event);
            });
          }

          $search_form_box.removeClass("atbdp-form-fade");
        },
        error: function error(_error) {
          //console.log(_error);
        },
        complete: function () {
          directorist_get_category_based_tags(cat_id);
        },
      });
    });
  } // load custom fields of the selected category in the search form

  // Category based tags
  function directorist_get_category_based_tags(cat_id) {
    var tag_source = $("#directorist_tag_source").val();
    if (tag_source !== "all_tags") return;

    var form_data = new FormData();
    form_data.append("action", "directorist_get_category_based_tags");
    form_data.append("nonce", directorist.directorist_nonce);
    form_data.append("cat_id", cat_id);

    $.ajax({
      method: "POST",
      processData: false,
      contentType: false,
      url: directorist.ajax_url,
      data: form_data,
      success: function success(response) {
        if (response) {
          if (response.tags_html !== "") {
            $(".directorist-search-field > .directorist-search-tags").empty();
            $(".directorist-search-field > .directorist-search-tags").html(
              response.tags_html
            );
          }
          // $search_form_box.html(response["search_form"]);
          // $container
          //   .find(".directorist-category-select option")
          //   .data("custom-field", 1);
          // $container.find(".directorist-category-select").val(cat_id);
          [
            new CustomEvent("directorist-search-form-nav-tab-reloaded"),
            new CustomEvent("directorist-reload-select2-fields"),
            new CustomEvent("directorist-reload-map-api-field"),
            new CustomEvent("triggerSlice"),
          ].forEach(function (event) {
            document.body.dispatchEvent(event);
            window.dispatchEvent(event);
          });
        }
      },
      error: function error(_error) {
        //console.log(_error);
      },
      complete: function () {
        //directorist_get_category_based_tags(cat_id);
      },
    });
  }
});
