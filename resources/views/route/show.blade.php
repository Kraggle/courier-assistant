@php
  $last_route = $user->route();
  $date = K::date($last_route->date ?? '');
  $weeks = [$date->copy(), $date->copy()->sub('week', 1), $date->copy()->sub('week', 2), $date->copy()->sub('week', 3)];
@endphp

<x-layout.app title="routes">
  <x-section.one class="px-0 md:px-0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        routes
      </x-slot>

      <x-slot:buttons>

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
          @foreach ($weeks as $date)
            @define($routes = $user->routesByWeek($date)->sortByDesc('date'))
            @include('route.table', ['routes' => $routes])
          @endforeach
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
        let loading = false;
        const $el = $('#pushRows'),
          $spinner = $('#spinner');

        const scrollFunc = () => {
          if (!$('tr:last-child', $el).isInViewport()) return;
          if (loading) return;
          loading = true;
          $spinner.show();

          const date = $el.find('tr:last-of-type').data('date');

          $.ajax({
            url: "{{ route('route.get') }}",
            method: "POST",
            data: {
              _token: "{{ csrf_token() }}",
              date,
            },
            success: function(data) {
              $el.append(data);
              $spinner.hide();
              refreshAll();
              loading = false;
            }
          });
        };

        $(window).on("scroll", scrollFunc);
        scrollFunc();
      });
    </script>
  @endpush

</x-layout.app>
