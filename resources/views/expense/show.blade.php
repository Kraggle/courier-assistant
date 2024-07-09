<x-layout.app :title="__('expenses')">
  <x-section.one px="">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        {{ __('expenses') }}
      </x-slot>

      <x-slot:buttons>

        @define($eTest = null)
        <x-button.dark x-data=""
          x-on:click.prevent="$dispatch('open-modal', 'add-expense')"
          id="addExpense"
          data-modal="{{ json_encode([
              'title.text' => Msg::add(__('expense')),
              'form.action' => route('expense.add'),
              'form.mode' => 'add',
              'choice-wrap.addclass' => 'hidden',
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
              'submit.text' => __('add'),
              'is-repeat.value' => 0,
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
            {{-- toggle all --}}
            <x-dropdown.link class="cursor-pointer"
              href="\expense?future=1">
              {{ __('Show future expenses') }}
            </x-dropdown.link>

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

    <div class="overflow-x-auto [&_.dt-layout-row:not(.dt-layout-table)]:px-4 md:[&_.dt-layout-row:not(.dt-layout-table)]:px-6">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg"
        id="table_id">

        <x-table.thead>
          <x-table.th class="pr-2"
            data-priority="1">{{ __('date') }}</x-table.th>
          <x-table.th class="px-2"
            data-priority="1">{{ __('expense') }}</x-table.th>
          <x-table.th class="px-2"
            data-priority="1">{{ __('cost') }}</x-table.th>
          <x-table.th class="__sm-only px-2"
            data-priority="2">{{ __('type') }}</x-table.th>
          <x-table.th class="w-[1%] pl-2"
            data-priority="1"
            data-dt-order="disable"></x-table.th>

        </x-table.thead>

        <tbody>

          @define($expenses = isset($_GET['future']) ? $user->expenses : $user->expensesToNextWeek)
          @foreach ($expenses as $e)
            @php
              $repeat = $e->repeat;
              $end = K::date($repeat->end_date ?? K::date($e->date)->add(1, 'year'));
              $hide = 'choice-wrap.' . ($repeat ? 'removeclass' : 'addclass');
              $future = $e->isFuture() ? 'opacity-50' : '';
            @endphp
            <x-table.tr x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'add-expense')"
              class="cursor-pointer"
              id="editExpense{{ $e->id }}"
              :data-modal="json_encode([
                  'title.text' => $repeat ? 'Edit repeat expense' : 'Edit expense',
                  'form.action' => route('expense.edit', $e->id),
                  'form.mode' => 'edit',
                  $hide => 'hidden',
                  'date.value' => old('date', K::date($e->date)->format('Y-m-d')),
                  'type.value' => old('type', $e->type),
                  'date_to.value' => old('date_to', $end->format('Y-m-d')),
                  'repeat.value' => old('repeat', $repeat->rules->repeat ?? 'never'),
                  'every.value' => old('every', $repeat->rules->every ?? 'week'),
                  'every_x.value' => old('every_x', $repeat->rules->every_x ?? '1'),
                  'month.value' => old('month', $repeat->rules->month ?? 'day'),
                  'describe.value' => old('describe', $e->describe),
                  'cost.value' => old('cost', $e->cost),
                  'image-wrap.set-inputs' => old('image-wrap', ''),
                  'image-wrap.set-img' => $e->getImageURL(),
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('expense.destroy', $e->id),
                          'title.text' => $repeat ? 'Delete repeat expense' : 'Delete expense',
                          'message.text' => $repeat ? 'Choose which of these repeat expenses you want to delete.' : Msg::sureDelete(__('expense')),
                          $hide => 'hidden',
                          'submit.text' => $repeat ? 'delete' : 'yes',
                      ],
                  ],
                  'submit.text' => __('save'),
                  'is-repeat.value' => old('is-repeat', $e->isRepeat()),
              ])">

              {{-- date --}}
              <x-table.td class="{{ $future }} whitespace-nowrap">
                {{ K::displayDate($e->date, 'jS M Y') }}
              </x-table.td>

              {{-- expense --}}
              <x-table.td class="{{ $future }}">
                {{ $e->describe }}
              </x-table.td>

              {{-- cost --}}
              <x-table.td class="{{ $future }}">
                {{ K::formatCurrency($e->cost) }}
              </x-table.td>

              {{-- type --}}
              <x-table.td class="__sm-only {{ $future }}">
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

                  @if ($e->isFuture())
                    <x-icon class="far fa-up cursor-pointer text-green-500"
                      data-tooltip-position="left"
                      title="{{ Str::title(__('upcoming')) }}" />
                  @endif

                  @if ($e->isRepeat())
                    <x-icon class="far fa-repeat cursor-pointer text-blue-400"
                      data-tooltip-position="left"
                      title="{{ Str::title(__('repeat')) }}" />
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
  @include('expense.modal.destroy')
  @include('modal.receipt')
  @include('modal.export')

  <script type="module">
    $(() => {
      const $table = $('#table_id'),
        table = $table.DataTable({
          responsive: true,
          order: [0, 'asc'],
          columnDefs: [{
            type: 'date',
            targets: 0
          }, ],
          pageLength: 25
        });

      const timer = timed();
      $(window).on('resize', () => {
        timer.run(() => {
          $table.css('width', '');
          table.columns.adjust().responsive.recalc();
        }, 500);
      });
    });
  </script>
</x-layout.app>
