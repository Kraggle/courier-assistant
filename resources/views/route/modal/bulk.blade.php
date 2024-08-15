{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-route">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('route.bulk') }}">
    @csrf
    @method('PATCH')

    {{-- modal header --}}
    <x-modal.header :title="Msg::bulkTitle('routes')" />

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'start_time', 'end_time', 'start_mileage', 'end_mileage'], ['invoice_mileage', 'bonus', 'stops', 'type', 'depot_id', 'ttfs', 'vat']) }}
      </p>
      <p class="text-sm">
        If `type` is missing, it will just be set as `Standard`.
      </p>
      <p class="text-sm">
        By selecting the depot you work out of below, if you are importing data that was NOT exported from this site, it will add these routes to that depot.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
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

      {{-- depot --}}
      @define($key = 'depot_id')
      <x-form.wrap value="depot"
        :key="$key">

        <x-form.select id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="Please select...">

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
            <a href="{{ route('depot.create') }}">Not Found, click to add!</a>
          </x-slot>

        </x-form.select>

      </x-form.wrap>

    </div>

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
