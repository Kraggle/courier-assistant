@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the changes modal --}}
<x-modal class="p-4 md:p-6"
  name="changes-modal">

  <div class="{{ $gap }} flex flex-col">
    {{-- modal header --}}
    <x-modal.header title="none"
      :help="true" />

    <div class="-mx-4 overflow-x-auto md:-mx-6">
      <table class="w-full table-auto whitespace-nowrap text-xs sm:text-sm">

        <x-table.thead>

          @foreach (['Date', 'User', 'Attribute', 'Old', 'New'] as $header)
            <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs">
              {{ __($header) }}
            </x-table.th>
          @endforeach

        </x-table.thead>

        <tbody ref="tbody">

        </tbody>

      </table>
    </div>

    {{-- close --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.dark x-on:click="$dispatch('close')">
        {{ __('close') }}
      </x-button.dark>
    </div>

  </div>

</x-modal>
