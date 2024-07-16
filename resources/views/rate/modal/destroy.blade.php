{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-rate">
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="">
    @csrf
    @method('DELETE')

    {{-- modal header --}}
    <x-modal.header :title="Msg::delete('rate')" />

    {{-- modal content --}}
    <p class="text-sm">
      {{ Msg::sureDelete('rate') }}
    </p>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        no
      </x-button.light>

      <x-button.dark class=""
        ref="submit">
        yes
      </x-button.dark>
    </div>

  </form>
</x-modal>
