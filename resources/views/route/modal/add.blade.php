{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-route"
  help-root>
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('route.add') }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add('route')"
      :help="true" />

    <div class="grid grid-cols-1 md:grid-cols-2">
      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap class="required"
        value="route type"
        :key="$key"
        help="This is used to determine the day rate for the route.">

        <x-form.select id="{{ $key }}_route"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="Select your type..."
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
        value="depot"
        :key="$key"
        help="The depot you're working out of for this route. Also used to determine the day rate for the route. If the depot you're working out of is not present, click the `Not Found` button to add it yourself.">

        <div class="flex">
          <div class="flex-grow">
            <x-form.select id="{{ $key }}_route"
              name="{{ $key }}"
              ref="{{ $key }}"
              placeholder="Select your depot..."
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
                <div>Not found!</div>
              </x-slot>

            </x-form.select>
          </div>

          <x-button.light size="xs"
            open-modal="add-depot">
            new
          </x-button.light>
        </div>

      </x-form.wrap>

    </div>

    <div class="grid grid-cols-3">
      {{-- date --}}
      @define($key = 'date')
      <x-form.wrap class="required col-span-3 md:col-span-1"
        value="date"
        :key="$key"
        help="The date you worked the route.">

        <x-form.date id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          :value="old($key)" />

      </x-form.wrap>

      <div class="col-span-3 grid grid-cols-2 md:col-span-2">
        {{-- time start --}}
        @define($key = 'start_time')
        <x-form.wrap class="required"
          value="start time"
          :key="$key"
          help="The time you were there to load your vehicle.">

          <x-form.time id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}"
            placeholder="Select your start time..."
            :value="old($key)" />

        </x-form.wrap>

        {{-- time end --}}
        @define($key = 'end_time')
        <x-form.wrap value="end time"
          :key="$key"
          help="Set this as the time you do or should arrive back at the depot. This makes it more accurate for hourly rates.">

          <x-form.time id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}"
            :value="old($key)"
            placeholder="Leave until route ended!" />

        </x-form.wrap>

      </div>
    </div>

    <div class="grid grid-cols-2">
      {{-- mileage start --}}
      @define($key = 'start_mileage')
      <x-form.wrap class="required"
        value="start mileage"
        :key="$key"
        help="The odometer reading when you arrive at the depot. The `+` section will add that figure to the mileage. Useful if you fill in before getting to the destination.">

        <div class="flex items-center gap-2">
          <x-form.text class="flex-grow"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            placeholder="Odometer reading at depot..."
            :value="old($key)" />

          <x-icon class="fas fa-plus" />

          <x-form.text class="w-[40px] text-center"
            id="{{ $key }}_plus"
            name="{{ $key }}_plus"
            type="number"
            title="{{ Str::title('Amount to add to entered mileage.') }}"
            ref="{{ $key }} . '_plus'" />
        </div>

      </x-form.wrap>

      {{-- mileage end --}}
      @define($key = 'end_mileage')
      <x-form.wrap value="end mileage"
        :key="$key"
        help="The odometer reading for the end of your route, when you have returned to the depot. If you don't go back, set it to what it would have been back at the depot. This helps for accurate fuel costs and earnings.">

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
            title="{{ Str::title('Amount to add to entered mileage.') }}"
            ref="{{ $key }} . '_plus'" />
        </div>

      </x-form.wrap>
    </div>

    <div class="flex flex-col"
      ref="more"
      hide="more">
      <div class="grid grid-cols-2 md:grid-cols-3">
        {{-- invoice mileage --}}
        @define($key = 'invoice_mileage')
        <x-form.wrap class="col-span-2 md:col-span-1"
          value="invoiced miles"
          :key="$key"
          help="Once you receive your invoice enter the mileage amazon have paid you for the route. This shows an accurate total for earnings when coupled with the fuel rate for the week.">

          <x-form.text id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :value="old($key)"
            placeholder="Get this from your invoice!" />

        </x-form.wrap>

        @define($key = 'bonus')
        {{-- bonus --}}
        <x-form.wrap value="bonus"
          :key="$key"
          help="Add any bonuses or subtractions to this input, it will calculate correctly for the total for the route.">

          <x-form.text-prefix id="{{ $key }}"
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
        <x-form.wrap value="Claiming VAT?"
          :key="$key"
          help="You you claim VAT from amazon mark this as Yes! It will calculate the extra pay for you.">

          <x-form.toggle id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}"
            :value="old($key)" />

        </x-form.wrap>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2">
        {{-- ttfs --}}
        @define($key = 'ttfs')
        <x-form.wrap value="Minutes to First Stop"
          :key="$key"
          help="Will give you a better result for your stops per hour. This is subtracted from the total time of your route before dividing by stops.">

          <x-form.text id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :value="old($key)" />

        </x-form.wrap>

        {{-- stops --}}
        @define($key = 'stops')
        <x-form.wrap value="Total Stops or Locations"
          :key="$key"
          help="This is totally optional, but it will show you your stops/locations per hour and give you an overall average.">

          <x-form.text id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :value="old($key)"
            placeholder="Used to determine stops per hour!" />

        </x-form.wrap>
      </div>

      {{-- note --}}
      @define($key = 'note')
      <x-form.wrap value="note"
        :key="$key"
        help="You may want to remind yourself of something about this route. You can write that here.">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          :value="old($key)" />

      </x-form.wrap>
    </div>

    <div class="flex justify-center">
      <x-button.light class="border-0 shadow-none"
        ref="more-btn"
        hide-id="more"
        size="sm">Show More</x-button.light>
    </div>

    {{-- submit --}}
    <div class="flex justify-between">
      <x-button.danger class="no-loader"
        open-modal="destroy-route"
        ref="destroy">
        delete
      </x-button.danger>

      <span></span>

      <div class="flex justify-end">
        <x-button.light close-modal>
          cancel
        </x-button.light>

        <x-button.loader>
          <x-slot:text
            ref="submit">add</x-slot>
          <x-slot:loader></x-slot>
        </x-button.loader>
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
