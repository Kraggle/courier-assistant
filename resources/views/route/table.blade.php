@if ($routes->count())
  @php
    $weekNo = K::date($routes->first()->date)->week();
    $items = collect();
    $routes->each(fn($item) => $items->add($item->only(['id', 'date', 'invoice_mileage', 'bonus', 'vat'])));
  @endphp

  {{-- the total row --}}
  <x-table.tr x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'edit-week')"
    class="cursor-pointer !border-t-2 !border-t-gray-500 !bg-green-100 font-semibold"
    id="editWeek{{ $weekNo }}"
    :data-modal="json_encode([
        'title.text' => K::date($items->first()['date'])->week(),
        'week.data' => [
            'routes' => $items,
        ],
    ])">

    {{-- date --}}
    <x-table.td class="whitespace-nowrap">
      <div class="text-xs font-light text-gray-600 sm:text-sm">Pay Date</div>
      <div class="">{{ K::displayDate(K::getPayDay($routes->first()->date)) }}</div>
    </x-table.td>

    {{-- time --}}
    <x-table.td class="sm-only whitespace-nowrap">
      @php
        $time = $routes->sum('time');
        $hours = floor($time / 3600);
        $minutes = ($time % 3600) / 60;
      @endphp
      {{ K::pluralize('% hr', '% hrs', $hours) . ($minutes > 0 ? K::pluralize(' and % min', ' and % mins', $minutes) : '') }}
    </x-table.td>

    {{-- miles --}}
    <x-table.td>
      @define($miles = $routes->sum('miles'))
      @if ($miles)
        <x-table.text-with-alt>
          <x-slot:main>{{ $miles }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency($routes->sum('fuel_spend')) }}</x-slot:alt>
        </x-table.text-with-alt>
        <x-table.text-with-alt>
          <x-slot:main>{{ $routes->sum('mileage') }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency($routes->sum('fuel_pay')) }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- pay --}}
    <x-table.td>
      @define($total = $routes->sum('total_pay'))
      @define($actual = $routes->sum('actual_pay'))
      <x-table.text-with-alt>
        <x-slot:main>{{ K::formatCurrency($total) }}</x-slot:main>
        <x-slot:alt>@ {{ K::formatCurrency(K::getHourly($total, $hours, $minutes)) . 'ph' }}</x-slot:alt>
      </x-table.text-with-alt>

      @if ($total != $actual)
        <x-table.text-with-alt>
          <x-slot:main>{{ K::formatCurrency($actual) }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency(K::getHourly($actual, $hours, $minutes)) . 'ph' }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- stops --}}
    <x-table.td class="sm-only">
      @define($stops = $routes->sum('stops'))
      @if ($stops)
        <x-table.text-with-alt>
          <x-slot:main>{{ $stops }}</x-slot:main>
          <x-slot:alt>@ {{ round($routes->where('stops')->avg('stops_hourly'), 1) . 'ph' }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- type --}}
    <x-table.td>
      <div class="flex items-center justify-end gap-4">
        @define($first = $routes->first())
        @unless ($first->user->weeksFuelRateIsSet($first->depot_id, $first->date))
          <x-icon x-data=""
            x-on:click.prevent.stop="$dispatch('open-modal', 'add-rate')"
            class="far fa-chart-simple cursor-pointer text-teal-400"
            id="addRate"
            data-modal="{{ json_encode([
                'title.text' => Msg::add(__('rate')),
                'form.action' => route('rate.add'),
                'type.value' => old('type', 'fuel'),
                'date.value' => old('date', K::firstDayOfWeek($first->date)->format('Y-m-d')),
                'depot_id.value' => old('depot_id', $first->depot_id),
                'amount.value' => old('amount', ''),
                'destroy.addclass' => 'hidden',
                'submit.text' => __('add'),
            ]) }}"
            data-tooltip-position="left"
            :title="__('Set weeks fuel rate?')" />
        @endunless

        <x-icon class="far fa-edit hidden cursor-pointer text-base text-orange-400 sm:block sm:text-xl"
          data-tooltip-position="left"
          :title="Str::title(__('update entire week'))" />

        <span class="text-lg font-bold sm:hidden">#{{ $weekNo }}</span>
      </div>
    </x-table.td>

    {{-- action --}}
    <x-table.td class="sm-only text-right text-2xl">#{{ $weekNo }}</x-table.td>
  </x-table.tr>
@endif

@foreach ($routes as $r)
  <x-table.tr x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'add-route')"
    class="cursor-pointer"
    id="editRoute{{ $r->id }}"
    data-date="{{ $r->date->format('Y-m-d') }}"
    :data-modal="json_encode([
        'title.text' => Msg::edit(__('route')),
        'form.action' => route('route.edit', $r->id),
        'type.value' => old('type', $r->type),
        'depot_id.value' => old('depot_id', $r->depot_id),
        'date.value' => old('date', K::date($r->date)->format('Y-m-d')),
        'start_time.value' => old('start_time', K::date($r->start_time)->format('g:i A')),
        'end_time.value' => old('end_time', $r->end_time ? K::date($r->end_time)->format('g:i A') : ''),
        'start_mileage.value' => old('start_mileage', $r->start_mileage),
        'start_mileage_plus.value' => old('start_mileage_plus', ''),
        'end_mileage.value' => old('end_mileage', $r->end_mileage),
        'end_mileage_plus.value' => old('end_mileage_plus', ''),
        'invoice_mileage.value' => old('invoice_mileage', $r->invoice_mileage),
        'stops.value' => old('stops', $r->stops),
        'bonus.value' => old('bonus', $r->bonus),
        'vat.checked' => old('vat', $r->vat),
        'ttfs.value' => old('ttfs', $r->ttfs),
        'note.value' => old('note', $r->note),
        'destroy.removeclass' => 'hidden',
        'destroy.data' => [
            'modal' => [
                'form.action' => route('route.destroy', $r->id),
            ],
        ],
        'submit.text' => __('save'),
    ])">

    @define($time = K::formatTime($r->start_time) . ' - ' . ($r->end_time ? K::formatTime($r->end_time) : '??'))

    {{-- date --}}
    <x-table.td class="whitespace-nowrap">
      <div class="sm:hidden">{{ K::displayDate($r->date, 'D, jS M y') }}</div>
      <div class="hidden sm:block">{{ K::displayDate($r->date) }}</div>
      <div class="hidden sm:block">{{ $r->date->format('Y') }}</div>
      <div class="xs-only text-xs font-light text-gray-600 sm:text-sm">{{ $time }}</div>
      <div class="xs-only">{{ $r->time_string }}</div>
    </x-table.td>

    {{-- time --}}
    <x-table.td class="sm-only">
      <div class="text-xs font-light text-gray-600 sm:text-sm">{{ $time }}</div>
      <div>{{ $r->time_string }}</div>
    </x-table.td>

    {{-- miles --}}
    <x-table.td>
      @if ($r->miles)
        <x-table.text-with-alt>
          <x-slot:main>{{ $r->miles }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency($r->fuel_spend) }}</x-slot:alt>
        </x-table.text-with-alt>
        <x-table.text-with-alt>
          <x-slot:main>{{ $r->mileage }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency($r->fuel_pay) }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- pay --}}
    <x-table.td>
      <x-table.text-with-alt>
        <x-slot:main>{{ K::formatCurrency($r->total_pay) }}</x-slot:main>
        <x-slot:alt>@ {{ K::formatCurrency($r->total_hourly) . 'ph' }}</x-slot:alt>
      </x-table.text-with-alt>

      @if ($r->total_pay != $r->actual_pay)
        <x-table.text-with-alt>
          <x-slot:main>{{ K::formatCurrency($r->actual_pay) }}</x-slot:main>
          <x-slot:alt>@ {{ K::formatCurrency($r->actual_hourly) . 'ph' }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- stops --}}
    <x-table.td class="sm-only">
      @if ($r->stops)
        <x-table.text-with-alt>
          <x-slot:main>{{ $r->stops }}</x-slot:main>
          <x-slot:alt>@ {{ $r->stops_hourly . 'ph' }}</x-slot:alt>
        </x-table.text-with-alt>
      @endif
    </x-table.td>

    {{-- type --}}
    <x-table.td class="sm-only whitespace-nowrap">
      <div>{{ $r->getType() }}</div>
      <x-table.text-with-alt>
        <x-slot:main>{{ $r->depot->identifier }}</x-slot:main>
        <x-slot:alt>{{ $r->depot->location }}</x-slot:alt>
      </x-table.text-with-alt>
    </x-table.td>

    {{-- action --}}
    <x-table.td class="text-base sm:text-xl">
      <div class="flex items-center justify-end gap-4">
        @if ($r->hasExtra())
          @php
            $data = [];

            if ($r->bonus) {
                $data['bonus.text'] = $r->bonus;
                $data['bonus-wrap.removeclass'] = 'hidden';
            } else {
                $data['bonus-wrap.addclass'] = 'hidden';
            }

            if ($r->note) {
                $data['note.text'] = $r->note;
                $data['note-wrap.removeclass'] = 'hidden';
            } else {
                $data['note-wrap.addclass'] = 'hidden';
            }
          @endphp

          <x-icon x-data=""
            x-on:click.prevent.stop="$dispatch('open-modal', 'extra-route')"
            class="far fa-square-ellipsis-vertical mt-[2px] cursor-pointer text-blue-400"
            data-modal="{{ json_encode($data) }}"
            data-tooltip-position="left"
            :title="__('Extra information!')" />
        @endif

        <x-icon class="far fa-edit cursor-pointer text-orange-400"
          data-tooltip-position="left"
          title="{{ Str::title(__('edit')) }}" />
      </div>
    </x-table.td>
  </x-table.tr>
@endforeach
