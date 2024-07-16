@define($refuels = $vehicle->refuels)

<x-layout.app title="refuels">
  <x-section.one px="">

    @define($tab = $vehicle->id ?? 0)
    <x-tab.container :active="$tab">
      <x-slot:tabs>
        @foreach ($user->vehiclesByDate() as $v)
          <x-tab.link :href="route('refuels', $v->id)"
            :tab="$v->id">
            {{ $v->reg }}
          </x-tab.link>
        @endforeach

        <x-tab.link :href="route('vehicle.show')"
          tab="false">
          New Vehicle
        </x-tab.link>
      </x-slot>

      <x-slot:button>
        <x-button.dark id="addRefuel"
          data-modal="{{ json_encode([
              'title.text' => Msg::add('refuel'),
              'form.action' => route('refuel.add', $vehicle->id),
              'date.value' => old('date', now()->format('Y-m-d')),
              'mileage.value' => old('mileage', ''),
              'cost.value' => old('cost', ''),
              'first.checked' => old('first', false),
              'image-wrap.set-inputs' => old('image-wrap', ''),
              'image-wrap.set-img' => Vite::asset('resources/images/no-image.svg'),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'add',
          ]) }}"
          open-modal="add-refuel"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
          <span class="hidden sm:block">{{ Msg::add('refuel') }}</span>
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
              open-modal="bulk-refuel">
              bulk add
            </x-dropdown.link>

            {{-- export all --}}
            <x-dropdown.link class="cursor-pointer"
              data-modal="{{ json_encode([
                  'title.text' => Msg::exportTitle('refuels'),
                  'question.text' => Msg::exportQuestion('refuels'),
                  'form.action' => route('refuel.export', $vehicle->id),
                  'form.filename' => 'refuel-' . $user->id . '.csv',
              ]) }}"
              open-modal="export-modal">
              Export as CSV
            </x-dropdown.link>
          </x-slot>
        </x-dropdown.wrap>
      </x-slot>

      <x-slot:content>
        <div class="overflow-x-auto">
          <table class="w-full table-auto whitespace-nowrap text-sm md:text-lg">

            <x-table.thead>

              @foreach (['date', 'cost', 'odometer', 'miles', 'ppm', ''] as $header)
                <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs md:text-sm">
                  {{ $header }}
                </x-table.th>
              @endforeach

            </x-table.thead>

            <tbody>

              @foreach ($refuels as $r)
                <x-table.tr class="cursor-pointer"
                  id="editRefuel{{ $r->id }}"
                  open-modal="add-refuel"
                  :data-modal="json_encode([
                      'title.text' => Msg::edit('refuel'),
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
                      'submit.text' => 'save',
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
                      <x-icon class="far fa-receipt cursor-pointer"
                        data-modal="{{ json_encode([
                            'image.src' => $r->getImageURL(),
                        ]) }}"
                        data-tooltip-position="left"
                        title="{{ Str::title('receipt') }}"
                        open-modal="show-receipt" />
                    @endif

                    <x-icon class="far fa-edit cursor-pointer text-orange-400"
                      data-tooltip-position="left"
                      title="{{ Str::title('edit') }}" />
                  </x-table.td>

                </x-table.tr>
              @endforeach

            </tbody>

          </table>

          @if ($refuels->count() == 0)
            <div class="px-6 pt-6 text-center">{{ Msg::noResults('refuels') }}</div>
          @endif

        </div>
      </x-slot>

    </x-tab.container>
  </x-section.one>

  @include('refuel.modal.add')
  @include('refuel.modal.bulk')
  @include('modal.receipt')
  @include('modal.export')
  @include('refuel.modal.destroy')

</x-layout.app>
