<x-layout.app center="true">

  @if (!$user->hasDSP())

    <x-section.one maxWidth="md">
      <form class="flex flex-col"
        help-root
        method="POST"
        action="{{ route('dsp.attach') }}">
        @csrf
        @method('PATCH')

        <x-section.title>
          <x-slot:title>
            Select your DSP!
          </x-slot>

          <x-slot:buttons>
            <x-icon class="far fa-circle-question cursor-pointer"
              data-help-trigger="false"
              title="Toggle help text!" />
          </x-slot>
        </x-section.title>

        {{-- dsp_id --}}
        @define($key = 'dsp_id')
        <x-form.wrap value="DSP"
          :key="$key"
          help="Search here for the name of the Delivery Service Provider you work for. It's important you select the correct one, as they differ in their pay rates. The one you select, if already added, will most likely already have rates set to date.">

          <div class="flex">
            <div class="flex-grow">
              <x-form.select id="{{ $key }}"
                name="{{ $key }}"
                style="width: 100%"
                placeholder="Select your Delivery Service Provider">

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
                  <span class="font-semibold">Not Found!</span>
                </x-slot>

              </x-form.select>
            </div>

            <x-button.light size="xs"
              open-modal="create-dsp">
              new
            </x-button.light>
          </div>
        </x-form.wrap>

        {{-- date --}}
        @define($key = 'date')
        <x-form.wrap value="start date"
          :key="$key"
          help="The date that you started working for this DSP. As you can add many, you will only be able to change anything on the latest one you have selected.">

          <x-form.date id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- submit --}}
        <div class="flex justify-end">
          <x-button.dark>
            choose
          </x-button.dark>
        </div>

      </form>
    </x-section.one>

    @include('dsp.modal.create')
    <x-section.one maxWidth="md">
      <form class="flex flex-col"
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
            Add your day rate!
          </div>

          <x-icon class="far fa-circle-question cursor-pointer"
            data-help-trigger="false"
            title="Toggle help text!" />

        </div>

        <div class="hidden"
          help-message>
          <div class="flex gap-2">
            <x-icon class="far fa-info-circle text-base text-blue-400" />
            <div class="text-xs text-gray-500">
              <p>Here you should add your day rate if you know it. First select the type of route you do, then set the date that rate started at, then the actual rate.</p>
              <p>With these done, when you add your first route, the amount you are being paid will be calculated.</p>
            </div>
          </div>
        </div>

        {{-- type --}}
        @define($key = 'type')
        <x-form.wrap value="rate type"
          :key="$key"
          help="The type of rate you want to add. They are all explained on the selection.">

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

        <div class="grid grid-cols-1 md:grid-cols-2">
          {{-- date --}}
          @define($key = 'date')
          <x-form.wrap value="from date"
            :key="$key"
            help="The date that the rate is started from, just set your first day if you do not know that actual date it started.">

            <x-form.date id="{{ $key }}"
              name="{{ $key }}" />

          </x-form.wrap>

          {{-- amount --}}
          @define($key = 'amount')
          <x-form.wrap value="rate"
            :key="$key"
            help="The rate of the type you are adding.">

            <x-form.text-prefix id="{{ $key }}"
              name="{{ $key }}"
              type="number"
              step="0.0001">

              <x-icon class="fas fa-sterling-sign text-gray-400" />

            </x-form.text-prefix>

          </x-form.wrap>
        </div>

        {{-- submit --}}
        <div class="flex justify-end">
          <x-button.dark>
            add
          </x-button.dark>
        </div>

      </form>
    </x-section.one>
  @else
    <x-section.one maxWidth="md">
      <p class="text-center">You appear to have arrived somewhere you shouldn't have. You clever thing you!</p>
    </x-section.one>
  @endif
</x-layout.app>
