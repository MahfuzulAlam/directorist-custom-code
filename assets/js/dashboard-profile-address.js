/**
 * Profile address autocomplete (Google Places or Nominatim) on Directorist user dashboard.
 */
(function ($) {
  'use strict';

  function debounce(fn, wait) {
    var t;
    return function () {
      var ctx = this;
      var args = arguments;
      clearTimeout(t);
      t = setTimeout(function () {
        fn.apply(ctx, args);
      }, wait);
    };
  }

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function getFields() {
    var $form = $('#user_profile_form');
    if (!$form.length) {
      return null;
    }
    var $address = $form.find('input[name="user[address]"]');
    if (!$address.length) {
      return null;
    }
    return {
      $address: $address,
      $lat: $form.find('#latitude'),
      $lng: $form.find('#longitude'),
    };
  }

  function initOpenStreet(fields) {
    var $box = $('#dcc-profile-address-suggestions');
    if (!$box.length) {
      $box = $('<div id="dcc-profile-address-suggestions" class="dcc-profile-address-suggestions" aria-live="polite"></div>');
      fields.$address.closest('.directorist-form-group').append($box);
    }

    var runSearch = debounce(function () {
      var q = fields.$address.val().trim();
      if (q.length < 3) {
        $box.empty().hide();
        return;
      }
      $.ajax({
        url: 'https://nominatim.openstreetmap.org/search',
        type: 'GET',
        dataType: 'json',
        data: {
          q: q,
          format: 'json',
          limit: 5,
        },
        success: function (data) {
          if (!data || !data.length) {
            $box.empty().hide();
            return;
          }
          var html = '<ul class="dcc-profile-address-suggestions__list">';
          for (var i = 0; i < data.length; i++) {
            var item = data[i];
            var lat = escapeHtml(String(item.lat));
            var lon = escapeHtml(String(item.lon));
            var name = item.display_name || '';
            html +=
              '<li><button type="button" class="dcc-profile-address-suggestions__item" data-lat="' +
              lat +
              '" data-lon="' +
              lon +
              '">' +
              escapeHtml(name) +
              '</button></li>';
          }
          html += '</ul>';
          $box.html(html).show();
        },
        error: function () {
          $box.empty().hide();
        },
      });
    }, 750);

    fields.$address.on('input.dccProfileAddr', runSearch);

    $box.on('click.dccProfileAddr', '.dcc-profile-address-suggestions__item', function (e) {
      e.preventDefault();
      var $btn = $(this);
      fields.$address.val($btn.text());
      fields.$lat.val($btn.attr('data-lat'));
      fields.$lng.val($btn.attr('data-lon'));
      $box.empty().hide();
    });

    $(document).on('click.dccProfileAddrClose', function (e) {
      if (!$(e.target).closest('#address, #dcc-profile-address-suggestions').length) {
        $box.hide();
      }
    });
  }

  function googleAutocompleteOptions(cfg) {
    if (
      cfg.countryRestriction &&
      cfg.restrictedCountries &&
      cfg.restrictedCountries.length
    ) {
      return {
        types: ['geocode'],
        componentRestrictions: { country: cfg.restrictedCountries },
      };
    }
    return { types: [] };
  }

  function initGoogle(fields) {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
      return;
    }
    var cfg = window.dccDashboardAddress || {};
    var autocomplete = new google.maps.places.Autocomplete(
      fields.$address[0],
      googleAutocompleteOptions(cfg)
    );
    autocomplete.addListener('place_changed', function () {
      var place = autocomplete.getPlace();
      if (!place.geometry || !place.geometry.location) {
        return;
      }
      fields.$lat.val(place.geometry.location.lat());
      fields.$lng.val(place.geometry.location.lng());
    });
  }

  window.dccDashboardAddressGoogleInit = function () {
    jQuery(function () {
      var fields = getFields();
      if (!fields) {
        return;
      }
      initGoogle(fields);
    });
  };

  $(function () {
    var cfg = window.dccDashboardAddress;
    if (!cfg) {
      return;
    }

    var fields = getFields();
    if (!fields) {
      return;
    }

    if (cfg.mapType === 'openstreet') {
      initOpenStreet(fields);
      return;
    }

    if (cfg.mapType === 'google' && cfg.googleApiKey) {
      if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        initGoogle(fields);
      }
    }
  });
})(jQuery);
