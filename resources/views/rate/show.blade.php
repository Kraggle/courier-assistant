@php
  $user = K::user();
  $dsp = $user->dsp();
  $rates = $dsp->rates;
@endphp

<x-layout.app title="pay rates">

  <x-section.one px="0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        {{ "$dsp->name " . 'pay rates' }}
      </x-slot>

      <x-slot:buttons>

        <x-button.dark x-data=""
          x-on:click.prevent="$dispatch('open-modal', 'add-rate')"
          id="addRate"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('rate'),
              'form.action' => route('rate.add'),
              'date.value' => old('date', now()->format('Y-m-d')),
              'type.value' => old('type', '-1'),
              'depot_id.value' => old('depot_id', $user->options->depot_id),
              'amount.value' => old('amount', ''),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'add',
          ]) }}"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
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
            <x-dropdown.link x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'bulk-rates')"
              class="cursor-pointer">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'export-modal')"
              class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('rates'),
                  'question.text' => Msg::exportQuestion('rates'),
                  'form.action' => route('rate.export'),
                  'form.filename' => 'rates-' . $user->id . '.csv',
              ]) }}">
              Export as CSV
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>
      </x-slot>
    </x-section.title>

    <div class="overflow-x-auto">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden">

        <x-table.thead>
          <x-table.th class="pr-2">from date</x-table.th>
          <x-table.th class="px-2">type</x-table.th>
          <x-table.th class="px-2">rate</x-table.th>
          <x-table.th class="px-2">depot</x-table.th>
          <x-table.th class="sm-only px-2">creator</x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>

        </x-table.thead>

        <tbody>

          @foreach ($rates as $rate)
            <x-table.tr x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'add-rate')"
              class="cursor-pointer"
              id="editRate{{ $rate->id }}"
              :data-modal="json_encode([
                  'title.text' => Msg::edit('rate'),
                  'form.action' => route('rate.edit', $rate->id),
                  'date.value' => old('date', $rate->date),
                  'type.value' => old('type', $rate->type),
                  'depot_id.value' => old('depot_id', $rate->depot_id),
                  'amount.value' => old('amount', $rate->amount),
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('rate.destroy', $rate->id),
                      ],
                  ],
                  'submit.text' => 'save',
              ])">

              {{-- date --}}
              <x-table.td>
                {{ K::date($rate->date)->format('jS M Y') }}
              </x-table.td>

              {{-- type --}}
              <x-table.td>
                {!! $rate->getType(true, 'hidden sm:inline') !!}
              </x-table.td>

              {{-- amount --}}
              <x-table.td>
                {{ K::formatCurrency($rate->amount, $rate->amount < 0.9999) }}
              </x-table.td>

              {{-- depot --}}
              <x-table.td>
                {{ $rate->depot->identifier }}
              </x-table.td>

              {{-- creator --}}
              <x-table.td class="sm-only">
                @if ($rate->hasCreateLog())
                  {{ $rate->createLog()->causer->name }}
                @else
                  {{ App\Models\User::all()->first()->name }}
                @endif
              </x-table.td>

              {{-- action --}}
              <x-table.td class="text-base sm:text-xl">
                <div class="flex justify-end gap-4">
                  @if ($rate->hasChangeLogs())
                    @php
                      $logs = [];
                      foreach ($rate->changeLogs() as $log) {
                          $logs[] = [
                              'date' => K::displayDate($log->created_at, 'd-m-Y'),
                              'properties' => $log->properties,
                              'user' => $log->causer->name,
                          ];
                      }

                    @endphp

                    <x-icon x-data=""
                      x-on:click.prevent.stop="$dispatch('open-modal', 'changes-modal')"
                      class="far fa-rotate cursor-pointer text-green-600"
                      data-modal="{{ json_encode([
                          'title.text' => 'changes',
                          'tbody.changes' => $logs,
                      ]) }}"
                      data-tooltip-position="left"
                      title="{{ Str::title('changes') }}" />
                  @endif

                  <x-icon class="far fa-edit cursor-pointer text-orange-400"
                    data-tooltip-position="left"
                    title="{{ Str::title('edit') }}" />
                </div>
              </x-table.td>
            </x-table.tr>
          @endforeach

        </tbody>

      </table>

      @if (!$user->hasDSP())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults('rates') }}</div>
      @endif
    </div>
  </x-section.one>

  @include('rate.modal.add')
  @include('rate.modal.bulk')
  @include('rate.modal.destroy')
  @include('modal.changes')
  @include('modal.export')

</x-layout.app>
