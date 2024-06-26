@define($gap = 'gap-4')

<x-layout.app :center="true"
  :title="__('forgot password')">
  <x-section.one class="self-center"
    maxWidth="lg">

    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('password.email') }}">
      @csrf

      <x-modal.header :title="__('Forgot your password?')" />

      <div class="text-sm text-gray-600">
        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
      </div>

      {{-- Session Status --}}
      <x-notify.status :status="session('status')" />

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap class=""
        :key="$key"
        :value="__('email')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          :value="old($key)"
          autofocus
          autocomplete="username" />

      </x-form.wrap>

      <div class="{{ $gap }} flex items-center justify-end">
        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          href="{{ route('login') }}">
          {{ __('Back to login?') }}
        </a>

        <x-button.dark>
          {{ __('Email Password Reset Link') }}
        </x-button.dark>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
