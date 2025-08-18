jQuery(document).ready(function ($) {

  // Load initial autocomplete after window load
  window.onload = function () {
    if (typeof google !== "undefined" && google.maps && google.maps.places) {
      initAutocomplete();
    } else {
      console.error("Google Maps JS API not loaded!");
    }
  };
  
  function initAutocompleteForInput(input) {
    if (!input) return;

    console.log(input);

    var opt = {
      types: ["geocode"],
      componentRestrictions: {
        country: directorist.restricted_countries,
      },
    };
    var options = directorist.countryRestriction ? opt : { types: [] };

    var autocomplete = new google.maps.places.Autocomplete(input, options);

    autocomplete.addListener("place_changed", function () {
      var place = autocomplete.getPlace();
      if (!place.place_id) return;

      // Update lat/lng hidden fields inside same address_item
      var $wrapper = $(input).closest(".address_item");
      $wrapper
        .find(".google_addresses_lat")
        .val(place.geometry?.location?.lat() || "");
      $wrapper
        .find(".google_addresses_lng")
        .val(place.geometry?.location?.lng() || "");

      // Optional: Store full place info if needed
      var $hiddenInput = $wrapper.find(".google_place");
      if (!$hiddenInput.length) {
        $hiddenInput = $(
          '<input type="hidden" class="google_place" />'
        ).appendTo($wrapper);
      }
      $hiddenInput.val(
        JSON.stringify({
          place_id: place.place_id,
          place_address: input.value,
        })
      );
      generateJson();
    });
  }

  function initAutocomplete() {
    document.querySelectorAll(".directorist-form-multi-address-field .google_addresses").forEach(function (input) {
      initAutocompleteForInput(input);
    });
  }

  function getRandomInt(min, max) {
    min = Math.ceil(min); // round up
    max = Math.floor(max); // round down
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  function generateAddressField(){
    let newField = `
        <div class="address_item">
            <input type="text" autocomplete="off" name="address[]" class="directorist-form-element google_addresses multi-field-`+getRandomInt(100000, 999999)+`" placeholder="Enter address">
            <input type="hidden" class="google_addresses_lat" name="latitude[]" value="">
            <input type="hidden" class="google_addresses_lng" name="longitude[]" value="">
            <button type="button" class="remove_address_btn">Remove</button>
        </div>
        `;
    $(".directorist-form-multi-address-field .address_field_holder").append(newField);
  }

  // Re-init autocomplete when new address fields are added dynamically
  jQuery(document).on("click", ".directorist-form-multi-address-field .add_address_btn", function () {
    generateAddressField();
    var $newField = $(".directorist-form-multi-address-field .address_field_holder .address_item")
      .last()
      .find(".google_addresses");
    if (
      $newField.length &&
      typeof google !== "undefined" &&
      google.maps &&
      google.maps.places
    ) {
      initAutocompleteForInput($newField[0]);
    }
  });

  // Remove address field
  $(document).on("click", ".directorist-form-multi-address-field .remove_address_btn", function () {
    $(this).closest(".address_item").remove();
    generateJson();
  });

  // Function to generate JSON from fields
  function generateJson() {
    let addresses = [];
    $(".address_item").each(function () {
      let addr = $(this).find(".google_addresses").val();
      let lat = $(this).find(".google_addresses_lat").val();
      let lng = $(this).find(".google_addresses_lng").val();

      if (addr.trim() !== "") {
        addresses.push({
          address: addr,
          latitude: lat,
          longitude: lng,
        });
      }
    });

    $('input.google_addresses_json').val(JSON.stringify(addresses));
  }
});
