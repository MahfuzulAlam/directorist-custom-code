<?php

/**
 * Add your custom php code here
 */


add_action( 'wp_footer', function(){
?>
    <script type="text/javascript">
        jQuery('document').ready(function($){
            $('#directorist-reset-filter-button').click(function(e){
                e.preventDefault();
                $('.directorist-btn-reset-js').trigger('click');
                window.location.href = window.location.protocol + '//' + window.location.hostname + window.location.pathname
            });
        });
    </script>
<?php
} );