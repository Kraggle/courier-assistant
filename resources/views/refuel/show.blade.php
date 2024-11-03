@define($refuels = $user->refuels)

<x-layout.app title="refuels">
  <x-section.one class="px-0 md:px-0">

    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        Refuels
      </x-slot>

      <x-slot:buttons>
        <x-button.dark class="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
          id="addRefuel"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('refuel'),
              'form.action' => route('refuel.add'),
              'vehicle.value' => old('vehicle', $refuels->first()->vehicle->id ?? 0),
              'date.value' => old('date', now()->format('Y-m-d')),
              'mileage.value' => old('mileage', ''),
              'cost.value' => old('cost', ''),
              'first.checked' => old('first', false),
              'image-wrap.set-inputs' => old('image-wrap', ''),
              'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'add',
          ]) }}"
          open-modal="add-refuel">
          <span class="hidden sm:block">{{ Msg::add('refuel') }}</span>
          <span class="block sm:hidden">add</span>
        </x-button.dark>

        <x-dropdown.wrap contentClasses="font-normal py-1 bg-white">
          <x-slot:trigger>
            <x-button.light>
              <x-icon class="far fa-ellipsis-vertical text-xs" />
            </x-button.light>
          </x-slot>

          <x-slot:content>
            {{-- bulk add --}}
            <x-dropdown.link class="cursor-pointer"
              open-modal="bulk-refuel">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('refuels'),
                  'question.text' => Msg::exportQuestion('refuels'),
                  'form.action' => route('refuel.export'),
                  'form.filename' => 'refuel-' . $user->id . '.csv',
              ]) }}"
              open-modal="export-modal">
              Export as CSV
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>
      </x-slot>
    </x-section.title>

    <div class="grid grid-cols-2 px-6 pb-3 sm:grid-cols-4">

      <div class="flex items-center gap-1">
        <span class="text-sm font-bold uppercase">Per Page:</span>
        <x-form.select id="length"
          minresultsforsearch=999>
          <x-slot:options>
            @foreach ([10, 25, 50, 100] as $i)
              <option value="{{ $i }}"
                {{ K::selected($i, $_GET['length'] ?? 25) }}>{{ $i }}</option>
            @endforeach
          </x-slot>
        </x-form.select>
      </div>

      <div class="hidden sm:block"></div>

      <div class="hidden sm:block"></div>

      <x-form.text-prefix class="col-span-2 w-full sm:col-span-1"
        id="search"
        value="{{ $_GET['search'] ?? '' }}"
        placeholder="Filter results...">

        <x-icon class="fal fa-search"></x-icon>

      </x-form.text-prefix>

    </div>

    <div class="overflow-x-auto">

      <input id="assetURL"
        type="hidden"
        value="{{ route('refuel.get') }}">

      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden"
        id="table_id">

        <x-table.thead>
          @php
            $by = $_GET['by'] ?? 'date';
            $dir = $_GET['dir'] ?? 'desc';
          @endphp

          <x-table.th class="pr-2">
            <x-button.sort data-by="date"
              :active="$by === 'date'"
              :dir="$dir">date</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="vehicle"
              :active="$by === 'vehicle'"
              :dir="$dir">vehicle</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="cost"
              :active="$by === 'cost'"
              :dir="$dir">cost</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="mileage"
              :active="$by === 'mileage'"
              :dir="$dir">odometer</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="miles"
              :active="$by === 'miles'"
              :dir="$dir">miles</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="fuel_rate"
              :active="$by === 'fuel_rate'"
              :dir="$dir">ppm</x-button.sort>
          </x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>
        </x-table.thead>

        <tbody id="pushRows">

          <x-table.tr class="keep skip-tooltip hidden cursor-pointer"
            open-modal="add-refuel"
            is="row">

            {{-- date --}}
            <x-table.td class="date whitespace-nowrap"></x-table.td>

            {{-- vehicle --}}
            <x-table.td class="vehicle"></x-table.td>

            {{-- cost --}}
            <x-table.td class="cost"></x-table.td>

            {{-- odometer --}}
            <x-table.td class="mileage"></x-table.td>

            {{-- miles --}}
            <x-table.td class="miles"></x-table.td>

            {{-- ppm --}}
            <x-table.td class="fuel_rate"></x-table.td>

            {{-- actions --}}
            <x-table.td class="text-base sm:text-xl">
              <div class="flex justify-end gap-4">
                <x-icon class="hide-receipt far fa-receipt cursor-pointer"
                  data-tooltip-position="left"
                  title="{{ Str::title('receipt') }}"
                  open-modal="show-receipt" />

                <x-icon class="far fa-edit cursor-pointer text-orange-400"
                  data-tooltip-position="left"
                  title="{{ Str::title('edit') }}" />
              </div>
            </x-table.td>

          </x-table.tr>

          <tr class="keep">
            <td class="text-center"
              colspan="6">
              <x-loader class="pb-6 pt-12"
                id="spinner"
                size="4"
                color="bg-gray-500" />
            </td>
          </tr>
        </tbody>

      </table>

      @if (!$user->hasRefuels())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults('refuels') }}</div>
      @else
        <div class="flex flex-wrap-reverse justify-center px-6 pt-6 sm:flex-nowrap sm:justify-between">
          <div class="whitespace-nowrap"
            id="counter"></div>
          <div class="flex gap-1.5"
            id="pagination"
            data-page="{{ $_GET['page'] ?? 1 }}"></div>
        </div>
      @endif

    </div>

  </x-section.one>

  @include('refuel.modal.add')
  @include('refuel.modal.bulk')
  @include('modal.receipt')
  @include('modal.export')
  @include('refuel.modal.destroy')
  @include('vehicle.modal.add')

  @vite(['resources/js/table.js'])
</x-layout.app>
