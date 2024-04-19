@php
  $user = K::user();
  $last_route = $user->route();

  $first = K::date($last_route->date ?? '');
  $weeks = [$first->copy(), $first->copy()->sub('week', 1), $first->copy()->sub('week', 2), $first->copy()->sub('week', 3)];

  $vehicle = $user->vehicle();

@endphp
{{-- @log(K::firstDayOfWeek($weeks[0])->format('Y-m-d')) --}}

<x-layout.app :title="__('dashboard')">

  <x-section.two grid="grid-cols-1 lg:grid-cols-[auto_1fr]">
    <x-slot:one
      class="flex justify-center gap-3 sm:gap-6">
      <x-button.icon x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'add-route')"
        id="addRoute"
        data-modal="{{ json_encode([
            'title.text' => Msg::add(__('route')),
            'form.action' => route('route.add'),
            'type.value' => old('type', $last_route->type ?? '-1'),
            'depot_id.value' => old('depot_id', $last_route->depot_id ?? $user->options->depot_id),
            'date.value' => old('date', now()->format('Y-m-d')),
            'start_time.value' => old('start_time', K::date($last_route->start_time ?? now())->format('g:i A')),
            'end_time.value' => old('end_time', ''),
            'start_mileage.value' => old('start_mileage', ''),
            'start_mileage_plus.value' => old('start_mileage_plus', ''),
            'end_mileage.value' => old('end_mileage', ''),
            'end_mileage_plus.value' => old('end_mileage_plus', ''),
            'invoice_mileage.value' => old('invoice_mileage', ''),
            'stops.value' => old('stops', ''),
            'bonus.value' => old('bonus', ''),
            'vat.checked' => old('vat', $last_route->vat ?? 0),
            'ttfs.value' => old('ttfs', $last_route->ttfs ?? 60),
            'note.value' => old('note', ''),
            'destroy.addclass' => 'hidden',
            'submit.text' => 'add',
        ]) }}"
        color="bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-700"
        icon="fa-thin fa-compass">
        <span class="block">{{ Msg::add(__('route')) }}</span>
      </x-button.icon>

      <x-button.icon x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'add-refuel')"
        id="addRefuel"
        data-modal="{{ json_encode([
            'title.text' => Msg::add(__('refuel')),
            'form.action' => route('refuel.add', $vehicle->id ?? 1),
            'date.value' => old('date', now()->format('Y-m-d')),
            'mileage.value' => old('mileage', ''),
            'cost.value' => old('cost', ''),
            'first.checked' => old('first', false),
            'image-wrap.set-inputs' => old('image-wrap', ''),
            'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
            'destroy.addclass' => 'hidden',
            'submit.text' => __('add'),
        ]) }}"
        color="bg-lime-600 hover:bg-lime-700 focus:bg-lime-700 active:bg-lime-700"
        icon="fa-thin fa-gas-pump">
        <span class="block">{{ Msg::add(__('refuel')) }}</span>
      </x-button.icon>

      <x-button.icon x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'add-rate')"
        id="addRate"
        data-modal="{{ json_encode([
            'title.text' => Msg::add(__('rate')),
            'form.action' => route('rate.add'),
            'type.value' => old('type', '-1'),
            'date.value' => old('date', now()->format('Y-m-d')),
            'depot_id.value' => old('depot_id', $user->options->depot_id),
            'amount.value' => old('amount', ''),
            'destroy.addclass' => 'hidden',
            'submit.text' => __('add'),
        ]) }}"
        color="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-700"
        icon="fa-thin fa-chart-simple">
        <span class="block">{{ Msg::add(__('rate')) }}</span>
      </x-button.icon>

      <x-button.icon x-data=""
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
        color="bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-700"
        icon="fa-thin fa-file-invoice">
        <span class="block">{{ Msg::add(__('expense')) }}</span>
      </x-button.icon>
    </x-slot:one>

    <x-slot:two
      class="grid grid-cols-2 content-center justify-center gap-1 md:gap-3">

      @php
        $results = collect();
        foreach ($weeks as $date) {
            $routes = $user->routesByWeek($date->copy());
            $first = $routes->first();
            if (!$first) {
                continue;
            }
            $paydate = K::getPayDay($first->date, $first->dsp()->in_hand, $first->dsp()->pay_day);
            if ($paydate >= K::now()) {
                $results->push(['routes' => $routes, 'pay' => $paydate]);
            }
        }
        $use = $results->sortBy('pay')->first() ?? null;

        $last = $user->route();
      @endphp

      <x-section.detail :value="K::formatCurrency($user->lastRefuel()->fuel_rate)"
        :title="__('Latest fuel rate')"
        icon="fas fa-gauge-low text-yellow-500"
        :none="__('Not yet added a refuel!')"
        :active="$user->hasRefuels()" />

      <x-section.detail :value="K::formatCurrency($use['routes']->sum('total_pay'))"
        :title="__('Next pay amount')"
        icon="fas fa-coin text-yellow-500"
        :none="__('Not due any pay yet!')"
        :active="!!$use" />

      <x-section.detail :value="K::formatCurrency($last->rate('fuel')->amount)"
        :title="__('Latest invoice rate')"
        icon="fas fa-gauge-high text-yellow-500"
        :none="__('No invoiced rate yet!')"
        :active="$last && $last->hasRate('fuel')" />

      <x-section.detail :value="K::displayDate($use['pay'])"
        :title="__('Next pay date')"
        icon="fas fa-calendar-star text-yellow-500"
        :none="__('Not due any pay yet!')"
        :active="!!$use" />

    </x-slot:two>

  </x-section.two>

  @define($tab = request()->get('tab') ?? 0)
  <x-section.one x-data="{ activeTab: {{ $tab }} }"
    px="">
    <x-tab.link-wrap class="mb-4 px-4 text-2xl font-medium text-gray-900 md:mb-5 md:px-6">
      <x-slot:tabs>

        @foreach ($weeks as $week)
          <x-tab.button class="whitespace-nowrap"
            :tab="$loop->index">
            {{ __('week') }} {{ $week->week() }}
          </x-tab.button>
        @endforeach
      </x-slot>

    </x-tab.link-wrap>

    <x-tab.content-wrap>

      @foreach ($weeks as $date)
        @define($routes = $user->routesByWeek($date->copy()))

        <x-tab.content class="max-w-[100vw] overflow-x-auto"
          :tab="$loop->index">

          <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden">

            <x-table.thead class="text-xs md:text-sm">
              <x-table.th class="pr-2">
                {{ __('date') }}
                <span class="xs-only"> & {{ __('time') }}</span>
              </x-table.th>
              <x-table.th class="sm-only px-2">{{ __('time') }}</x-table.th>
              <x-table.th class="px-2">{{ __('miles') }}</x-table.th>
              <x-table.th class="px-2">{{ __('pay') }}</x-table.th>
              <x-table.th class="sm-only px-2">{{ __('stops') }}</x-table.th>
              <x-table.th class="sm-only px-2">{{ __('type') }}</x-table.th>
              <x-table.th class="w-[1%] pl-2"></x-table.th>
            </x-table.thead>

            <tbody>

              @foreach ($routes as $r)
                <x-table.tr x-data=""
                  x-on:click.prevent="$dispatch('open-modal', 'add-route')"
                  class="cursor-pointer"
                  id="editRoute{{ $r->id }}"
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
                    <div>{{ K::displayDate($r->date) }}</div>
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

              @if ($routes->count())
                @php
                  $items = collect();
                  $routes->each(fn($item) => $items->add($item->only(['id', 'date', 'invoice_mileage', 'bonus', 'vat'])));
                @endphp

                {{-- the total row --}}
                <x-table.tr x-data=""
                  x-on:click.prevent="$dispatch('open-modal', 'edit-week')"
                  class="cursor-pointer font-semibold last-of-type:bg-green-100"
                  id="editWeek{{ $loop->index }}"
                  :data-modal="json_encode([
                      'title.text' => K::date($items->first()['date'])->week(),
                      'week.data' => [
                          'routes' => $items,
                      ],
                  ])">

                  {{-- date --}}
                  <x-table.td class="whitespace-nowrap">
                    <div class="text-xs font-light text-gray-600 sm:text-sm">{{ __('Pay Date') }}</div>
                    @php
                      $r = $routes->first();
                      $d = $r->dsp();
                    @endphp
                    <div>{{ K::displayDate(K::getPayDay($r->date, $d->in_hand, $d->pay_day)) }}</div>
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
                  <x-table.td class="sm-only"></x-table.td>

                  {{-- action --}}
                  <x-table.td class="">
                    <div class="flex items-center justify-end gap-4">
                      @unless ($user->weeksFuelRateIsSet($routes->first()->depot_id, $date))
                        <x-icon x-data=""
                          x-on:click.prevent.stop="$dispatch('open-modal', 'add-rate')"
                          class="far fa-chart-simple cursor-pointer text-teal-400"
                          id="addRate{{ $loop->index }}"
                          data-modal="{{ json_encode([
                              'title.text' => Msg::add(__('rate')),
                              'form.action' => route('rate.add'),
                              'type.value' => old('type', 'fuel'),
                              'date.value' => old('date', K::firstDayOfWeek($date)->format('Y-m-d')),
                              'depot_id.value' => old('depot_id', $routes->first()->depot_id),
                              'amount.value' => old('amount', ''),
                              'destroy.addclass' => 'hidden',
                              'submit.text' => __('add'),
                          ]) }}"
                          data-tooltip-position="left"
                          :title="__('Set weeks fuel rate?')" />
                      @endunless

                      <x-icon class="far fa-edit cursor-pointer text-base text-orange-400 sm:text-xl"
                        data-tooltip-position="left"
                        :title="Str::title(__('update entire week'))" />
                    </div>
                  </x-table.td>
                </x-table.tr>
              @endif

            </tbody>

          </table>
          @if (!$routes->count())
            <div class="px-6 pt-6 text-center">{{ Msg::noResults(__('routes')) }}</div>
          @endif

        </x-tab.content>
      @endforeach

    </x-tab.content-wrap>

  </x-section.one>

  @push('modals')
    @include('route.modal.add')
    @include('route.modal.week')
    @include('route.modal.extra')
    @include('rate.modal.add')
    @include('refuel.modal.add')
    @include('expense.modal.add')
    @include('route.modal.destroy')
  @endpush

  {{-- used to test javascript --}}
  @if (false)
    <script type="module">
      $(() => {
        K.urlParam();
      });
    </script>
  @endif

</x-layout.app>
