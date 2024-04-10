@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="extra-route"
  maxWidth="sm">

  <div class="{{ $gap }} flex flex-col">

    {{-- modal header --}}
    <x-modal.header :title="__('Extra Information!')" />

    <x-form.section ref="note-wrap"
      :label="__('note')">
      <p class="-m-2"
        ref="note"></p>
    </x-form.section>

    <x-form.section ref="bonus-wrap"
      :label="__('bonus')">
      <p class="-m-2">£<span ref="bonus"></span></p>
    </x-form.section>

    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('close') }}
      </x-button.light>
    </div>

  </div>
</x-modal>
