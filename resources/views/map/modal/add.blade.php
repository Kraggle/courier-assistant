{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-info"
  maxWidth="sm"
  help-root>

  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('info.add') }}">
    @csrf

    <input name="_method"
      type="hidden"
      value="POST"
      ref="method">

    {{-- modal header --}}
    <x-modal.header title="none"
      :help="true" />

    @define($key = 'id')
    <input id="{{ $key }}"
      name="{{ $key }}"
      type="hidden"
      ref="{{ $key }}">

    @define($key = 'lat')
    <input id="{{ $key }}"
      name="{{ $key }}"
      type="hidden"
      ref="{{ $key }}">

    @define($key = 'lng')
    <input id="{{ $key }}"
      name="{{ $key }}"
      type="hidden"
      ref="{{ $key }}">
    <x-form.error class="mt-2"
      :messages="$errors->get($key)" />

    {{-- address --}}
    @define($key = 'address')
    <x-form.wrap value="address"
      :key="$key"
      help="The address of the location.">

      <x-form.textarea id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        placeholder="20 High St, Supertown, AB12 3DE" />

    </x-form.wrap>

    {{-- note --}}
    @define($key = 'note')
    <x-form.wrap value="note"
      :key="$key"
      help="The note you want to display at this location.">

      <x-form.textarea id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        placeholder="The note you want to either save for future reference or share with other users" />

    </x-form.wrap>

    <div class="flex justify-between">
      <x-button.danger class="no-loader"
        open-modal="destroy-info"
        ref="destroy">
        delete
      </x-button.danger>

      <span></span>

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
    </div>

  </form>
</x-modal>
