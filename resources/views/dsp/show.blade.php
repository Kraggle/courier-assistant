@php
  $user = K::user();
  $dsps = $user->dsps;
@endphp

<x-layout.app :title="__('DSPs')">

  <x-section.wrap px="0">
    <x-section.title class="px-4 md:px-6">
      <x-slot:title>
        {{ __('Delivery Service Providers') }}
      </x-slot>

      <x-slot:buttons>

        <x-button.dark x-data=""
          x-on:click.prevent="$dispatch('open-modal', 'add-dsp')"
          id="addDSP"
          data-modal="{{ json_encode([
              'title.text' => __('Select your Delivery Service Provider'),
              'form.action' => route('dsp.attach'),
              'date.value' => old('date', now()->format('Y-m-d')),
              'dsp_id.value' => old('dsp_id', ''),
              'dsp_wrap.removeclass' => 'hidden',
              'close.addclass' => 'hidden',
              'add-section.removeclass' => 'hidden',
              'destroy.addclass' => 'hidden',
              'submit.text' => __('select'),
          ]) }}"
          color="bg-violet-800 hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
          <span class="hidden sm:block">{{ Msg::add(__('DSP')) }}</span>
          <span class="block sm:hidden">{{ __('add') }}</span>
        </x-button.dark>
      </x-slot>
    </x-section.title>

    <div class="max-w-[100vw] overflow-x-auto pb-3">
      <table class="w-full table-auto whitespace-nowrap text-sm sm:text-lg">

        <x-table.thead>

          @foreach ([__('from date'), __('name'), __('identifier'), __('no. of drivers'), ''] as $header)
            <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs md:text-sm">
              {{ __($header) }}
            </x-table.th>
          @endforeach

        </x-table.thead>

        <tbody>

          @foreach ($dsps as $dsp)
            <x-table.tr x-data=""
              x-on:click.prevent="$dispatch('open-modal', 'add-dsp')"
              class="cursor-pointer"
              id="editDSP{{ $dsp->id }}"
              :data-modal="json_encode([
                  'title.text' => Msg::edit(__('DSP connection')),
                  'form.action' => route('dsp.edit', $dsp->id),
                  'date.value' => old('date', K::date($dsp->pivot->date)->format('Y-m-d')),
                  'dsp_id.value' => old('dsp_id', $dsp->id),
                  'dsp_wrap.addclass' => 'hidden',
                  'close.removeclass' => 'hidden',
                  'add-section.addclass' => 'hidden',
                  'destroy.removeclass' => 'hidden',
                  'destroy.data' => [
                      'modal' => [
                          'form.action' => route('dsp.detach', $dsp->id),
                      ],
                  ],
                  'submit.text' => __('save'),
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
                  title="{{ Str::title(__('edit')) }}" />
              </x-table.td>
            </x-table.tr>
          @endforeach

        </tbody>

      </table>

      @if (!$user->hasDSP())
        <div class="px-6 pt-6 text-center">{{ Msg::noResults(__('DSPs')) }}</div>
      @endif
    </div>

  </x-section.wrap>

  @include('dsp.modal.add')
  @include('dsp.modal.destroy')

</x-layout.app>
