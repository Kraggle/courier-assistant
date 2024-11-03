{{-- the add modal --}}
<x-modal class="p-4 md:p-6"
  name="add-vehicle"
  help-root
  maxWidth="sm">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('vehicle.create') }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add('vehicle')"
      :help="false" />

    {{-- reg input --}}
    @define($key = 'reg')
    <x-form.wrap value="Vehicle Regestration"
      :key="$key">

      <x-form.text class="block w-full text-center text-2xl font-extrabold uppercase"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader>
        <x-slot:text
          ref="submit">add</x-slot>
        <x-slot:loader></x-slot>
      </x-button.loader>
    </div>

  </form>
</x-modal>
