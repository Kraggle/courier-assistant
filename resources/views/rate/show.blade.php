@php
  $user = K::user();
  $dsp = $user->dsp();
  $rates = $dsp->rates;
  $last_route = $user->route();
@endphp

<x-layout.app title="pay rates">

  <x-section.one class="px-0 md:px-0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        {{ "$dsp->name " . 'pay rates' }}
      </x-slot>

      <x-slot:buttons>

        <x-button.dark class="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
          id="addRate"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('rate'),
              'form.action' => route('rate.add'),
              'date.value' => old('date', now()->format('Y-m-d')),
              'type.value' => old('type', '-1'),
              'depot_id.value' => old('depot_id', $last_route->depot_id ?? ($user->options->depot_id ?? null)),
              'amount.value' => old('amount', ''),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'add',
          ]) }}"
          open-modal="add-rate">
          <span class="hidden sm:block">{{ Msg::add('rate') }}</span>
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
              open-modal="bulk-rates">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('rates'),
                  'question.text' => Msg::exportQuestion('rates'),
                  'form.action' => route('rate.export'),
                  'form.filename' => 'rates-' . $user->id . '.csv',
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
        value="{{ route('rate.get') }}">

      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden">

        <x-table.thead>
          @php
            $by = $_GET['by'] ?? 'date';
            $dir = $_GET['dir'] ?? 'desc';
          @endphp

          <x-table.th class="pr-2">
            <x-button.sort data-by="date"
              :active="$by === 'date'"
              :dir="$dir">from date</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="vehicle"
              :active="$by === 'vehicle'"
              :dir="$dir">type</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="cost"
              :active="$by === 'cost'"
              :dir="$dir">rate</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="mileage"
              :active="$by === 'mileage'"
              :dir="$dir">depot</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="miles"
              :active="$by === 'miles'"
              :dir="$dir">creator</x-button.sort>
          </x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>
        </x-table.thead>

        <tbody id="pushRows">

          <x-table.tr class="keep skip-tooltip hidden cursor-pointer"
            open-modal="add-rate"
            is="row">

            {{-- date --}}
            <x-table.td class="date whitespace-nowrap"></x-table.td>

            {{-- type --}}
            <x-table.td class="type"></x-table.td>

            {{-- rate --}}
            <x-table.td class="amount"></x-table.td>

            {{-- depot --}}
            <x-table.td class="depot_identifier"></x-table.td>

            {{-- creator --}}
            <x-table.td class="creator"></x-table.td>

            {{-- actions --}}
            <x-table.td class="text-base sm:text-xl">
              <div class="flex justify-end gap-4">
                <x-icon class="hide-changes far fa-rotate cursor-pointer text-green-600"
                  data-tooltip-position="left"
                  title="{{ Str::title('changes') }}"
                  open-modal="changes-modal" />

                <x-icon class="far fa-edit cursor-pointer text-orange-400"
                  data-tooltip-position="left"
                  title="{{ Str::title('edit') }}" />
              </div>
            </x-table.td>

            {{-- <span class="hidden sm:inline pl-1 text-xs text-gray-400">(per mile)</span> --}}

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

      @if (!$user->hasDSP())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults('rates') }}</div>
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

  @include('rate.modal.add')
  @include('rate.modal.bulk')
  @include('rate.modal.destroy')
  @include('modal.changes')
  @include('modal.export')

  @vite(['resources/js/table.js'])
</x-layout.app>
