/**
 * Keep profile-derived search locations in sync with visitor changes.
 */

jQuery(document).ready(function ($) {
  var stateSelector = 'input[name="dcc_profile_location_state"]';
  var lastFocusedAddress = $();
  var addressMutationSequence = 0;

  function isManagedContext($context) {
    return (
      $context.length > 0 &&
      ($context.find(stateSelector).length > 0 ||
        $context.find(
          '[data-dcc-profile-location-default="taxonomy"], ' +
            '[data-dcc-profile-location-default="address"]'
        ).length > 0)
    );
  }

  function getSearchContext($field) {
    var $context = $field.closest('form');

    if (!$context.length) {
      $context = $field
        .closest('.directorist-instant-search')
        .find('form')
        .filter(function () {
          return isManagedContext($(this));
        })
        .first();
    }

    return $context;
  }

  function getStateField($context) {
    return $context.find(stateSelector).first();
  }

  function setLocationState($context, state) {
    if (!isManagedContext($context)) {
      return;
    }

    var $state = getStateField($context);
    if (!$state.length) {
      $state = $(
        '<input type="hidden" name="dcc_profile_location_state" class="dcc-profile-location-state">'
      );
      $context.append($state);
    }
    $state.val(state);

    var $instantSearch = $context.closest('.directorist-instant-search');
    if ($instantSearch.length) {
      $instantSearch.find(stateSelector).val(state);
      $instantSearch.data('dccProfileLocationState', state);
    }
  }

  function getProfileComponents($context) {
    var state = String(getStateField($context).val() || '');

    return state.split(',').filter(function (component) {
      return component === 'taxonomy' || component === 'address';
    });
  }

  function hasLocationValue($context) {
    var hasValue = false;

    $context
      .find('select[name="in_loc"], input[name="address"], input[name="zip"]')
      .each(function () {
        if ($.trim(String($(this).val() || '')) !== '') {
          hasValue = true;
          return false;
        }
      });

    return hasValue;
  }

  function updateStateFromValues($context) {
    if (getProfileComponents($context).length) {
      return;
    }

    setLocationState(
      $context,
      hasLocationValue($context) ? 'custom' : 'cleared'
    );
  }

  function removeProfileComponent($context, component) {
    var components = getProfileComponents($context).filter(function (item) {
      return item !== component;
    });

    if (components.length) {
      setLocationState($context, components.join(','));
      return;
    }

    setLocationState(
      $context,
      hasLocationValue($context) ? 'custom' : 'cleared'
    );
  }

  function syncSelectedLocationData($location, value) {
    var selectedLabel = '';

    if (value) {
      selectedLabel = $.trim($location.find('option:selected').text());

      if ($location.hasClass('select2-hidden-accessible')) {
        try {
          var selectedData = $location.select2('data');
          if (selectedData && selectedData.length && selectedData[0].text) {
            selectedLabel = selectedData[0].text;
          }
        } catch (error) {
          // Select2 can be between destroy/reinitialize cycles during AJAX reloads.
        }
      }

      $location
        .attr('data-selected-id', value)
        .attr('data-selected-label', selectedLabel)
        .data('selected-id', value)
        .data('selected-label', selectedLabel);
      return;
    }

    $location
      .removeAttr('data-selected-id data-selected-label')
      .removeData('selected-id')
      .removeData('selected-label');

    var $select2Container = $location.next('.select2-container');
    var $addons = $select2Container.find('.directorist-select2-addons-area');
    $addons.find('.directorist-select2-dropdown-close').remove();
    $select2Container
      .find('.select2-selection__rendered')
      .css('padding-right', ($addons.outerWidth() || 0) + 'px');
  }

  function clearProfileTaxonomyDefault($context) {
    $context
      .find(
        'select[name="in_loc"][data-dcc-profile-location-default="taxonomy"]'
      )
      .each(function () {
        var $location = $(this);

        $location
          .removeAttr(
            'data-dcc-profile-location-default data-dcc-profile-location-value'
          )
          .val('');
        syncSelectedLocationData($location, '');
        removeProfileComponent($context, 'taxonomy');
        $location.trigger('change');
      });
  }

  function clearProfileAddressDefault($context) {
    $context
      .find(
        'input[name="address"][data-dcc-profile-location-default="address"]'
      )
      .each(function () {
        var $address = $(this);
        var $field = $address.closest('.directorist-search-field');

        $address
          .removeAttr(
            'data-dcc-profile-location-default data-dcc-profile-location-value'
          )
          .val('');
        $field.find('input[name="cityLat"], input[name="cityLng"]').val('');
        $field.find('.address_result').hide();
        removeProfileComponent($context, 'address');
        $address.trigger('input');
      });
  }

  function getAddressState($address) {
    var $field = $address.closest('.directorist-search-field');

    return {
      value: String($address.val() || ''),
      dataValue: $address.attr('data-value'),
      latitude: String($field.find('input[name="cityLat"]').val() || ''),
      longitude: String($field.find('input[name="cityLng"]').val() || ''),
    };
  }

  function applyAddressState($address, state) {
    var $field = $address.closest('.directorist-search-field');
    $address.val(state.value);

    if (typeof state.dataValue === 'undefined') {
      $address.removeAttr('data-value');
    } else {
      $address.attr('data-value', state.dataValue);
    }

    $field.find('input[name="cityLat"]').val(state.latitude);
    $field.find('input[name="cityLng"]').val(state.longitude);
  }

  function rememberAddressState($address) {
    if (!$address.length) {
      return;
    }

    addressMutationSequence += 1;
    $address
      .data('dccAddressMutationVersion', addressMutationSequence)
      .data('dccAddressStableState', getAddressState($address));
  }

  function invalidateProgrammaticAddressWatches($context) {
    $context.find('.dcc-search-location-address').each(function () {
      var $address = $(this);

      if ($address.data('dccProgrammaticAddressTimer')) {
        rememberAddressState($address);
      }
    });
  }

  function hasValidCoordinates(latitude, longitude) {
    if (latitude === '' || longitude === '') {
      return false;
    }

    var lat = Number(latitude);
    var lng = Number(longitude);

    return (
      Number.isFinite(lat) &&
      Number.isFinite(lng) &&
      lat >= -90 &&
      lat <= 90 &&
      lng >= -180 &&
      lng <= 180
    );
  }

  function usesOpenStreetMap() {
    return (
      window.directorist &&
      window.directorist.i18n_text &&
      window.directorist.i18n_text.select_listing_map === 'openstreet'
    );
  }

  function markAddressAsCustom($address, force) {
    var $context = getSearchContext($address);
    if (!isManagedContext($context)) {
      return;
    }

    var wasProfileDefault =
      $address.attr('data-dcc-profile-location-default') === 'address';
    var defaultValue = String(
      $address.attr('data-dcc-profile-location-value') || ''
    );
    var currentValue = String($address.val() || '');

    if (!force && wasProfileDefault && currentValue === defaultValue) {
      return;
    }

    $address.removeAttr(
      'data-dcc-profile-location-default data-dcc-profile-location-value'
    );

    if (wasProfileDefault) {
      removeProfileComponent($context, 'address');
    }

    if ($.trim(currentValue) !== '') {
      clearProfileTaxonomyDefault($context);
      setLocationState($context, 'custom');
    } else {
      updateStateFromValues($context);
    }

    syncRequiredLocation($context);
  }

  function startProgrammaticAddressWatch($address, restoreUnrelatedForms) {
    var $context = getSearchContext($address);
    if (!$address.length || !isManagedContext($context)) {
      return;
    }

    var $field = $address.closest('.directorist-search-field');
    var initialAddress = String($address.val() || '');
    var initialLatitude = String(
      $field.find('input[name="cityLat"]').val() || ''
    );
    var initialLongitude = String(
      $field.find('input[name="cityLng"]').val() || ''
    );
    var $globalLatitude = restoreUnrelatedForms
      ? $('input#cityLat').first()
      : $();
    var $globalLongitude = restoreUnrelatedForms
      ? $('input#cityLng').first()
      : $();
    var initialGlobalLatitude = String($globalLatitude.val() || '');
    var initialGlobalLongitude = String($globalLongitude.val() || '');
    var attempts = 0;
    var unrelatedSnapshots = [];
    var existingTimer = $address.data('dccProgrammaticAddressTimer');

    if (existingTimer) {
      window.clearInterval(existingTimer);
    }

    if (restoreUnrelatedForms) {
      $('.dcc-search-location-address')
        .not($address)
        .each(function () {
          var $otherAddress = $(this);

          unrelatedSnapshots.push({
            address: $otherAddress,
            version: Number(
              $otherAddress.data('dccAddressMutationVersion') || 0
            ),
            state: getAddressState($otherAddress),
          });
        });
    }

    // A new watcher is itself a mutation. This lets another form's OpenStreet
    // watcher preserve this field's most recent intentional state.
    rememberAddressState($address);
    var watcherMutationVersion = Number(
      $address.data('dccAddressMutationVersion') || 0
    );

    function restoreUnrelatedAddressSnapshots() {
      unrelatedSnapshots.forEach(function (snapshot) {
        var state = snapshot.state;
        var currentVersion = Number(
          snapshot.address.data('dccAddressMutationVersion') || 0
        );

        if (currentVersion !== snapshot.version) {
          state = snapshot.address.data('dccAddressStableState') || state;
        }

        applyAddressState(snapshot.address, state);
      });
    }

    var timer = window.setInterval(function () {
      attempts += 1;

      if (!$address.closest('html').length) {
        window.clearInterval(timer);
        $address.removeData('dccProgrammaticAddressTimer');
        return;
      }

      var currentAddress = String($address.val() || '');
      var currentLatitude = String(
        $field.find('input[name="cityLat"]').val() || ''
      );
      var currentLongitude = String(
        $field.find('input[name="cityLng"]').val() || ''
      );
      var addressChanged =
        $.trim(currentAddress) !== '' && currentAddress !== initialAddress;
      var targetCoordinatesChanged =
        currentLatitude !== initialLatitude ||
        currentLongitude !== initialLongitude;
      var observedLatitude = currentLatitude;
      var observedLongitude = currentLongitude;
      var usedGlobalCoordinates = false;
      var watcherStillOwnsAddress =
        Number($address.data('dccAddressMutationVersion') || 0) ===
        watcherMutationVersion;

      if (!watcherStillOwnsAddress) {
        var latestAddressState = $address.data('dccAddressStableState');

        if (latestAddressState) {
          applyAddressState($address, latestAddressState);
        }

        if (restoreUnrelatedForms) {
          restoreUnrelatedAddressSnapshots();
        }

        if (attempts >= 120) {
          window.clearInterval(timer);
          $address.removeData('dccProgrammaticAddressTimer');
        }

        return;
      }

      // Directorist's OpenStreet handler writes duplicate-ID coordinates to
      // the first form. Only use that pair when the clicked form did not get
      // its own update, and compare it with its own pre-click baseline.
      if (
        !targetCoordinatesChanged &&
        restoreUnrelatedForms &&
        watcherStillOwnsAddress
      ) {
        var globalLatitude = String($globalLatitude.val() || '');
        var globalLongitude = String($globalLongitude.val() || '');
        var globalCoordinatesChanged =
          globalLatitude !== initialGlobalLatitude ||
          globalLongitude !== initialGlobalLongitude;

        if (
          globalCoordinatesChanged ||
          (addressChanged &&
            hasValidCoordinates(globalLatitude, globalLongitude))
        ) {
          observedLatitude = globalLatitude;
          observedLongitude = globalLongitude;
          usedGlobalCoordinates = true;
        }
      }

      var coordinatesChanged =
        targetCoordinatesChanged || usedGlobalCoordinates;

      if (
        addressChanged &&
        coordinatesChanged &&
        hasValidCoordinates(observedLatitude, observedLongitude)
      ) {
        window.clearInterval(timer);
        $address.removeData('dccProgrammaticAddressTimer');

        if (restoreUnrelatedForms) {
          if (usedGlobalCoordinates) {
            $field.find('input[name="cityLat"]').val(observedLatitude);
            $field.find('input[name="cityLng"]').val(observedLongitude);
          }

          restoreUnrelatedAddressSnapshots();
        }

        rememberAddressState($address);
        markAddressAsCustom($address, true);
        $field.trigger('change');
        return;
      }

      if (attempts >= 120) {
        window.clearInterval(timer);
        $address.removeData('dccProgrammaticAddressTimer');
      }
    }, 250);

    $address.data('dccProgrammaticAddressTimer', timer);
  }

  function markZipAsCustom($zip) {
    var $context = getSearchContext($zip);
    if (!isManagedContext($context)) {
      return;
    }

    invalidateProgrammaticAddressWatches($context);

    if ($.trim(String($zip.val() || '')) !== '') {
      clearProfileTaxonomyDefault($context);
      clearProfileAddressDefault($context);
      setLocationState($context, 'custom');
      return;
    }

    updateStateFromValues($context);
  }

  function getAjaxDataValue(data, key) {
    if (!data) {
      return '';
    }

    if (typeof data === 'string') {
      return String(new URLSearchParams(data).get(key) || '');
    }

    if (window.FormData && data instanceof window.FormData) {
      return String(data.get(key) || '');
    }

    if (typeof data === 'object' && typeof data[key] !== 'undefined') {
      return Array.isArray(data[key])
        ? String(data[key][0] || '')
        : String(data[key] || '');
    }

    return '';
  }

  function setAjaxDataValue(options, key, value) {
    if (getAjaxDataValue(options.data, key) === String(value)) {
      return;
    }

    if (typeof options.data === 'string') {
      var data = {};
      data[key] = value;
      options.data += (options.data ? '&' : '') + $.param(data);
      return;
    }

    if (window.FormData && options.data instanceof window.FormData) {
      options.data.set(key, value);
      return;
    }

    if (!options.data || typeof options.data !== 'object') {
      options.data = {};
    }

    options.data[key] = value;
  }

  function getInstantSearchWrapper(data) {
    var atts = data && typeof data === 'object' ? data.data_atts : null;

    if (!atts) {
      return $();
    }

    return $('.directorist-instant-search')
      .filter(function () {
        return $(this).data('atts') === atts;
      })
      .first();
  }

  function getProfileComponentRequestValues($instantSearch, state) {
    var values = {};
    var components = String(state).split(',');

    if (components.indexOf('taxonomy') !== -1) {
      var $location = $instantSearch
        .find(
          'select[name="in_loc"][data-dcc-profile-location-default="taxonomy"]'
        )
        .first();
      var locationId = String($location.val() || '');

      if (locationId) {
        values.in_loc = locationId;
      }
    }

    if (components.indexOf('address') !== -1) {
      var $address = $instantSearch
        .find(
          'input[name="address"][data-dcc-profile-location-default="address"]'
        )
        .first();
      var address = String($address.val() || '');

      if (address) {
        var $field = $address.closest('.directorist-search-field');
        values.address = address;
        values.cityLat = String(
          $field.find('input[name="cityLat"]').val() || ''
        );
        values.cityLng = String(
          $field.find('input[name="cityLng"]').val() || ''
        );
      }
    }

    return values;
  }

  function syncStateToCurrentUrl(state, requestValues) {
    if (!window.history || !window.history.replaceState) {
      return;
    }

    var url = new URL(window.location.href);
    url.searchParams.set('dcc_profile_location_state', state);

    $.each(requestValues, function (key, value) {
      if (value === '') {
        url.searchParams.delete(key);
      } else {
        url.searchParams.set(key, value);
      }
    });

    window.history.replaceState(window.history.state, '', url.toString());
  }

  function syncRequiredLocation($context) {
    var $validator = $context
      .find('[data-dcc-location-required="1"]')
      .first();

    if (!$validator.length) {
      return;
    }

    $validator.prop('required', !hasLocationValue($context));
  }

  function syncRenderedRequiredLocations() {
    $('form').has('[data-dcc-location-required="1"]').each(function () {
      syncRequiredLocation($(this));
    });
  }

  function cacheRenderedInstantSearchStates() {
    $('.directorist-instant-search').each(function () {
      var $instantSearch = $(this);
      var state = String(
        $instantSearch.find(stateSelector).first().val() || ''
      );

      if (state) {
        $instantSearch.data('dccProfileLocationState', state);
      }
    });
  }

  cacheRenderedInstantSearchStates();
  syncRenderedRequiredLocations();
  window.addEventListener(
    'directorist-instant-search-reloaded',
    function () {
      cacheRenderedInstantSearchStates();
      syncRenderedRequiredLocations();
    }
  );

  $.ajaxPrefilter(function (options, originalOptions) {
    var requestData = originalOptions.data;
    var requestType = String(options.type || originalOptions.type || '').toUpperCase();
    var ajaxUrl = window.directorist && window.directorist.ajaxurl;

    if (
      getAjaxDataValue(requestData, 'action') !==
        'directorist_instant_search' ||
      requestType !== 'POST' ||
      !ajaxUrl ||
      options.url !== ajaxUrl
    ) {
      return;
    }

    var $instantSearch = getInstantSearchWrapper(requestData);
    if (!$instantSearch.length) {
      return;
    }

    var state = String(
      $instantSearch.data('dccProfileLocationState') ||
        $instantSearch.find(stateSelector).first().val() ||
        ''
    );
    if (!state) {
      return;
    }

    var requestValues = getProfileComponentRequestValues(
      $instantSearch,
      state
    );

    setAjaxDataValue(options, 'dcc_profile_location_state', state);
    $.each(requestValues, function (key, value) {
      setAjaxDataValue(options, key, value);
    });
    syncStateToCurrentUrl(state, requestValues);
  });

  $('body').on(
    'input change',
    'select[name="in_loc"], input[name="address"], input[name="zip"]',
    function () {
      syncRequiredLocation(getSearchContext($(this)));
    }
  );

  document.addEventListener(
    'click',
    function (event) {
      var button = event.target.closest(
        '.directorist-search-field__btn--clear'
      );

      if (!button) {
        return;
      }

      var $field = $(button).closest('.directorist-search-field');
      var $context = getSearchContext($field);

      if (!isManagedContext($context)) {
        return;
      }

      var $address = $field.find('.dcc-search-location-address');
      var $location = $field.find('.dcc-search-location-taxonomy');
      var $zip = $field.find('input[name="zip"]');

      if (!$address.length && !$location.length && !$zip.length) {
        return;
      }

      if ($address.length) {
        var wasAddressDefault =
          $address.attr('data-dcc-profile-location-default') === 'address';
        $address
          .removeAttr(
            'data-dcc-profile-location-default data-dcc-profile-location-value'
          )
          .val('');
        $field.find('input[name="cityLat"], input[name="cityLng"]').val('');
        rememberAddressState($address);

        if (wasAddressDefault) {
          removeProfileComponent($context, 'address');
        }
      }

      if ($location.length) {
        var wasTaxonomyDefault =
          $location.attr('data-dcc-profile-location-default') === 'taxonomy';
        $location.removeAttr(
          'data-dcc-profile-location-default data-dcc-profile-location-value'
        );
        $location.val('');
        syncSelectedLocationData($location, '');

        if (wasTaxonomyDefault) {
          removeProfileComponent($context, 'taxonomy');
        }
      }

      if ($zip.length) {
        $zip.val('');
        $field
          .find(
            '.zip-cityLat, .zip-cityLng, input[name="zip_cityLat"], input[name="zip_cityLng"]'
          )
          .val('');
      }

      invalidateProgrammaticAddressWatches($context);
      updateStateFromValues($context);
      syncRequiredLocation($context);
    },
    true
  );

  $(document).on('input', '.dcc-search-location-address', function () {
    var $address = $(this);
    var defaultValue = String(
      $address.attr('data-dcc-profile-location-value') || ''
    );
    var currentValue = String($address.val() || '');

    if (
      $address.attr('data-dcc-profile-location-default') === 'address' &&
      currentValue === defaultValue
    ) {
      return;
    }

    $address
      .closest('.directorist-search-field')
      .find('input[name="cityLat"], input[name="cityLng"]')
      .val('');
    rememberAddressState($address);
    markAddressAsCustom($address);
  });

  $(document).on('focusin', '.dcc-search-location-address', function () {
    var $address = $(this);

    lastFocusedAddress = isManagedContext(getSearchContext($address))
      ? $address
      : $();
  });

  $(document).on('change', '.dcc-search-location-taxonomy', function () {
    var $location = $(this);
    var $context = getSearchContext($location);

    if (!isManagedContext($context)) {
      return;
    }

    var isProfileDefault =
      $location.attr('data-dcc-profile-location-default') === 'taxonomy';
    var defaultValue = String(
      $location.attr('data-dcc-profile-location-value') || ''
    );
    var currentValue = String($location.val() || '');

    if (isProfileDefault && currentValue === defaultValue) {
      return;
    }

    invalidateProgrammaticAddressWatches($context);

    $location.removeAttr(
      'data-dcc-profile-location-default data-dcc-profile-location-value'
    );
    syncSelectedLocationData($location, currentValue);

    if (isProfileDefault) {
      removeProfileComponent($context, 'taxonomy');
    }

    if (currentValue !== '') {
      clearProfileAddressDefault($context);
      setLocationState($context, 'custom');
    } else {
      updateStateFromValues($context);
    }
  });

  $(document).on('change', 'input[name="miles"]', function () {
    var $context = getSearchContext($(this));
    setLocationState($context, 'custom');
  });

  $(document).on('input', 'input[name="zip"]', function () {
    var $zip = $(this);
    $zip
      .closest('.directorist-zipcode-search')
      .find(
        '.zip-cityLat, .zip-cityLng, input[name="zip_cityLat"], input[name="zip_cityLng"]'
      )
      .val('');
    markZipAsCustom($zip);
  });

  $(document).on('change', 'input[name="zip"]', function () {
    markZipAsCustom($(this));
  });

  $(document).on('click', '.address_result ul li a', function () {
    var isCurrentLocation = $(this).hasClass('current-location');
    var $address = $(this)
      .closest('.directorist-search-field')
      .find('.dcc-search-location-address');

    if (isCurrentLocation) {
      startProgrammaticAddressWatch($address, true);
    }

    setTimeout(function () {
      if (!isCurrentLocation) {
        rememberAddressState($address);
      }

      // Directorist clears a current-location result before permission and
      // reverse geocoding finish. Reconcile that blank immediately so a
      // denied/failed request cannot leave a stale profile-default marker.
      markAddressAsCustom($address);
    }, 0);
  });

  $(document).on('mousedown', '.pac-item', function () {
    startProgrammaticAddressWatch(lastFocusedAddress, false);
  });

  $(document).on('click', '.directorist-filter-location-icon', function () {
    startProgrammaticAddressWatch(
      $(this)
        .closest('.directorist-search-field')
        .find('.dcc-search-location-address'),
      usesOpenStreetMap()
    );
  });

  $(document).on('click', '.directorist-country ul li a', function () {
    var $zip = $(this)
      .closest('.directorist-zipcode-search')
      .find('input[name="zip"]');

    setTimeout(function () {
      markZipAsCustom($zip);
    }, 0);
  });

  $(document).on(
    'click',
    '.directorist-btn-reset-js, .directorist-btn-reset-ajax',
    function () {
      var $button = $(this);
      var $resetScope = $button.closest('.directorist-contents-wrap');

      if (!$resetScope.length) {
        $resetScope = $button.closest('.directorist-instant-search');
      }

      var $contexts = $resetScope.find('form').filter(function () {
        return isManagedContext($(this));
      });

      if (!$contexts.length) {
        var $context = getSearchContext($button);
        $contexts = isManagedContext($context) ? $context : $();
      }

      setTimeout(function () {
        $contexts.each(function () {
          var $context = $(this);

          invalidateProgrammaticAddressWatches($context);
          $context
            .find('[data-dcc-profile-location-default]')
            .removeAttr(
              'data-dcc-profile-location-default data-dcc-profile-location-value'
            );
          setLocationState($context, 'cleared');
          syncRequiredLocation($context);
        });
      }, 0);
    }
  );
});
