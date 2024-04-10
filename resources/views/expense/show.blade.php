<x-layout.app :title="__('expenses')">
  <x-section.one px="">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        {{ __('expenses') }}
      </x-slot>

      <x-slot:buttons>

        <x-button.dark x-data=""
          x-on:click.prevent="$dispatch('open-modal', 'add-expense')"
          id="addExpense"
          data-modal="{{ json_encode([
              'title.text' => Msg::add(__('expense')),
              'form.action' => route('expense.add'),
              'date.value' => old('date', now()->format('Y-m-d')),
              'type.value' => old('type', '-1'),
              'describe.value' => old('describe', ''),
              'cost.value' => old('cost', null),
              'image-wrap.set-inputs' => old('image', ''),
              'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
              'destroy.addclass' => 'hidden',
              'submit.text' => __('add'),
          ]) }}"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
          <span class="hidden sm:block">{{ Msg::add(__('expense')) }}</span>
          <span class="block sm:hidden">{{ __('add') }}</span>
        </x-button.dark>

        <x-dropdown.wrap contentClasses="font-normal py-1 bg-white">
          <x-slot:trigger>
            <x-button.light>
              <x-icon class="far fa-ellipsis-vertical text-xs" />
            </x-button.light>
          </x-slot>

          <x-slot:content>
            {{-- bulk add --}}
            <x-dropdown.link x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'bulk-expense')"
              class="cursor-pointer">
              {{ __('bulk add') }}
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'export-modal')"
              class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle(__('expenses')),
                  'question.text' => Msg::exportQuestion(__('expenses')),
                  'form.action' => route('expense.export'),
                  'form.filename' => 'expenses-' . $user->id . '.csv',
              ]) }}">
              {{ __('Export as CSV') }}
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>

      </x-slot>

    </x-section.title>

    <div class="overflow-x-auto">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden">

        <x-table.thead>
          <x-table.th class="pr-2">{{ __('date') }}</x-table.th>
          <x-table.th class="px-2">{{ __('expense') }}</x-table.th>
          <x-table.th class="px-2">{{ __('cost') }}</x-table.th>
          <x-table.th class="sm-only px-2">{{ __('type') }}</x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>

        </x-table.thead>

        <tbody>

          @foreach ($user->expenses as $e)
            <x-table.tr x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'add-expense')"
              class="cursor-pointer"
              id="editExpense{{ $e->id }}"
              :data-modal="json_encode([
                  'title.text' => Msg::add(__('expense')),
                  'form.action' => route('expense.edit', $e->id),
                  'date.value' => old('date', K::date($e->date)->format('Y-m-d')),
                  'type.value' => old('type', $e->type),
                  'describe.value' => old('describe', $e->describe),
                  'cost.value' => old('cost', $e->cost),
                  'image-wrap.set-inputs' => old('image-wrap', ''),
                  'image-wrap.set-img' => $e->getImageURL(),
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('expense.destroy', $e->id),
                      ],
                  ],
                  'submit.text' => __('save'),
              ])">

              {{-- date --}}
              <x-table.td class="whitespace-nowrap">
                {{ K::displayDate($e->date, 'jS M Y') }}
              </x-table.td>

              {{-- expense --}}
              <x-table.td>
                {{ $e->describe }}
              </x-table.td>

              {{-- cost --}}
              <x-table.td>
                {{ K::formatCurrency($e->cost) }}
              </x-table.td>

              {{-- type --}}
              <x-table.td class="sm-only">
                <span>{{ Str::title($e->type) }}</span><span class="px-1">|</span>
                <span class="text-xs text-gray-400 md:text-sm">{{ $e->getType() }}</span>
              </x-table.td>

              {{-- action --}}
              <x-table.td class="text-base sm:text-xl">
                <div class="flex justify-end gap-4">
                  @if ($e->hasImage())
                    <x-icon x-data=""
                      x-on:click.prevent.stop="$dispatch('open-modal', 'show-receipt')"
                      class="far fa-receipt cursor-pointer"
                      data-modal="{{ json_encode([
                          'image.src' => $e->getImageURL(),
                          'form.action' => route('expense.download'),
                          'path.value' => $e->image,
                      ]) }}"
                      data-tooltip-position="left"
                      title="{{ Str::title(__('receipt')) }}" />
                  @endif

                  <x-icon class="far fa-edit cursor-pointer text-orange-400"
                    data-tooltip-position="left"
                    title="{{ Str::title(__('edit')) }}" />
                </div>
              </x-table.td>
            </x-table.tr>
          @endforeach

        </tbody>

      </table>
      @if (!$user->hasExpenses())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults(__('expenses')) }}</div>
      @else
        <x-loader class="hidden pb-6 pt-12"
          id="spinner"
          size="4"
          color="gray-500" />
      @endif
    </div>
  </x-section.one>

  @include('expense.modal.add')
  @include('expense.modal.bulk')
  @include('modal.receipt')
  @include('modal.export')
  @include('expense.modal.destroy')

</x-layout.app>
