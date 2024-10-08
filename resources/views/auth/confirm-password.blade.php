@define($gap = 'gap-4')

<x-layout.app title="confirm password"
  :center="true">
  <x-section.one class="self-center"
    maxWidth="md">

    <form class="flex flex-col"
      method="POST"
      action="{{ route('password.confirm') }}">
      @csrf

      <x-modal.header title="Secure area" />

      <div class="text-sm text-gray-600">
        This is a secure area of the application. Please confirm your password before continuing.
      </div>

      {{-- password --}}
      @define($key = 'password')
      <x-form.wrap value="password"
        :key="$key">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="current-password" />

      </x-form.wrap>

      <div class="flex justify-end">
        <x-button.loader>
          <x-slot:text>confirm</x-slot>
          <x-slot:loader></x-slot>
        </x-button.loader>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
