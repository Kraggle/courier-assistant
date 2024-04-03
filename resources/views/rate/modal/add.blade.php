@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-rate"
  help-root>

  {{-- modal content --}}
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('rate.add') }}">
    @csrf

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div class="font-extralight uppercase tracking-wider"
        ref="title">
        {{ Msg::add(__('route')) }}
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
          <p>{{ __('This is where the rates are set per DSP and Depot, as the rates are different for each of these things you have to make sure you select the correct ones.') }}</p>
          <p>{{ __('These can be created by anyone that has selected the same DSP as yourself. There is a log of who created, updated or deleted any shared record.') }}</p>
        </div>
      </div>
    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('rate type')"
        :help="__('The type of rate you want to add. They are all explained on the selection.')">

        <x-form.select id="{{ $key }}_rate"
          name="{{ $key }}"
          ref="{{ $key }}"
          minresultsforsearch=999
          placeholder="{{ __('Please select...') }}">

          @php
            $types = Lists::rateTypes();
          @endphp
          <x-slot:options>
            @foreach ($types as $key => $type)
              <option value="{{ $key }}">{{ $type }}</option>
            @endforeach
          </x-slot>

        </x-form.select>

      </x-form.wrap>

      {{-- depot --}}
      @define($key = 'depot_id')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('depot')"
        :help="__('The depot the rate is being added for.')">

        <x-form.select id="{{ $key }}_rate"
          name="{{ $key }}"
          ref="{{ $key }}">

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

    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- date --}}
      @define($key = 'date')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('from date')"
        :help="__('The date the new rate is starting from.')">

        <x-form.date class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}" />

      </x-form.wrap>

      {{-- amount --}}
      @define($key = 'amount')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('rate')"
        :help="__('The rate of the type you are adding.')">

        <x-form.text-prefix class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          :placeholder="__('Please enter value...')"
          step="0.0001">

          <x-icon class="fas fa-sterling-sign text-gray-400" />

        </x-form.text-prefix>

      </x-form.wrap>
    </div>

    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-rate')"
        class="no-loader"
        ref="destroy">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-on:click="$dispatch('close')">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark ref="submit">
          {{ __('add') }}
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
