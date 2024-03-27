@php

@endphp

<x-layout.app :title="__('map test')">

  <img class="hidden"
    id="markerSrc"
    src="{{ Vite::asset('resources/images/marker.png') }}" />

  <button x-data=""
    x-on:click="$dispatch('open-modal', 'add-info')"
    class="infoModal hidden"
    id="infoBtn"
    data-modal="{{ json_encode([
        'title.text' => __('add location'),
        'form.action' => route('info.add'),
        'method.value' => 'PUT',
        'id.value' => '',
        'lat.value' => old('lat', ''),
        'lng.value' => old('lng', ''),
        'number.value' => old('number', ''),
        'street.value' => old('street', ''),
        'town.value' => old('town', ''),
        'postcode.value' => old('postcode', ''),
        'name.value' => old('name', ''),
        'year.value' => old('year', ''),
        'note.value' => old('note', ''),
        'destroy.addclass' => 'hidden',
        'submit.text' => __('add'),
    ]) }}"></button>

  <button x-data=""
    x-on:click="$dispatch('open-modal', 'add-info')"
    class="infoModal hidden"
    id="editBtn"
    data-modal="{{ json_encode([
        'title.text' => __('edit location'),
        'form.action' => route('info.add'),
        'method.value' => 'PUT',
        'id.value' => old('id', ''),
        'lat.value' => old('lat', ''),
        'lng.value' => old('lng', ''),
        'number.value' => old('number', ''),
        'street.value' => old('street', ''),
        'town.value' => old('town', ''),
        'postcode.value' => old('postcode', ''),
        'name.value' => old('name', ''),
        'year.value' => old('year', ''),
        'note.value' => old('note', ''),
        'destroy.' . (old('id') ? 'removeclass' : 'addclass') => 'hidden',
        'destroy.data' => [
            'modal' => [
                'id.value' => old('id', ''),
            ],
        ],
        'submit.text' => __('save'),
    ]) }}"></button>

  <button x-data=""
    x-on:click.prevent.stop="$dispatch('open-modal', 'changes-modal')"
    class="changesBtn hidden"></button>

  <div class="mx-auto flex w-full flex-1 flex-col sm:max-w-7xl md:px-8">
    <div class="w-full flex-1 overflow-hidden border border-gray-400 bg-white shadow-lg md:rounded-md"
      id="map">

    </div>
  </div>

  @push('modals')
    @include('map.modal.add')
    @include('map.modal.destroy')
    @include('modal.changes')
  @endpush

  @vite('resources/js/map.js')
  <script type="module">
    const google = {
      addingMarkers: false,
      buttons: {},
      markers: []
    };

    (async () => {
      const initialPos = {
          lat: 53.27640,
          lng: -3.22189
        },
        keys = await fetchJSON('/google'),
        loader = new Loader({
          apiKey: keys.api,
          version: "weekly",
        });

      google.core = await loader.importLibrary('core');
      google.maps = await loader.importLibrary('maps');
      google.marker = await loader.importLibrary('marker');
      google.infos = await fetchJSON('/info');
      {{-- console.log(google); --}}

      google.map = new google.maps.Map(document.getElementById('map'), {
        center: initialPos,
        zoom: 14,
        mapId: keys.id,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false,
      });

      google.infoWindow = new google.maps.InfoWindow();

      const wrap = $('<div />', {
        class: 'flex flex-col justify-center items-center rounded-sm shadow-sm bg-white mr-2'
      });
      google.buttons.add = createControl('fa-duotone fa-location-plus border-b', 'Add marker!');
      google.buttons.pos = createControl('fa-duotone fa-circle-dot', 'Geolocation!');
      wrap.append(google.buttons.add);
      wrap.append(google.buttons.pos);

      google.map.controls[google.core.ControlPosition.RIGHT_CENTER].push(wrap[0]);

      google.map.addListener('click', e => {
        if (!google.addingMarkers) return;
        $('#infoBtn').trigger('click');
        $('#lat').val(e.latLng.lat());
        $('#lng').val(e.latLng.lng());
        placeMarker(e.latLng);
      });

      /* Change markers on zoom */
      google.core.event.addListener(google.map, 'zoom_changed', function() {
        const zoom = google.map.getZoom();
        google.markers.forEach(marker => {
          marker.map = zoom >= 16 ? google.map : null;
        });
      });

      google.infos.forEach(info => {
        placeMarker(new google.core.LatLng(info.position.lat, info.position.lng), info, false);
      });

      google.buttons.add.on('click', toggleMarker);
      google.buttons.pos.on('click', toGeoLocation);
      toGeoLocation();

      const user = new google.marker.AdvancedMarkerElement({
        map: google.map,
        content: $('<i />', {
          class: 'fa-duotone fa-circle-dot text-blue-500 text-lg'
        })[0]
      });

      trackLocation({
        onSuccess: ({
          coords: {
            latitude: lat,
            longitude: lng
          }
        }) => {
          user.position = {
            lat,
            lng
          };
        }
      });
    })();

    async function fetchJSON(url) {
      const res = await fetch(url);
      const json = await res.json();
      return json;
    }


    function toggleMarker() {
      if (google.addingMarkers) {
        google.map.setOptions({
          draggableCursor: ''
        });
        google.buttons.add.removeClass('text-blue-500').addClass('text-blue-700');
        google.addingMarkers = false;
      } else {
        google.map.setOptions({
          draggableCursor: `url(${$('#markerSrc').attr('src')}), auto`
        });
        google.buttons.add.addClass('text-blue-500').removeClass('text-blue-700');
        google.addingMarkers = true;
      }
    }

    function toGeoLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          const pos = new google.core.LatLng(position.coords.latitude,
            position.coords.longitude);

          google.map.setCenter(pos);
          google.map.setZoom(17);

        }, () => {
          handleLocationError(true, google.infoWindow.getPosition());
        });
      } else
        handleLocationError(false, google.map.getCenter());
    }

    function placeMarker(position, data = null, adding = true) {
      const marker = new google.marker.AdvancedMarkerElement({
        position,
        map: google.map,
        // gmpDraggable: true,
        content: buildContent(data)
      });

      google.markers.push(marker);

      if (adding) {
        toggleMarker();
        google.map.panTo(position);
      }

      marker.addListener('click', () => {
        toggleHighlight(marker);
      });
    }

    function toggleHighlight(markerView) {
      if (markerView.content.classList.contains("highlight")) {
        markerView.content.classList.remove("highlight");
        markerView.zIndex = null;
      } else {
        markerView.content.classList.add("highlight");
        markerView.zIndex = 1;
      }
    }

    function buildContent(data) {
      const $wrap = $('<div />', {
        class: 'marker'
      });

      let icon = $('<i />');

      if (data && data.name && data.year) {
        icon.addClass('fa-solid fa-user');
      } else {
        icon.addClass('fa-solid fa-note');
      }

      $wrap.append($('<div />', {
        class: 'icon',
        html: icon
      }));

      if (data) {
        const $detail = $('<div />', {
          class: 'details'
        }).appendTo($wrap);
        $('<div />', {
          class: 'title',
          text: "{{ __('Location Information') }}"
        }).appendTo($detail);

        const address = data.address;
        if (address.number && address.street) {
          const $address = $('<div />', {
            class: 'address'
          }).appendTo($detail);

          $('<div />', {
            class: 'number',
            text: `${address.number} ${address.street}` + (address.town ? `, ${address.town}` : '') + (address.postcode ? `, ${address.postcode}` : '')
          }).appendTo($address);
        }

        if (data.name && data.year) {
          const $name = $('<div />', {
            class: 'name-wrap'
          }).appendTo($detail);

          $('<div />', {
            class: 'name',
            text: data.name
          }).appendTo($name);
          $('<div />', {
            class: 'year',
            text: data.year
          }).appendTo($name);
        }

        if (data.note) {
          $('<div />', {
            class: 'note',
            text: data.note
          }).appendTo($detail);
        }

        const $foot = $('<div />', {
          class: 'footer'
        }).appendTo($detail);

        const $btns = $('<div />', {
          class: 'btns'
        }).appendTo($foot);

        const modal = {
          'title.text': "{{ __('edit location') }}",
          'form.action': "{{ route('info.update') }}",
          'method.value': 'PATCH',
          'id.value': data.id,
          'lat.value': data.position.lat,
          'lng.value': data.position.lng,
          'number.value': data.address.number,
          'street.value': data.address.street,
          'town.value': data.address.town,
          'postcode.value': data.address.postcode,
          'name.value': data.name,
          'year.value': data.year,
          'note.value': data.note,
          'destroy.removeclass': 'hidden',
          'destroy.data': {
            'modal': {
              'id.value': data.id,
            },
          },
          'submit.text': "{{ __('save') }}"
        };
        if (!K.isIn(data.creator, ["{{ K::user()->name }}", 'Kraig Larner'])) {
          modal['destroy.addclass'] = 'hidden';
          modal['destroy.data'] = {
            'modal': {
              'id.value': '',
            }
          };
        }

        $('<i />', {
          class: 'edit fa-regular fa-edit cursor-pointer text-orange-500'
        }).appendTo($btns).on('click', function(e) {
          e.stopPropagation();
          $('#editBtn').data('modal', modal).trigger('click');
        });

        if (data.changes.length) {
          $('<i />', {
            class: 'changes far fa-rotate cursor-pointer text-green-600'
          }).appendTo($btns).on('click', function(e) {
            e.stopPropagation();
            $('.changesBtn').data('modal', {
              'title.text': "{{ __('changes') }}",
              'tbody.changes': data.changes,
            }).trigger('click');
          });
        }

        $('<div />', {
          class: 'creator',
          text: data.creator
        }).appendTo($foot);
      }

      return $wrap[0];
    }

    function createControl(icon, title) {
      const $btn = $('<button />', {
        class: icon + ' text-center text-blue-700 hover:text-blue-400 text-xl w-10 h-10 transition',
        title,
        type: 'button'
      });

      return $btn;
    }

    function handleLocationError(browserHasGeolocation, pos) {
      google.infoWindow.setPosition(pos);
      google.infoWindow.setContent(
        browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.'
      );
      google.infoWindow.open(google.map);
    }

    function trackLocation({
      onSuccess,
      onError = () => {}
    }) {
      if ('geolocation' in navigator === false)
        return onError(new Error('Geolocation is not supported by this browser.'));

      return navigator.geolocation.watchPosition(onSuccess, onError);
    }
  </script>

  <style>
    :root {
      --note-color: #FF9800;
      --user-color: #0288D1;
    }

    /*
    * marker styles in unhighlighted state.
    */
    .marker {
      align-items: center;
      background-color: #FFFFFF;
      border-radius: 50%;
      color: #263238;
      display: flex;
      font-size: 14px;
      gap: 15px;
      height: 36px;
      justify-content: center;
      padding: 4px;
      position: relative;
      transition: all 0.3s ease-out;
      width: 36px;
    }

    .marker::after {
      border-left: 9px solid transparent;
      border-right: 9px solid transparent;
      border-top: 9px solid #FFFFFF;
      content: "";
      height: 0;
      left: 50%;
      position: absolute;
      bottom: -6px;
      transform: translate(-50%, 0);
      transition: all 0.3s ease-out;
      width: 0;
      z-index: 1;
    }

    .marker .icon {
      align-items: center;
      display: flex;
      justify-content: center;
      color: #FFFFFF;
      font-size: 16px;
    }

    .marker .details {
      display: none;
      transition: all 0.3s ease-out;
      flex-direction: column;
      flex: 1;
      gap: 5px;
    }

    .marker .address {
      color: #858585;
      font-size: 10px;
      border-bottom: 1px solid #E0E0E0;
      padding-bottom: 5px;
    }

    .marker .note {
      color: #858585;
      font-size: 11px;
      max-width: 200px;
    }

    .marker .name-wrap {
      display: flex;
      gap: 10px;
      justify-content: space-between;
    }

    .marker .footer {
      display: flex;
      gap: 5px;
      margin-top: 2px;
      justify-content: space-between;
      align-items: center;
    }

    .marker .creator {
      color: #858585;
      font-size: 7px;
    }

    .marker .btns {
      display: flex;
      gap: 5px;
    }

    /*
    * marker styles in highlighted state.
    */
    .marker.highlight {
      background-color: #FFFFFF;
      border-radius: 8px;
      box-shadow: 10px 10px 5px rgba(0, 0, 0, 0.2);
      height: auto;
      padding: 8px 15px;
      width: auto;
    }

    .marker.highlight::after {
      border-top: 9px solid #FFFFFF;
    }

    .marker.highlight .details {
      display: flex;
    }

    .marker.highlight .icon {
      font-size: 32px
    }

    /*
    * note icon colors.
    */
    .marker.highlight:has(.fa-note) .icon {
      color: var(--note-color);
    }

    .marker:not(.highlight):has(.fa-note) {
      background-color: var(--note-color);
    }

    .marker:not(.highlight):has(.fa-note)::after {
      border-top: 9px solid var(--note-color);
    }

    /*
    * user icon colors.
    */
    .marker.highlight:has(.fa-user) .icon {
      color: var(--user-color);
    }

    .marker:not(.highlight):has(.fa-user) {
      background-color: var(--user-color);
    }

    .marker:not(.highlight):has(.fa-user)::after {
      border-top: 9px solid var(--user-color);
    }
  </style>
</x-layout.app>
