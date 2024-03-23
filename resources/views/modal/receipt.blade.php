@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="show-receipt"
  maxWidth="sm">

  {{-- modal content --}}
  <div class="{{ $gap }} flex flex-col">

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div class="font-extralight uppercase tracking-wider">
        {{ __('receipt') }}
      </div>
    </div>

    <img x-ref="image"
      src="{{ Vite::asset('resources/images/no-image.svg') }}">

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <form x-ref="form"
        method="POST"
        action="">
        @csrf

        <input x-ref="path"
          name="path"
          type="hidden" />

        <x-button.dark color="no-loader bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800">
          {{ __('download') }}
        </x-button.dark>

      </form>

      <x-button.light x-on:click="$dispatch('close')">
        {{ __('close') }}
      </x-button.light>
    </div>

  </div>
</x-modal>
