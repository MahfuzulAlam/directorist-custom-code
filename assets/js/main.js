jQuery(function ($) {

  // Helper: check if Google Places API is available
  function isGooglePlacesLoaded() {
    return (typeof google !== "undefined" && google.maps && google.maps.places);
  }

  // Init autocomplete for a single input
  function initAutocompleteForInput(input) {
    if (!input || !isGooglePlacesLoaded()) return;

    const opt = {
      types: ["geocode"],
      componentRestrictions: {
        country: directorist.restricted_countries,
      },
    };
    const options = directorist.countryRestriction ? opt : { types: [] };

    const autocomplete = new google.maps.places.Autocomplete(input, options);

    autocomplete.addListener("place_changed", function () {
      const place = autocomplete.getPlace();
      if (!place.place_id) return;

      const $wrapper = $(input).closest(".address_item");
      $wrapper.find(".google_addresses_lat").val(place.geometry?.location?.lat() || "");
      $wrapper.find(".google_addresses_lng").val(place.geometry?.location?.lng() || "");

      // Store full place info (if not already there)
      let $hiddenInput = $wrapper.find(".google_place");
      if (!$hiddenInput.length) {
        $hiddenInput = $('<input>', { type: 'hidden', class: 'google_place' }).appendTo($wrapper);
      }
      $hiddenInput.val(JSON.stringify({
        place_id: place.place_id,
        place_address: input.value,
      }));

      generateJson();
    });
  }

  // Init all autocompletes on page load
  function initAutocomplete() {
    document.querySelectorAll(".directorist-form-multi-address-field .google_addresses").forEach(function (input) {
      initAutocompleteForInput(input);
    });
  }

  // Random int generator
  function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  // Generate new address field
  function generateAddressField() {
    const uniqueId = getRandomInt(100000, 999999);
    const newField = `
      <div class="address_item" data-id="${uniqueId}">
        <input type="text" autocomplete="off" name="addresses[]" 
               class="directorist-form-element google_addresses" 
               placeholder="Enter address">
        <input type="hidden" class="google_addresses_lat" name="latitude[]" value="">
        <input type="hidden" class="google_addresses_lng" name="longitude[]" value="">
        <button type="button" class="remove_address_btn">X</button>
      </div>
    `;
    $(".directorist-form-multi-address-field .address_field_holder").append(newField);
  }

  // Generate JSON from all fields
  function generateJson() {
    const addresses = [];
    $(".address_item").each(function () {
      const addr = $(this).find(".google_addresses").val() || "";
      const lat = $(this).find(".google_addresses_lat").val() || "";
      const lng = $(this).find(".google_addresses_lng").val() || "";

      if (addr.trim() !== "") {
        addresses.push({ address: addr, latitude: lat, longitude: lng });
      }
    });

    $('input.google_addresses_json').val(JSON.stringify(addresses));
  }

  // Events
  $(document).on("click", ".directorist-form-multi-address-field .add_address_btn", function () {
    generateAddressField();
    const $newField = $(".directorist-form-multi-address-field .address_field_holder .address_item").last().find(".google_addresses");
    if ($newField.length) {
      initAutocompleteForInput($newField[0]);
    }
  });

  $(document).on("click", ".directorist-form-multi-address-field .remove_address_btn", function () {
    $(this).closest(".address_item").remove();
    generateJson();
  });

  // Init autocomplete after window load
  $(window).on("load", function () {
    if (isGooglePlacesLoaded()) {
      // take some time
      setTimeout(initAutocomplete, 1000);
      //initAutocomplete();
    } else {
      console.error("Google Maps JS API not loaded!");
    }
  });

});
