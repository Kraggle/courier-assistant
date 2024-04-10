@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-route"
  help-root>
  {{-- modal content --}}
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('route.add') }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add(__('route'))"
      :help="true" />

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('route type')"
        :help="__('This is used to determine the day rate for the route.')">

        <x-form.select id="{{ $key }}_route"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="{{ __('Select your type...') }}"
          :value="old($key)"
          minresultsforsearch=999>

          @php
            $types = Lists::routeTypes();
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
        :help="__('The depot you\'re working out of for this route. Also used to determine the day rate for the route. If the depot you\'re working out of is not present, click the `Not Found` button to add it yourself.')">

        <x-form.select id="{{ $key }}_route"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="{{ __('Select your depot...') }}"
          :value="old($key)">

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

    {{-- date --}}
    @define($key = 'date')
    <x-form.wrap class="required"
      :key="$key"
      :value="__('date')"
      :help="__('The date you worked the route.')">

      <x-form.date class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        :value="old($key)" />

    </x-form.wrap>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- time start --}}
      @define($key = 'start_time')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('start time')"
        :help="__('The time you were there to load your vehicle.')">

        <x-form.time class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          :placeholder="__('Select your start time...')"
          :value="old($key)" />

      </x-form.wrap>

      {{-- time end --}}
      @define($key = 'end_time')
      <x-form.wrap :key="$key"
        :value="__('end time')"
        :help="__('Set this as the time you do or should arrive back at the depot. This makes it more accurate for hourly rates.')">

        <x-form.time class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          :value="old($key)"
          placeholder="{{ __('Leave until route ended!') }}" />

      </x-form.wrap>

    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- mileage start --}}
      @define($key = 'start_mileage')
      <x-form.wrap class="required"
        :key="$key"
        :value="__('start mileage')"
        :help="__('The odometer reading when you arrive at the depot. The `+` section will add that figure to the mileage. Useful if you fill in before getting to the destination.')">

        <div class="flex items-center gap-2">
          <x-form.text class="flex-grow"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :placeholder="__('Odometer reading at depot...')"
            :value="old($key)" />

          <x-icon class="fas fa-plus" />

          <x-form.text class="w-[40px] text-center"
            id="{{ $key }}_plus"
            name="{{ $key }}_plus"
            type="number"
            title="{{ Str::title(__('Amount to add to entered mileage.')) }}"
            ref="{{ $key }} . '_plus'" />
        </div>

      </x-form.wrap>

      {{-- mileage end --}}
      @define($key = 'end_mileage')
      <x-form.wrap :key="$key"
        :value="__('end mileage')"
        :help="__('The odometer reading for the end of your route, when you have returned to the depot. If you don\'t go back, set it to what it would have been back at the depot. This helps for accurate fuel costs and earnings.')">

        <div class="flex items-center gap-2">
          <x-form.text class="flex-grow"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :value="old($key)" />

          <x-icon class="fas fa-plus" />

          <x-form.text class="w-[40px] text-center"
            id="{{ $key }}_plus"
            name="{{ $key }}_plus"
            type="number"
            title="{{ Str::title(__('Amount to add to entered mileage.')) }}"
            ref="{{ $key }} . '_plus'" />
        </div>

      </x-form.wrap>
    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-3">
      {{-- invoice mileage --}}
      @define($key = 'invoice_mileage')
      <x-form.wrap :key="$key"
        :value="__('invoiced miles')"
        :help="__('Once you receive your invoice enter the mileage amazon have paid you for the route. This shows an accurate total for earnings when coupled with the fuel rate for the week.')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          :value="old($key)"
          placeholder="{{ __('Get this from your invoice!') }}" />

      </x-form.wrap>

      @define($key = 'bonus')
      {{-- bonus --}}
      <x-form.wrap :key="$key"
        :value="__('bonus')"
        :help="__('Add any bonuses or subtractions to this input, it will calculate correctly for the total for the route.')">

        <x-form.text-prefix class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          :value="old($key)"
          step="0.01">

          <x-icon class="fas fa-sterling-sign text-gray-400" />

        </x-form.text-prefix>

      </x-form.wrap>

      @define($key = 'vat')
      {{-- vat --}}
      <x-form.wrap :key="$key"
        :value="__('Claiming VAT?')"
        :help="__('You you claim VAT from amazon mark this as Yes! It will calculate the extra pay for you.')">

        <x-form.toggle class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          :value="old($key)" />

      </x-form.wrap>
    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      {{-- ttfs --}}
      @define($key = 'ttfs')
      <x-form.wrap :key="$key"
        :value="__('Minutes to First Stop')"
        :help="__('Will give you a better result for your stops per hour. This is subtracted from the total time of your route before dividing by stops.')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          :value="old($key)" />

      </x-form.wrap>

      {{-- stops --}}
      @define($key = 'stops')
      <x-form.wrap :key="$key"
        :value="__('Total Stops or Locations')"
        :help="__('This is totally optional, but it will show you your stops/locations per hour and give you an overall average.')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}"
          :value="old($key)"
          placeholder="{{ __('Used to determine stops per hour!') }}" />

      </x-form.wrap>
    </div>

    {{-- note --}}
    @define($key = 'note')
    <x-form.wrap :key="$key"
      :value="__('note')"
      :help="__('You may want to remind yourself of something about this route. You can write that here.')">

      <x-form.text class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        :value="old($key)" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-route')"
        class="no-loader"
        ref="destroy">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

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

@pushOnce('scripts')
  <script type="module">
    $(() => {
      const $mile = $('[ref=start_mileage]');
      $('#type_route').on('change', function() {
        $mile.closest('div[key]')[$(this).val() === 'poc' ? 'removeClass' : 'addClass']('required');
      }).trigger('change');
    });
  </script>
@endpushOnce
