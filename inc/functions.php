<?php

/**
 * Add your custom php code here
 */
//add_action('wp_footer', 'dar_google_place_autocomplate');
//add_action('admin_footer', 'dar_google_place_autocomplate');
function dar_google_place_autocomplate()
{
?>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKlvwZukAgDO0vxlI9nyiW0sj2f9jqfUU&libraries=places"></script> -->
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            function initAutocomplete() {
                var input = document.getElementById('google_place_address');
                if (!input) return;

                var autocomplete = new google.maps.places.Autocomplete(input, {
                    types: [], // or use ['establishment'] if you want businesses
                    fields: ['place_id'] // Optimize API usage
                    // Removed componentRestrictions to allow global search
                });

                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.place_id) return;

                    $('#google_place').val(JSON.stringify({
                        place_id: place.place_id,
                        place_address: input.value
                    }));
                });
            }

            // Load the autocomplete after window load
            window.onload = function() {
                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    initAutocomplete();
                } else {
                    console.error('Google Maps JS API not loaded!');
                }
            };

        });
    </script>
<?php
}
