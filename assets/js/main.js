/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  function initAutocomplete() {
    var input = document.getElementById("google_place_address");
    if (!input) return;

    var opt = {
        types: ['geocode'],
        componentRestrictions: {
          country: directorist.restricted_countries
        }
      };
      var options = directorist.countryRestriction ? opt : {
        types: []
      };

    var autocomplete = new google.maps.places.Autocomplete(input, options);

    console.log(autocomplete);

    autocomplete.addListener("place_changed", function () {
      var place = autocomplete.getPlace();
      if (!place.place_id) return;

      $("#google_place").val(
        JSON.stringify({
          place_id: place.place_id,
          place_address: input.value,
        })
      );
    });
  }

  // Load the autocomplete after window load
  window.onload = function () {
    if (typeof google !== "undefined" && google.maps && google.maps.places) {
      initAutocomplete();
    } else {
      console.error("Google Maps JS API not loaded!");
    }
  };
});
