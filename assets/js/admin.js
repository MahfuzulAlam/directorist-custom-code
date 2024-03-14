/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here
  $(function() {

    var start_date = $('input[name=start_date]').val();
    var end_date = $('input[name=end_date]').val();

    var start = start_date ? moment( start_date ) : moment();
    var end = end_date ? moment( end_date ) : moment().add( 30, 'days' );

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('input[name=start_date]').val( start.format('YYYY-MM-DD') );
        $('input[name=end_date]').val( end.format('YYYY-MM-DD') );
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

  });
});
