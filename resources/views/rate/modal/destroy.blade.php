@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-rate">
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    action="">
    @csrf
    @method('DELETE')

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div class="font-extralight uppercase tracking-wider"
        ref="title">
        {{ Msg::delete(__('rate')) }}
      </div>
    </div>

    {{-- modal content --}}
    <p class="text-sm">
      {{ Msg::sureDelete(__('rate')) }}
    </p>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('no') }}
      </x-button.light>

      <x-button.dark class=""
        ref="submit">
        {{ __('yes') }}
      </x-button.dark>
    </div>

  </form>
</x-modal>
