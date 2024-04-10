<x-layout.app :title="__('vehicle')">

  <x-section.one maxWidth="2xl">
    <form class="flex flex-col gap-6"
      method="POST"
      action="{{ route('vehicle.create') }}">
      @csrf
      @method('PUT')

      <h1 class="text-2xl font-light uppercase tracking-wider">{{ __('Add your first vehicle!') }}</h1>

      {{-- reg input --}}
      @define($key = 'reg')
      <x-form.wrap :key="$key"
        :value="__('Vehicle Regestration')">

        <x-form.text class="block w-full text-center text-5xl font-extrabold uppercase"
          id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}" />

      </x-form.wrap>

      {{-- submit --}}
      <div class="flex justify-end">
        <x-button.dark>
          {{ __('add') }}
        </x-button.dark>
      </div>

    </form>
  </x-section.one>

</x-layout.app>
