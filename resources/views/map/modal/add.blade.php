@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-info"
  maxWidth="sm"
  help-root>

  {{-- modal content --}}
  <form class="{{ $gap }} flex flex-col"
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
    <x-form.wrap :key="$key"
      :value="__('address')"
      :help="__('The address of the location.')">

      <x-form.textarea class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        placeholder="20 High St, Supertown, AB12 3DE" />

    </x-form.wrap>

    {{-- note --}}
    @define($key = 'note')
    <x-form.wrap :key="$key"
      :value="__('note')"
      :help="__('The note you want to display at this location.')">

      <x-form.textarea class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}"
        :placeholder="__('The note you want to either save for future reference or share with other users')" />

    </x-form.wrap>

    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-info')"
        class="no-loader"
        ref="destroy">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-on:click="$dispatch('close')">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark ref="submit">
          {{ __('add') }}
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
