{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-refuel">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('refuel.bulk', $vehicle->id) }}">
    @csrf
    @method('patch')

    {{-- modal header --}}
    <x-modal.header :title="Msg::bulkTitle('refuels')" />

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'cost', 'mileage']) }}
      </p>
      <p class="text-sm">
        Make sure the data is only for the currently selected vehicle.
      </p>
    </div>

    {{-- file input --}}
    @define($key = 'file')
    <x-form.wrap value="CSV"
      :key="$key"
      left="left-[5.75rem]">

      <x-form.file id="{{ $key }}"
        name="{{ $key }}"
        ref="file"
        accept="text/csv" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader>
        <x-slot:text
          ref="submit">upload</x-slot>
        <x-slot:loader></x-slot>
      </x-button.loader>
    </div>

  </form>
</x-modal>
