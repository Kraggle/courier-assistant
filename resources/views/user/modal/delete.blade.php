@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

<x-modal class="p-4 md:p-6"
  name="confirm-user-deletion"
  :show="$errors->userDeletion->isNotEmpty()">
  <form class="{{ $gap }} flex flex-col"
    method="post"
    action="{{ route('user.destroy') }}">
    @csrf
    @method('delete')

    <h2 class="text-lg font-medium text-gray-900">
      {{ __('Are you sure you want to delete your account?') }}
    </h2>

    <p class="text-sm text-gray-600">
      {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
    </p>

    <x-form.label class="sr-only"
      for="password_modal"
      value="{{ __('password') }}" />

    <x-form.text class="block w-full placeholder:capitalize"
      id="password_modal"
      name="password"
      type="password"
      placeholder="{{ __('password') }}" />

    <x-form.error :messages="$errors->userDeletion->get('password')" />

    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('cancel') }}
      </x-button.light>

      <x-button.danger>
        {{ __('delete account') }}
      </x-button.danger>
    </div>
  </form>
</x-modal>
