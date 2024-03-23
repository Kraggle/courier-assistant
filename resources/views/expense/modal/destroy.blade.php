@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-expense">
  <form x-ref="form"
    class="{{ $gap }} flex flex-col"
    method="POST"
    action="">
    @csrf

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div x-ref="title"
        class="font-extralight uppercase tracking-wider">
        {{ Msg::delete(__('expense')) }}
      </div>
    </div>

    {{-- modal content --}}
    <p class="text-sm">
      {{ Msg::sureDelete(__('expense')) }}
    </p>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('no') }}
      </x-button.light>

      <x-button.dark x-ref="submit"
        class="">
        {{ __('yes') }}
      </x-button.dark>
    </div>

  </form>
</x-modal>
