@php
  $user = K::user();
  $dsps = $user->dsps;
@endphp

<x-layout.app title="Your DSPs">

  <x-section.one px="0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        Delivery Service Providers
      </x-slot>

      <x-slot:buttons>

        <x-button.dark id="addDSP"
          data-modal="{{ json_encode([
              'title.text' => 'Select your DSP',
              'form.action' => route('dsp.attach'),
              'date.value' => old('date', now()->format('Y-m-d')),
              'dsp_wrap.removeclass' => 'hidden',
              'dsp_id.value' => old('dsp_id', ''),
              'destroy.addclass' => 'hidden',
              'submit.text' => 'select',
          ]) }}"
          open-modal="add-dsp"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
          <span class="hidden sm:block">{{ Msg::add('DSP') }}</span>
          <span class="block sm:hidden">add</span>
        </x-button.dark>
      </x-slot>
    </x-section.title>

    <div class="max-w-[100vw] overflow-x-auto pb-3">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg">

        <x-table.thead>

          @foreach (['from date', 'name', 'identifier', 'no. of drivers', ''] as $header)
            <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs md:text-sm">
              {{ $header }}
            </x-table.th>
          @endforeach

        </x-table.thead>

        <tbody>

          @foreach ($dsps as $dsp)
            <x-table.tr class="cursor-pointer"
              id="editDSP{{ $dsp->id }}"
              open-modal="add-dsp"
              :data-modal="json_encode([
                  'title.text' => Msg::edit('DSP connection'),
                  'form.action' => route('dsp.edit', $dsp->id),
                  'date.value' => old('date', K::date($dsp->pivot->date)->format('Y-m-d')),
                  'dsp_id.value' => old('dsp_id', $dsp->id),
                  'dsp_wrap.addclass' => 'hidden',
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('dsp.detach', $dsp->id),
                      ],
                  ],
                  'submit.text' => 'save',
              ])">

              {{-- date --}}
              <x-table.td class="whitespace-nowrap pr-2 text-sm md:text-lg">
                {{ K::date($dsp->pivot->date)->format('jS F Y') }}
              </x-table.td>

              {{-- name --}}
              <x-table.td class="whitespace-nowrap px-2 text-sm md:text-lg">
                {{ $dsp->name }}
              </x-table.td>

              {{-- abbr --}}
              <x-table.td class="whitespace-nowrap px-2 text-sm md:text-lg">
                {{ $dsp->identifier }}
              </x-table.td>

              {{-- count --}}
              <x-table.td class="whitespace-nowrap px-2 text-sm md:text-lg">
                {{ $dsp->count }}
              </x-table.td>

              {{-- action --}}
              <x-table.td class="text-sm md:text-lg">
                <x-icon class="far fa-edit cursor-pointer text-orange-400"
                  data-tooltip-position="left"
                  title="{{ Str::title('edit') }}" />
              </x-table.td>
            </x-table.tr>
          @endforeach

        </tbody>

      </table>

      @if (!$user->hasDSP())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults('DSPs') }}</div>
      @endif
    </div>

  </x-section.one>

  @include('dsp.modal.add')
  @include('dsp.modal.create')
  @include('dsp.modal.destroy')

</x-layout.app>
