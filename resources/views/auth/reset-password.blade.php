@define($gap = 'gap-4')

<x-layout.app title="password reset"
  :center="true">
  <x-section.one class="self-center"
    maxWidth="lg">

    <form class="flex flex-col"
      method="POST"
      action="{{ route('password.store') }}">
      @csrf

      <x-modal.header title="Reset your password" />

      {{-- Password Reset Token --}}
      <input name="token"
        type="hidden"
        value="{{ $request->route('token') }}">

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap value="email"
        :key="$key">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          :value="old($key, $request->email)"
          autofocus
          autocomplete="username" />

      </x-form.wrap>

      {{-- password --}}
      @define($key = 'password')
      <x-form.wrap value="new password"
        :key="$key">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      {{-- confirm password --}}
      @define($key = 'password_confirmation')
      <x-form.wrap value="confirm password"
        :key="$key">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="new-password" />

      </x-form.wrap>

      <div class="flex items-center justify-end">
        <x-button.loader>
          <x-slot:text>reset password</x-slot>
          <x-slot:loader></x-slot>
        </x-button.loader>
      </div>
    </form>

  </x-section.one>
</x-layout.app>
