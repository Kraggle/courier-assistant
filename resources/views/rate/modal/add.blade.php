{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-rate"
  help-root>

  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('rate.add') }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add('rate')"
      :help="true" />

    <div class="hidden"
      help-message>
      <div class="flex gap-2">
        <x-icon class="far fa-info-circle text-base text-blue-400" />
        <div class="text-xs text-gray-500">
          <p>This is where the rates are set per DSP and Depot, as the rates are different for each of these things you have to make sure you select the correct ones.</p>
          <p>These can be created by anyone that has selected the same DSP as yourself. There is a log of who created, updated or deleted any shared record.</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap class="required"
        value="rate type"
        :key="$key"
        help="The type of rate you want to add. They are all explained on the selection.">

        <x-form.select id="{{ $key }}_rate"
          name="{{ $key }}"
          ref="{{ $key }}"
          minresultsforsearch=999
          placeholder="Please select...">

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
        value="depot"
        :key="$key"
        help="The depot the rate is being added for.">

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
            <a href="{{ route('depot.create') }}">Not Found, click to add!</a>
          </x-slot>

        </x-form.select>

      </x-form.wrap>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
      {{-- date --}}
      @define($key = 'date')
      <x-form.wrap class="required"
        value="from date"
        :key="$key"
        help="The date the new rate is starting from.">

        <x-form.date class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}" />

      </x-form.wrap>

      {{-- amount --}}
      @define($key = 'amount')
      <x-form.wrap class="required"
        value="rate"
        :key="$key"
        help="The rate of the type you are adding.">

        <x-form.text-prefix class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          placeholder="Please enter value..."
          step="0.0001">

          <x-icon class="fas fa-sterling-sign text-gray-400" />

        </x-form.text-prefix>

      </x-form.wrap>
    </div>

    <div class="flex justify-between">
      <x-button.danger class="no-loader"
        open-modal="destroy-rate"
        ref="destroy">
        delete
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="flex justify-end">
        <x-button.light close-modal>
          cancel
        </x-button.light>

        <x-button.dark ref="submit">
          add
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
