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
      <x-button.light close-modal>
        no
      </x-button.light>

      <x-button.loader>
        <x-slot:text
          ref="submit">yes</x-slot>
        <x-slot:loader></x-slot>
      </x-button.loader>
    </div>

  </form>
</x-modal>
