<x-layout.app :title="__('depot')">

  <x-section.one class="sm:mx-auto sm:max-w-2xl">
    <h1 class="text-2xl">{{ Msg::add(__('depot')) }}</h1>

    <form ref="form"
      method="POST"
      action="{{ route('depot.store') }}">
      @csrf
      @method('PUT')

      {{-- location --}}
      @define($key = 'location')
      <x-form.wrap :key="$key"
        :value="__('location')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="e.g. Chester, Liverpool..." />

      </x-form.wrap>

      {{-- abbreviation --}}
      @define($key = 'identifier')
      <x-form.wrap :key="$key"
        :value="__('identifier')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="e.g. DCE1, DXM4..." />

      </x-form.wrap>

      {{-- submit --}}
      <div class="mt-6 flex justify-end">
        <x-button.dark class="ms-3"
          ref="submit">
          {{ __('add') }}
        </x-button.dark>
      </div>

    </form>
  </x-section.one>

</x-layout.app>
