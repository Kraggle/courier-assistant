<x-layout.app title="depot creator">

  <x-section.one class="sm:mx-auto sm:max-w-2xl">
    <h1 class="text-2xl">{{ Msg::add('depot') }}</h1>

    <form ref="form"
      method="POST"
      action="{{ route('depot.store') }}">
      @csrf
      @method('PUT')

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
      <div class="mt-6 flex justify-end">
        <x-button.loader class="ms-3">
          <x-slot:text
            ref="submit">add</x-slot>
          <x-slot:loader></x-slot>
        </x-button.loader>
      </div>

    </form>
  </x-section.one>

</x-layout.app>
