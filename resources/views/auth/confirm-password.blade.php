@define($gap = 'gap-4')

<x-layout.app :center="true"
  :title="__('confirm password')">
  <x-section.one class="self-center"
    maxWidth="md">

    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('password.confirm') }}">
      @csrf

      <x-modal.header :title="__('Secure area')" />

      <div class="text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
      </div>

      {{-- password --}}
      @define($key = 'password')
      <x-form.wrap :key="$key"
        :value="__('password')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="current-password" />

      </x-form.wrap>

      <div class="flex justify-end">
        <x-button.dark>
          {{ __('confirm') }}
        </x-button.dark>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
