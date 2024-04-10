@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-refuel">
  {{-- modal content --}}
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('refuel.bulk', $vehicle->id) }}">
    @csrf
    @method('patch')

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div class="font-extralight uppercase tracking-wider"
        ref="title">
        {{ Msg::bulkTitle(__('refuels')) }}
      </div>
    </div>

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'cost', 'mileage']) }}
      </p>
      <p class="text-sm">
        {{ __('Make sure the data is only for the currently selected vehicle.') }}
      </p>
    </div>

    {{-- file input --}}
    @define($key = 'file')
    <x-form.wrap :key="$key"
      :value="__('CSV')"
      left="left-[5.75rem]">

      <x-form.file class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="file"
        accept="text/csv" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('cancel') }}
      </x-button.light>

      <x-button.dark ref="submit">
        {{ __('upload') }}
      </x-button.dark>
    </div>

  </form>
</x-modal>
