@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-info">
  <form class="{{ $gap }} flex flex-col"
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
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        no
      </x-button.light>

      <x-button.dark class=""
        ref="submit">
        yes
      </x-button.dark>
    </div>

  </form>
</x-modal>
