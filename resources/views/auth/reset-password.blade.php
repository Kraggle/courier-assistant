@define($gap = 'gap-4')

<x-layout.app :center="true"
  :title="__('password reset')">
  <x-section.one class="self-center"
    maxWidth="lg">

    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('password.store') }}">
      @csrf

      <x-modal.header :title="__('Reset your password')" />

      {{-- Password Reset Token --}}
      <input name="token"
        type="hidden"
        value="{{ $request->route('token') }}">

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap :key="$key"
        :value="__('email')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          :value="old($key, $request->email)"
          autofocus
          autocomplete="username" />

      </x-form.wrap>

      {{-- password --}}
      @define($key = 'password')
      <x-form.wrap :key="$key"
        :value="__('new password')">

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

      <div class="flex items-center justify-end">
        <x-button.dark>
          {{ __('reset password') }}
        </x-button.dark>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
