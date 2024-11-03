{{-- the add modal --}}
<x-modal class="p-4 md:p-6"
  name="add-depot"
  help-root
  maxWidth="sm">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('depot.store') }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add('depot')"
      :help="false" />

    {{-- location --}}
    @define($key = 'location')
    <x-form.wrap value="location"
      :key="$key">

      <x-form.text id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        placeholder="e.g. Chester, Liverpool..." />

    </x-form.wrap>

    {{-- abbreviation --}}
    @define($key = 'identifier')
    <x-form.wrap value="identifier"
      :key="$key">

      <x-form.text id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        placeholder="e.g. DCE1, DXM4..." />

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
