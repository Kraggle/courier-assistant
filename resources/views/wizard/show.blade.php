@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

<x-layout.app center="true">

  @if (!$user->hasDSP())

    <x-section.wrap maxWidth="md">
      <form class="{{ $gap }} flex flex-col"
        method="POST"
        action="{{ route('dsp.attach') }}">
        @csrf
        @method('PATCH')

        <div class="flex items-center justify-between">
          <div class="font-serif text-xl">
            {{ __('Select your DSP!') }}
          </div>

          <x-icon class="far fa-circle-question cursor-pointer"
            data-help-trigger="false"
            :title="__('Toggle help text!')" />

        </div>

        {{-- dsp_id --}}
        @define($key = 'dsp_id')
        <x-form.wrap :key="$key"
          :value="__('DSP')"
          :help="__('Search here for the name of the Delivery Service Provider you work for. It\'s important you select the correct one, as they differ in their pay rates. The one you select, if already added, will most likely already have rates set to date.')">

          <x-form.select id="{{ $key }}"
            name="{{ $key }}"
            style="width: 100%"
            :placeholder="__('Select your Delivery Service Provider')">

            <x-slot:elements>

              @foreach (\App\Models\DSP::all()->sortBy('name') as $dsp)
                <div class="flex items-center justify-between"
                  value="{{ $dsp->id }}">
                  <span>{{ $dsp->name }}</span>
                  <span class="text-base font-semibold">{{ $dsp->identifier }}</span>
                </div>
              @endforeach

            </x-slot>

            <x-slot:noresults>
              <span class="font-semibold">{{ __('Not Found, please add below!') }}</span>
            </x-slot>

          </x-form.select>

        </x-form.wrap>

        {{-- date --}}
        @define($key = 'date')
        <x-form.wrap :key="$key"
          :value="__('start date')"
          :help="__('The date that you started working for this DSP. As you can add many, you will only be able to change anything on the latest one you have selected.')">

          <x-form.date class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- submit --}}
        <div class="{{ $gap }} flex justify-end">
          <x-button.dark>
            {{ __('choose') }}
          </x-button.dark>
        </div>

      </form>
    </x-section.wrap>

    <x-section.wrap maxWidth="md">
      <form class="{{ $gap }} flex flex-col"
        method="POST"
        action="{{ route('dsp.create') }}">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between">
          <div class="font-serif text-xl">
            {{ __('OR ... add it here!') }}
          </div>
        </div>

        {{-- name --}}
        @define($key = 'name')
        <x-form.wrap :key="$key"
          :value="__('DSPs Name')"
          :help="__('The name of your Delivery Service Provider. Please be accurate with this as anyone else searching for your DSP will want to find it easily. Also if there is any profanity found the DSP will be removed and you will loose any data added.')">

          <x-form.text class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- identifier --}}
        @define($key = 'identifier')
        <x-form.wrap :key="$key"
          :value="__('Amazon identifier')"
          :help="__('Amazons identifier for your Delivery Service Provider, you can ask your OSM for this if you don\'t know it. It\'s another way for other drivers to find the correct DSP.')">

          <x-form.text class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            placeholder="{{ __('e.g. CLBT, LWTS, ROKL, GAMD') }}" />

        </x-form.wrap>

        {{-- submit --}}
        <div class="{{ $gap }} flex justify-end">
          <x-button.dark>
            {{ __('create') }}
          </x-button.dark>
        </div>

      </form>
    </x-section.wrap>
  @elseif (!$user->hasVehicle())
    <x-section.wrap maxWidth="md">
      <form class="{{ $gap }} flex flex-col"
        method="POST"
        help-root
        action="{{ route('vehicle.create') }}">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between">
          <div class="font-serif text-xl">
            {{ __('Add your first vehicle!') }}
          </div>

          <x-icon class="far fa-circle-question cursor-pointer"
            data-help-trigger="false"
            :title="__('Toggle help text!')" />

        </div>

        {{-- reg --}}
        @define($key = 'reg')
        <x-form.wrap :key="$key"
          :value="__('Vehicle Regestration')"
          :help="__('We separate each vehicle for refuels, this makes it easier to see the difference in your fuel costs, like if one vehicle ran cheaper than another.')">

          <x-form.text class="block w-full text-center text-2xl font-extrabold uppercase"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- submit --}}
        <div class="flex justify-end">
          <x-button.dark>
            {{ __('add') }}
          </x-button.dark>
        </div>

      </form>
    </x-section.wrap>
  @elseif(!$user->hasDepot())
    <div class="flex flex-col gap-[inherit]"
      help-root>
      <x-section.wrap maxWidth="md">
        <form class="{{ $gap }} flex flex-col"
          method="POST"
          action="{{ route('user.options') }}">
          @csrf

          <div class="flex items-center justify-between">
            <div class="font-serif text-xl">
              {{ __('Select your Depot!') }}
            </div>

            <x-icon class="far fa-circle-question cursor-pointer"
              data-help-trigger="false"
              :title="__('Toggle help text!')" />

          </div>

          {{-- depot --}}
          @define($key = 'depot_id')
          <x-form.wrap :key="$key"
            :value="__('depot')"
            :help="__('The depot that you work out of, this can be changed per route, but it is better to have a default so you don\'t need to change it each time you add a new route.')">

            <x-form.select id="{{ $key }}_rate"
              name="{{ $key }}"
              style="width: 100%">

              <x-slot:elements>

                @foreach (\App\Models\Depot::all()->sortBy('location') as $depot)
                  <div class="flex items-center justify-between"
                    value="{{ $depot->id }}">
                    <span>{{ $depot->location }}</span>
                    <span class="text-base font-semibold">{{ $depot->identifier }}</span>
                  </div>
                @endforeach

              </x-slot>

              <x-slot:noresults>
                <a href="{{ route('depot.create') }}">{{ __('Not Found, click to add!') }}</a>
              </x-slot>

            </x-form.select>

          </x-form.wrap>

          {{-- submit --}}
          <div class="{{ $gap }} flex justify-end">
            <x-button.dark>
              {{ __('choose') }}
            </x-button.dark>
          </div>

        </form>
      </x-section.wrap>

      <x-section.wrap maxWidth="md">
        <form class="{{ $gap }} flex flex-col"
          method="POST"
          action="{{ route('depot.store') }}">
          @csrf
          @method('PUT')

          {{-- split header --}}
          <div class="flex items-center justify-between">
            <div class="font-serif text-xl">
              {{ __('OR ... add it here!') }}
            </div>
          </div>

          {{-- location --}}
          @define($key = 'location')
          <x-form.wrap :key="$key"
            :value="__('Depot location')"
            :help="__('The location of your depot, for instance this could be Chester, Liverpool, Warrington etc...')">

            <x-form.text class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              placeholder="e.g. Chester, Liverpool..." />

          </x-form.wrap>

          {{-- identifier --}}
          @define($key = 'identifier')
          <x-form.wrap :key="$key"
            :value="__('Amazon identifier')"
            :help="__('Amazons identifier for your depot, you can ask your OSM for this if you don\'t know it. It\'s another way for other drivers to find the correct depot.')">

            <x-form.text class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              placeholder="e.g. DCE1, DXM4..." />

          </x-form.wrap>

          {{-- submit --}}
          <div class="{{ $gap }} flex justify-end">
            <x-button.dark>
              {{ __('create') }}
            </x-button.dark>
          </div>

        </form>
      </x-section.wrap>
    </div>
  @elseif(!$user->hasRate())
    <x-section.wrap maxWidth="md">
      <form class="{{ $gap }} flex flex-col"
        help-root
        method="POST"
        action="{{ route('rate.add') }}">
        @csrf
        @method('PUT')

        {{-- depot --}}
        @define($key = 'depot_id')
        <input name="{{ $key }}"
          type="hidden"
          value="{{ $user->options->depot_id }}">

        <div class="flex items-center justify-between">
          <div class="font-serif text-xl">
            {{ __('Add your day rate!') }}
          </div>

          <x-icon class="far fa-circle-question cursor-pointer"
            data-help-trigger="false"
            :title="__('Toggle help text!')" />

        </div>

        <div class="hidden"
          help-message>
          <div class="flex gap-2">
            <x-icon class="far fa-info-circle text-base text-blue-400" />
            <div class="text-xs text-gray-500">
              <p>{{ __('Here you should add your day rate if you know it. First select the type of route you do, then set the date that rate started at, then the actual rate.') }}</p>
              <p>{{ __('With these done, when you add your first route, the amount you are being paid will be calculated.') }}</p>
            </div>
          </div>
        </div>

        {{-- type --}}
        @define($key = 'type')
        <x-form.wrap :key="$key"
          :value="__('rate type')"
          :help="__('The type of rate you want to add. They are all explained on the selection.')">

          <x-form.select id="{{ $key }}_rate"
            name="{{ $key }}"
            style="width: 100%"
            minresultsforsearch=999>

            <x-slot:options>
              @foreach (Lists::routeTypes() as $key => $type)
                <option value="{{ $key }}"
                  {{ $key == 'md' ? 'selected' : '' }}>
                  {{ $type }}
                </option>
              @endforeach
            </x-slot>

          </x-form.select>

        </x-form.wrap>

        <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
          {{-- date --}}
          @define($key = 'date')
          <x-form.wrap :key="$key"
            :value="__('from date')"
            :help="__('The date that the rate is started from, just set your first day if you do not know that actual date it started.')">

            <x-form.date class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}" />

          </x-form.wrap>

          {{-- amount --}}
          @define($key = 'amount')
          <x-form.wrap :key="$key"
            :value="__('rate')"
            :help="__('The rate of the type you are adding.')">

            <x-form.text-prefix class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              type="number"
              step="0.0001">

              <x-icon class="fas fa-sterling-sign text-gray-400" />

            </x-form.text-prefix>

          </x-form.wrap>
        </div>

        {{-- submit --}}
        <div class="{{ $gap }} flex justify-end">
          <x-button.dark>
            {{ __('add') }}
          </x-button.dark>
        </div>

      </form>
    </x-section.wrap>
  @else
    <x-section.wrap maxWidth="md">
      <p class="text-center">You appear to have arrived somewhere you shouldn't have. You clever thing you!</p>
    </x-section.wrap>
  @endif
</x-layout.app>
