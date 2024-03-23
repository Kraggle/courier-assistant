@define($refuels = $vehicle->refuels)

<x-layout.app :title="__('refuels')">
  <x-section.wrap px="">
    <x-tab.link-wrap class="mb-4 px-4 text-2xl font-medium text-gray-900 md:mb-5 md:px-6">
      <x-slot name="tabs">
        @foreach ($user->vehiclesByDate() as $v)
          <x-tab.link :href="route('refuels', $v->id)"
            :active="$v->id == $vehicle->id">
            {{ $v->reg }}
          </x-tab.link>
        @endforeach

        <x-tab.link :href="route('vehicle.show')"
          :active="false">
          {{ __('New Vehicle') }}
        </x-tab.link>
      </x-slot>

      <x-slot name="button">

        <x-button.dark x-data=""
          x-on:click.prevent="$dispatch('open-modal', 'add-refuel')"
          id="addRefuel"
          data-modal="{{ json_encode([
              'title.text' => Msg::add(__('refuel')),
              'form.action' => route('refuel.add', $vehicle->id),
              'date.value' => old('date', now()->format('Y-m-d')),
              'mileage.value' => old('mileage', ''),
              'cost.value' => old('cost', ''),
              'first.checked' => old('first', false),
              'image-wrap.set-inputs' => old('image-wrap', ''),
              'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
              'destroy.addclass' => 'hidden',
              'submit.text' => __('add'),
          ]) }}"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
          <span class="hidden sm:block">{{ Msg::add(__('refuel')) }}</span>
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
              x-on:click.prevent="$dispatch('open-modal', 'bulk-refuel')"
              class="cursor-pointer">
              {{ __('bulk add') }}
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'export-modal')"
              class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle(__('refuels')),
                  'question.text' => Msg::exportQuestion(__('refuels')),
                  'form.action' => route('refuel.export', $vehicle->id),
                  'form.filename' => 'refuel-' . $user->id . '.csv',
              ]) }}">
              {{ __('Export as CSV') }}
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>

      </x-slot>

    </x-tab.link-wrap>

    <div class="overflow-x-auto">
      <table class="w-full table-auto whitespace-nowrap text-sm md:text-lg">

        <x-table.thead>

          @foreach ([__('date'), __('cost'), __('odometer'), __('miles'), __('ppm'), ''] as $header)
            <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs md:text-sm">
              {{ __($header) }}
            </x-table.th>
          @endforeach

        </x-table.thead>

        <tbody>

          @foreach ($refuels as $r)
            <x-table.tr x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'add-refuel')"
              class="cursor-pointer"
              id="editRefuel{{ $r->id }}"
              :data-modal="json_encode([
                  'title.text' => Msg::edit(__('refuel')),
                  'form.action' => route('refuel.edit', $r->id),
                  'date.value' => old('date', $r->date->format('Y-m-d')),
                  'mileage.value' => old('mileage', $r->mileage),
                  'cost.value' => old('cost', $r->cost),
                  'first.checked' => old('first', K::isTrue($r->first)),
                  'image-wrap.set-inputs' => old('image-wrap', ''),
                  'image-wrap.set-img' => $r->getImageURL() ?? Vite::asset('resources/images/no-image.svg'),
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('refuel.destroy', $r->id),
                      ],
                  ],
                  'submit.text' => __('save'),
              ])">

              {{-- date --}}
              <x-table.td>{{ $r->date->format('D, jS M \'y') }}</x-table.td>

              {{-- cost --}}
              <x-table.td>{{ '£' . number_format($r->cost, 2) }}</x-table.td>

              {{-- odometer --}}
              <x-table.td> {{ number_format($r->mileage, 0) }}</x-table.td>

              {{-- miles --}}
              <x-table.td>{{ number_format($r->miles) }}</x-table.td>

              {{-- ppm --}}
              <x-table.td>{{ number_format($r->fuel_rate, 4) . 'p' }}</x-table.td>

              {{-- actions --}}
              <x-table.td class="flex justify-end gap-4 text-base sm:text-xl">
                @if ($r->hasImage())
                  <x-icon x-data=""
                    x-on:click.prevent.stop="$dispatch('open-modal', 'show-receipt')"
                    class="far fa-receipt cursor-pointer"
                    data-modal="{{ json_encode([
                        'image.src' => $r->getImageURL(),
                    ]) }}"
                    data-tooltip-position="left"
                    title="{{ Str::title(__('receipt')) }}" />
                @endif

                <x-icon class="far fa-edit cursor-pointer text-orange-400"
                  data-tooltip-position="left"
                  title="{{ Str::title(__('edit')) }}" />
              </x-table.td>

            </x-table.tr>
          @endforeach

        </tbody>

      </table>

      @if ($refuels->count() == 0)
        <div class="px-6 pt-6 text-center">{{ Msg::noResults(__('refuels')) }}</div>
      @endif

    </div>

  </x-section.wrap>

  @include('refuel.modal.add')
  @include('refuel.modal.bulk')
  @include('modal.receipt')
  @include('modal.export')
  @include('refuel.modal.destroy')

</x-layout.app>
