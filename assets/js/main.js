/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  $(document).on("click", ".directorist-custom-category-list__toggle", function () {
    var $toggle = $(this);
    var targetId = $toggle.attr("aria-controls");
    var target = targetId ? document.getElementById(targetId) : null;
    var expanded = $toggle.attr("aria-expanded") === "true";

    if (!target) {
      return;
    }

    $toggle.attr("aria-expanded", expanded ? "false" : "true");
    target.hidden = expanded;
  });
});
