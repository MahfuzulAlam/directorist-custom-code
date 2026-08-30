(function ($) {
  'use strict';

  $(function () {
    var $list = $('#directorist-reorder-types-order-list');
    var $value = $('#directorist-reorder-types-order-value');

    if (!$list.length || !$value.length) {
      return;
    }

    function updateValue() {
      var order = $list.children('[data-term-id]').map(function () {
        return $(this).data('term-id');
      }).get();

      $value.val(order.join(','));
    }

    $list.sortable({
      axis: 'y',
      cursor: 'grabbing',
      placeholder: 'directorist-reorder-types__placeholder',
      update: updateValue
    });

    updateValue();
  });
})(jQuery);
