@define($gap = 'gap-4')

<x-layout.app :center="true">
  <x-section.wrap class="self-center"
    maxWidth="lg">
    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('register') }}">
      @csrf

      <h1 class="text-center font-serif text-xl font-light uppercase tracking-widest text-gray-400">{{ __('Create an account') }}</h1>

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

        <x-form.text x-ref="{{ $key }}"
          class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
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
  </x-section.wrap>
</x-layout.app>
