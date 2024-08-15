{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-info">
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('info.destroy') }}">
    @csrf
    @method('delete')

    <input name="_method"
      type="hidden"
      value="DELETE"
      ref="method">

    @define($key = 'id')
    <input id="{{ $key }}"
      name="{{ $key }}"
      type="hidden"
      ref="{{ $key }}">

    {{-- modal header --}}
    <x-modal.header :title="Msg::delete('Location Information')" />
    {{-- modal content --}}
    <p class="text-sm">
      {{ Msg::sureDelete('Location Information') }}
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
