{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-rates">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('rate.bulk') }}">
    @csrf
    @method('PATCH')

    {{-- modal header --}}
    <x-modal.header :title="Msg::bulkTitle('rates')" />

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'amount', 'type']) }}
      </p>
      <p class="text-sm">
        'The available `types` are below...
      </p>
      <div class="ml-2 grid grid-cols-[max-content_1fr] items-center gap-x-2">
        @foreach (Lists::rateTypes() as $k => $v)
          <span class="justify-self-end text-sm font-semibold">{{ $k }}:</span>
          <span class="text-xs font-normal">{{ $v }}</span>
        @endforeach
      </div>
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

        <x-form.select name="{{ $key }}"
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
