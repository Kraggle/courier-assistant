@define($gap = 'gap-4')

<x-layout.app title="register"
  :center="true">
  <x-section.one class="self-center"
    maxWidth="lg">
    <form class="flex flex-col"
      method="POST"
      action="{{ route('register') }}">
      @csrf

      <x-modal.header title="Create an account" />

      {{-- name --}}
      @define($key = 'name')
      <x-form.wrap value="name"
        :key="$key">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="text"
          :value="old($key)"
          autocomplete="name" />

      </x-form.wrap>

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap value="email"
        :key="$key">

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
      <x-form.wrap value="password"
        :key="$key">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      {{-- confirm password --}}
      @define($key = 'password_confirmation')
      <x-form.wrap value="confirm password"
        :key="$key">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      <div class="flex items-center justify-end">
        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          href="{{ route('login') }}">
          Already registered?
        </a>

        <x-button.dark>
          register
        </x-button.dark>
      </div>
    </form>
  </x-section.one>
</x-layout.app>
