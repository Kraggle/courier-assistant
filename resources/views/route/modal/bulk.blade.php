@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-route">
  {{-- modal content --}}
  <form x-ref="form"
    class="{{ $gap }} flex flex-col"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('route.bulk') }}">
    @csrf

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div x-ref="title"
        class="font-extralight uppercase tracking-wider">
        {{ Msg::bulkTitle(__('routes')) }}
      </div>
    </div>

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'start_time', 'end_time', 'start_mileage', 'end_mileage'], ['invoice_mileage', 'bonus', 'stops', 'type', 'depot_id', 'ttfs', 'vat']) }}
      </p>
      <p class="text-sm">
        {{ __('If :type is missing, it will just be set as :work.', ['type' => '`type`', 'work' => '`Standard`']) }}
      </p>
      <p class="text-sm">
        {{ __('By selecting the depot you work out of below, if you are importing data that was NOT exported from this site, it will add these routes to that depot.') }}
      </p>
    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- file input --}}
      @define($key = 'file')
      <x-form.wrap :key="$key"
        :value="__('CSV')"
        left="left-[5.75rem]">

        <x-form.file x-ref="file"
          class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          accept="text/csv" />

      </x-form.wrap>

      {{-- depot --}}
      @define($key = 'depot_id')
      <x-form.wrap :key="$key"
        :value="__('depot')">

        <x-form.select x-ref="{{ $key }}"
          name="{{ $key }}"
          placeholder="{{ __('Please select...') }}">

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

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('cancel') }}
      </x-button.light>

      <x-button.dark x-ref="submit">
        {{ __('upload') }}
      </x-button.dark>
    </div>

  </form>
</x-modal>
