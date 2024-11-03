<x-layout.app title="expenses">
  <x-section.one class="px-0 md:px-0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        expenses
      </x-slot>

      <x-slot:buttons>

        @define($eTest = null)
        <x-button.dark class="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
          id="addExpense"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('expense'),
              'form.action' => route('expense.add'),
              'form.mode' => 'add',
              'choice-wrap.addclass' => 'hidden',
              'choice.value' => 'this',
              'date.value' => old('date', now()->format('Y-m-d')),
              'type.value' => old('type', $eTest ? 'interest' : '-1'),
              'date_to.value' => old('date_to', $eTest ? now()->add(2, 'month')->format('Y-m-d') : now()->add(1, 'year')->format('Y-m-d')),
              'repeat.value' => old('repeat', $eTest ?? 'never'),
              'every.value' => old('every', $eTest ?? 'week'),
              'every_x.value' => old('every_x', '1'),
              'month.value' => old('month', 'day'),
              'describe.value' => old('describe', $eTest ? 'TEST' : ''),
              'cost.value' => old('cost', $eTest ? 9.99 : null),
              'image-wrap.set-inputs' => old('image', ''),
              'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'add',
              'is-repeat.value' => 0,
          ]) }}"
          open-modal="add-expense">
          <span class="hidden sm:block">{{ Msg::add('expense') }}</span>
          <span class="block sm:hidden">add</span>
        </x-button.dark>

        <x-dropdown.wrap contentClasses="font-normal py-1 bg-white">
          <x-slot:trigger>
            <x-button.light>
              <x-icon class="far fa-ellipsis-vertical text-xs" />
            </x-button.light>
          </x-slot>

          <x-slot:content>
            {{-- toggle all --}}
            {{-- <x-dropdown.link class="cursor-pointer"
              href="\expense?future=1">
              Show future expenses
            </x-dropdown.link> --}}

            {{-- bulk add --}}
            <x-dropdown.link class="cursor-pointer"
              open-modal="bulk-expense">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('expenses'),
                  'question.text' => Msg::exportQuestion('expenses'),
                  'form.action' => route('expense.export'),
                  'form.filename' => 'expenses-' . $user->id . '.csv',
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

      <x-form.check id="future"
        :checked="$_GET['future'] ?? 0">
        <x-slot:label>Show future expenses?</x-slot>
      </x-form.check>

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
        value="{{ route('expense.get') }}">

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
            <x-button.sort data-by="describe"
              :active="$by === 'describe'"
              :dir="$dir">expense</x-button.sort>
          </x-table.th>
          <x-table.th class="px-2">
            <x-button.sort data-by="cost"
              :active="$by === 'cost'"
              :dir="$dir">cost</x-button.sort>
          </x-table.th>
          <x-table.th class="sm-only px-2">
            <x-button.sort data-by="type"
              :active="$by === 'type'"
              :dir="$dir">type</x-button.sort>
          </x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>
        </x-table.thead>

        <tbody id="pushRows">
          <x-table.tr class="keep skip-tooltip hidden cursor-pointer [&.is-future_td:not(:last-child)]:opacity-50"
            open-modal="add-expense"
            is="row">

            {{-- date --}}
            <x-table.td class="date whitespace-nowrap"></x-table.td>

            {{-- describe --}}
            <x-table.td class="describe"></x-table.td>

            {{-- cost --}}
            <x-table.td class="cost"></x-table.td>

            {{-- type --}}
            <x-table.td class="sm-only">
              <span class="type"></span><span class="px-1">|</span>
              <span class="type_desc text-xs text-gray-400 md:text-sm"></span>
            </x-table.td>

            {{-- action --}}
            <x-table.td class="text-base sm:text-xl">
              <div class="flex justify-end gap-4">
                <x-icon class="hide-receipt far fa-receipt cursor-pointer"
                  data-tooltip-position="left"
                  title="{{ Str::title('receipt') }}"
                  open-modal="show-receipt" />

                <x-icon class="hide-future far fa-up cursor-pointer text-green-500"
                  data-tooltip-position="left"
                  title="{{ Str::title('upcoming') }}" />

                <x-icon class="hide-repeat far fa-repeat cursor-pointer text-blue-400"
                  data-tooltip-position="left"
                  title="{{ Str::title('repeat') }}" />

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

    </div>

    @if (!$user->hasExpenses())
      <div class="px-6 pt-6 text-center">{{ Msg::noResults('expenses') }}</div>
    @else
      <div class="flex flex-wrap-reverse justify-center px-6 pt-6 sm:flex-nowrap sm:justify-between">
        <div class="whitespace-nowrap"
          id="counter"></div>
        <div class="flex gap-1.5"
          id="pagination"
          data-page="{{ $_GET['page'] ?? 1 }}"></div>
      </div>
    @endif
  </x-section.one>

  @include('expense.modal.add')
  @include('expense.modal.bulk')
  @include('expense.modal.destroy')
  @include('modal.receipt')
  @include('modal.export')

  @vite(['resources/js/table.js'])
</x-layout.app>
