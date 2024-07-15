{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="extra-route"
  maxWidth="sm">

  <div class="flex flex-col">

    {{-- modal header --}}
    <x-modal.header title="Extra Information!" />

    <x-form.section ref="note-wrap"
      label="note">
      <p class="-m-2"
        ref="note"></p>
    </x-form.section>

    <x-form.section ref="bonus-wrap"
      label="bonus">
      <p class="-m-2">£<span ref="bonus"></span></p>
    </x-form.section>

    <div class="flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        close
      </x-button.light>
    </div>

  </div>
</x-modal>
