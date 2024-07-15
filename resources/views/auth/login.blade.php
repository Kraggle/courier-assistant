@define($gap = 'gap-4')

<x-layout.app title="sign in"
  :center="true">
  <x-section.one class="self-center"
    maxWidth="md">

    <form class="flex flex-col"
      method="POST"
      action="{{ route('login') }}">
      @csrf

      <x-modal.header title="Sign in" />

      {{-- Session Status --}}
      <x-notify.status :status="session('status')" />

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap value="email"
        :key="$key">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          :value="old($key, env('TEST_EMAIL', ''))"
          autofocus
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
          :value="env('TEST_PASSWORD', '')"
          autocomplete="current-password" />

      </x-form.wrap>

      <div class="flex items-center justify-end">

        @if (Route::has('password.request'))
          <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            href="{{ route('password.request') }}">
            Forgot your password?
          </a>
        @endif

        <x-button.dark>
          Log in
        </x-button.dark>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
