<x-layout.app :title="__('depot')">

  <x-section.wrap class="sm:mx-auto sm:max-w-2xl">
    <h1 class="text-2xl">{{ Msg::add(__('depot')) }}</h1>

    <form x-ref="form"
      method="POST"
      action="{{ route('depot.store') }}">
      @csrf

      {{-- location --}}
      @define($key = 'location')
      <x-form.wrap :key="$key"
        :value="__('location')">

        <x-form.text x-ref="{{ $key }}"
          class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          placeholder="e.g. Chester, Liverpool..." />

      </x-form.wrap>

      {{-- abbreviation --}}
      @define($key = 'identifier')
      <x-form.wrap :key="$key"
        :value="__('identifier')">

        <x-form.text x-ref="{{ $key }}"
          class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          placeholder="e.g. DCE1, DXM4..." />

      </x-form.wrap>

      {{-- submit --}}
      <div class="mt-6 flex justify-end">
        <x-button.dark x-ref="submit"
          class="ms-3">
          {{ __('add') }}
        </x-button.dark>
      </div>

    </form>
  </x-section.wrap>

</x-layout.app>
