@php
  $last_route = $user->route();
  $date = K::date($last_route->date ?? '');
  //$weeks = [$date->copy(), $date->copy()->sub('week', 1), $date->copy()->sub('week', 2), $date->copy()->sub('week', 3)];
@endphp

<x-layout.app title="routes">
  <x-section.one class="px-0 md:px-0">
    <x-section.title class="px-4 md:px-6"
      id="pageTitle">
      <x-slot:title>
        routes
        <span class="pl-2 align-middle text-sm">
          (<span class="loaded-count">0</span> /
          <span>{{ $user->routes->count() }}</span>)
        </span>
      </x-slot>

      <x-slot:buttons>

        @php
          $modal = null;
          $week = false;
          if (isset($_GET['modal'])) {
              $modal = $_GET['modal'];
              $week = !preg_match('/^editRoute/', $modal);
              preg_match('/(\d+)$/', $modal, $m);
              $id = $m[1];
              $r = $user->routes->find($id);
          }
        @endphp
        @if ($modal && !$week && $r)
          <x-button.dark class="hidden"
            id="{{ $modal }}"
            :data-modal="json_encode([
                'title.text' => Msg::edit('route'),
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
                'submit.text' => 'save',
                'more-btn.text' => 'Show More',
                'more.addclass' => 'hidden',
            ])"
            open-modal="add-route">
          </x-button.dark>
        @elseif ($modal && $week)
        @endif

        <x-button.dark class="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
          id="addRoute"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('route'),
              'form.action' => route('route.add'),
              'type.value' => old('type', $last_route->type ?? null),
              'depot_id.value' => old('depot_id', $last_route->depot_id ?? null),
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
              'more-btn.text' => 'Show More',
              'more.addclass' => 'hidden',
          ]) }}"
          open-modal="add-route">
          <span class="hidden sm:block">{{ Msg::add('route') }}</span>
          <span class="block sm:hidden">add</span>
        </x-button.dark>

        <x-dropdown.wrap>
          <x-slot:trigger>
            <x-button.light>
              <x-icon class="far fa-ellipsis-vertical text-xs" />
            </x-button.light>
          </x-slot>

          <x-slot:content
            class="font-normal">
            {{-- bulk add --}}
            <x-dropdown.link class="cursor-pointer"
              open-modal="bulk-route">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('routes'),
                  'question.text' => Msg::exportQuestion('routes'),
                  'form.action' => route('route.export'),
                  'form.filename' => 'routes-' . $user->id . '.csv',
              ]) }}"
              open-modal="export-modal">
              Export as CSV
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>

      </x-slot>

    </x-section.title>

    <div class="overflow-x-auto">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg [&_.sm-only]:hidden sm:[&_.sm-only]:table-cell sm:[&_.xs-only]:hidden">

        <x-table.thead>
          <x-table.th class="pr-2' : 'px-2">
            date
            <span class="xs-only"> & time</span>
          </x-table.th>
          <x-table.th class="sm-only px-2">time</x-table.th>
          <x-table.th class="px-2">miles</x-table.th>
          <x-table.th class="px-2">pay</x-table.th>
          <x-table.th class="sm-only px-2">stops</x-table.th>
          <x-table.th class="sm-only px-2">type</x-table.th>
          <x-table.th class="w-[1%] pl-2"></x-table.th>
        </x-table.thead>

        <tbody id="pushRows">

          <x-table.tr class="skip-tooltip hidden cursor-pointer !border-t-2 !border-t-gray-500 !bg-green-100 font-semibold"
            data-modal=""
            is="week"
            open-modal="edit-week">

            {{-- date --}}
            <x-table.td class="whitespace-nowrap">
              <div class="text-xs font-light text-gray-600 sm:text-sm">Pay Date</div>
              <div class="pay_day"></div>
            </x-table.td>

            {{-- time --}}
            <x-table.td class="duration sm-only whitespace-nowrap"></x-table.td>

            {{-- miles --}}
            <x-table.td>
              <x-table.text-with-alt class="hide-miles">
                <x-slot:main
                  class="miles"></x-slot:main>
                <x-slot:alt
                  class="fuel_spend"></x-slot:alt>
              </x-table.text-with-alt>
              <x-table.text-with-alt class="hide-miles">
                <x-slot:main
                  class="mileage"></x-slot:main>
                <x-slot:alt
                  class="fuel_pay"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- pay --}}
            <x-table.td>
              <x-table.text-with-alt>
                <x-slot:main
                  class="total_pay"></x-slot:main>
                <x-slot:alt
                  class="total_hourly"></x-slot:alt>
              </x-table.text-with-alt>
              <x-table.text-with-alt class="hide-actual">
                <x-slot:main
                  class="actual_pay"></x-slot:main>
                <x-slot:alt
                  class="actual_hourly"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- stops --}}
            <x-table.td class="sm-only">
              <x-table.text-with-alt class="hide-stops">
                <x-slot:main
                  class="stops"></x-slot:main>
                <x-slot:alt
                  class="stops_avg"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- type --}}
            <x-table.td>
              <div class="flex items-center justify-end gap-4">
                <x-icon class="hide-rate far fa-chart-simple cursor-pointer text-teal-400"
                  id="addRate"
                  data-modal=""
                  data-tooltip-position="left"
                  title="Set weeks fuel rate?"
                  open-modal="add-rate" />

                <x-icon class="far fa-edit hidden cursor-pointer text-base text-orange-400 sm:block sm:text-xl"
                  data-tooltip-position="left"
                  :title="Str::title('update entire week')" />

                <span class="text-lg font-bold sm:hidden">#<span class="week"></span></span>
              </div>
            </x-table.td>

            {{-- action --}}
            <x-table.td class="sm-only text-right text-2xl">#<span class="week"></span></x-table.td>

          </x-table.tr>

          <x-table.tr class="skip-tooltip hidden cursor-pointer"
            data-date="{{ $date->format('Y-m-d') }}"
            data-modal=""
            is="route"
            open-modal="add-route">

            {{-- date --}}
            <x-table.td class="whitespace-nowrap">
              <div class="date_full block sm:hidden"></div>
              <div class="date_display hidden sm:block"></div>
              <div class="date_year hidden sm:block"></div>
              <div class="time xs-only text-xs font-light text-gray-600 sm:text-sm"></div>
              <div class="time_string xs-only"></div>
            </x-table.td>

            {{-- time --}}
            <x-table.td class="sm-only">
              <div class="time text-xs font-light text-gray-600 sm:text-sm"></div>
              <div class="time_string"></div>
            </x-table.td>

            {{-- miles --}}
            <x-table.td>
              <x-table.text-with-alt class="hide-miles">
                <x-slot:main
                  class="miles"></x-slot:main>
                <x-slot:alt
                  class="fuel_spend"></x-slot:alt>
              </x-table.text-with-alt>
              <x-table.text-with-alt class="hide-miles">
                <x-slot:main
                  class="mileage"></x-slot:main>
                <x-slot:alt
                  class="fuel_pay"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- pay --}}
            <x-table.td>
              <x-table.text-with-alt>
                <x-slot:main
                  class="total_pay"></x-slot:main>
                <x-slot:alt
                  class="total_hourly"></x-slot:alt>
              </x-table.text-with-alt>

              <x-table.text-with-alt class="hide-actual">
                <x-slot:main
                  class="actual_pay"></x-slot:main>
                <x-slot:alt
                  class="actual_hourly"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- stops --}}
            <x-table.td class="sm-only">
              <x-table.text-with-alt class="hide-stops">
                <x-slot:main
                  class="stops"></x-slot:main>
                <x-slot:alt
                  class="stops_hourly"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- type --}}
            <x-table.td class="sm-only whitespace-nowrap">
              <div class="type"></div>
              <x-table.text-with-alt>
                <x-slot:main
                  class="depot_identifier"></x-slot:main>
                <x-slot:alt
                  class="depot_location"></x-slot:alt>
              </x-table.text-with-alt>
            </x-table.td>

            {{-- action --}}
            <x-table.td class="text-base sm:text-xl">
              <div class="flex items-center justify-end gap-4">
                <x-icon class="hide-extra far fa-square-ellipsis-vertical mt-[2px] cursor-pointer text-blue-400"
                  data-modal=""
                  data-tooltip-position="left"
                  title="Extra information!"
                  open-modal="extra-route" />

                <x-icon class="far fa-edit cursor-pointer text-orange-400"
                  data-tooltip-position="left"
                  title="{{ Str::title('edit') }}" />
              </div>
            </x-table.td>
          </x-table.tr>

        </tbody>

      </table>

      @if (!$user->hasRoutes())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults('routes') }}</div>
      @else
        <x-loader class="pb-6 pt-12"
          id="spinner"
          style="display: none;"
          size="4"
          color="bg-gray-500" />
      @endif
    </div>
  </x-section.one>

  <div class="fixed right-20 top-0 -translate-y-full rounded-b-md bg-white px-3 py-1 font-serif opacity-0 shadow-md transition-all duration-150 ease-out [&.show]:translate-y-0 [&.show]:opacity-100"
    id="countFloat">
    <span class="align-middle text-sm">
      (<span class="loaded-count">0</span> /
      <span>{{ $user->routes->count() }}</span>)
    </span>
  </div>

  @push('modals')
    @include('route.modal.add')
    @include('route.modal.week')
    @include('route.modal.bulk')
    @include('route.modal.extra')
    @include('route.modal.destroy')
    @include('rate.modal.add')
    @include('modal.export')
  @endpush

  @push('scripts')
    <script type="module">
      $.fn.isInViewport = function() {
        const elementTop = $(this).offset().top,
          elementBottom = elementTop + $(this).outerHeight();
        const viewportTop = $(window).scrollTop(),
          viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
      };

      $(() => {
        let loading = false,
          available = true,
          first = true,
          count = 0;
        const $el = $('#pushRows'),
          $spinner = $('#spinner'),
          $route = $('[is=route]'),
          $week = $('[is=week]'),
          $loaded = $('.loaded-count'),
          $count = $('#countFloat'),
          $title = $('#pageTitle');

        const getRows = () => {
          $count[`${$title.isInViewport() ? 'remove' : 'add'}Class`]('show');

          if (!$('tr:last-child', $el).isInViewport()) return;
          if (loading || !available) return;
          loading = true;
          $spinner.show();

          const date = $el.find('tr:last-of-type').data('date');

          $.ajax({
            url: "{{ route('route.get') }}",
            method: "POST",
            data: {
              _token: "{{ csrf_token() }}",
              date,
              first,
              count
            },
            success: function(data) {
              // console.log(data);

              if (data.items && data.items.length > 0)
                generateRows(data.items);

              $spinner.hide();
              refreshAll();
              loading = false;
              first = false;

              available = data.available;
              count = data.total;
              $loaded.text(data.total);
            }
          });
        }

        const generateRows = rows => {
          K.each(rows, (index, row) => {
            let $row;

            switch (row.is) {
              case 'route':
                $row = $route.clone();
                $row.data('modal', row.modal.route);
                $('.hide-extra', $row).data('modal', row.modal.extra);
                $row.attr('id', `editRoute${row.id}`);
                $row.attr('data-date', row.date_ymd);

                if (!row.has_extra)
                  $('.hide-extra', $row).addClass('hidden');

                break;
              case 'week':
                $row = $week.clone();
                $row.data('modal', row.modal.week);
                $('.hide-rate', $row).data('modal', row.modal.rate);
                $row.attr('id', `editWeek${row.week}_${row.year}`);

                if (row.rate_is_set)
                  $('.hide-rate', $row).addClass('hidden');
                break;
            }

            if (!row.miles)
              $('.hide-miles', $row).addClass('hidden');
            if (row.total_pay == row.actual_pay)
              $('.hide-actual', $row).addClass('hidden');
            if (!row.stops)
              $('.hide-stops', $row).addClass('hidden');

            K.each(row, (key, value) => {
              $(`.${key}`, $row).text(value);
            });

            $el.append($row.removeClass('hidden skip-tooltip'));
          });
        }

        $(window).on("scroll", getRows);
        getRows();
      });
    </script>
  @endpush

</x-layout.app>
