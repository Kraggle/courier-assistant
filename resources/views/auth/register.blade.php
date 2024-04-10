@define($gap = 'gap-4')

<x-layout.app :center="true">
  <x-section.one class="self-center"
    maxWidth="lg">
    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('register') }}">
      @csrf

      <x-modal.header :title="__('Create an account')" />

      {{-- name --}}
      @define($key = 'name')
      <x-form.wrap :key="$key"
        :value="__('name')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="text"
          :value="old($key)"
          autocomplete="name" />

      </x-form.wrap>

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap :key="$key"
        :value="__('email')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          ref="{{ $key }}"
          :value="old($key)"
          autocomplete="username" />

      </x-form.wrap>

      {{-- password --}}
      @define($key = 'password')
      <x-form.wrap :key="$key"
        :value="__('password')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      {{-- confirm password --}}
      @define($key = 'password_confirmation')
      <x-form.wrap :key="$key"
        :value="__('confirm password')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      <div class="{{ $gap }} flex items-center justify-end">
        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          href="{{ route('login') }}">
          {{ __('Already registered?') }}
        </a>

        <x-button.dark>
          {{ __('register') }}
        </x-button.dark>
      </div>
    </form>
  </x-section.one>
</x-layout.app>
