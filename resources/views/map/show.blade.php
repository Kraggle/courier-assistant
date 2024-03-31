@php

@endphp

<x-layout.app :title="__('map test')">

  <style>
    .hover-first :first-child {
      transform: scale(0);
      transform-origin: right;
    }

    .hover-first:hover :first-child {
      transform: scale(1);
    }
  </style>

  <div class="hidden w-[84%] max-w-[500px] rounded-br-md bg-white p-3 font-sans shadow-md"
    id="search-card">
    <div class="flex flex-col gap-2">
      {{-- <div class="text-xl font-bold uppercase"></div> --}}
      <x-form.wrap class="-mt-1"
        id="lookup-field"
        value="{{ __('address lookup') }}">
        <x-form.text class="w-full"
          id="map-search"
          placeholder="Enter a location..." />
      </x-form.wrap>
    </div>
  </div>

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
        'address.value' => old('address', ''),
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
        'address.value' => old('address', ''),
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
    <div class="min-h-[315px] w-full flex-1 overflow-hidden border border-gray-400 bg-white shadow-lg md:rounded-md"
      id="map">

    </div>
  </div>

  @push('modals')
    @include('map.modal.add')
    @include('map.modal.destroy')
    @include('modal.changes')
  @endpush

  @php
    $_K = [
        'str' => [
            'editLocation' => __('edit location'),
            'save' => __('save'),
            'username' => K::user()->name,
            'locationInformation' => __('Location Information'),
        ],
        'route' => [
            'infoUpdate' => route('info.update'),
            'infoLocation' => route('info.location'),
        ],
    ];
  @endphp

  <script type="module">
    window._K = @json($_K);
  </script>
  @vite('resources/js/map.js')

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

    .marker.moving {
      outline: #ffa5003b solid 8px;
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
      text-transform: uppercase;
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

    .marker .fa-copy {
      font-size: 8px;
      margin-left: 4px;
      position: relative;
      top: -3px;
      {{-- color: #40c95d; --}}
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
