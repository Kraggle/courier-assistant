{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-refuel">
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="">
    @csrf
    @method('delete')

    {{-- modal header --}}
    <x-modal.header :title="Msg::delete('refuel')" />

    {{-- modal content --}}
    <p class="text-sm">
      {{ Msg::sureDelete('refuel') }}
    </p>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        no
      </x-button.light>

      <x-button.dark ref="submit">
        yes
      </x-button.dark>
    </div>

  </form>
</x-modal>
