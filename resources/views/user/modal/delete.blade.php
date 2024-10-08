<x-modal class="p-4 md:p-6"
  name="confirm-user-deletion"
  :show="$errors->userDeletion->isNotEmpty()">
  <form class="flex flex-col"
    method="post"
    action="{{ route('user.destroy') }}">
    @csrf
    @method('delete')

    <h2 class="text-lg font-medium text-gray-900">
      Are you sure you want to delete your account?
    </h2>

    <p class="text-sm text-gray-600">
      Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
    </p>

    <x-form.label class="sr-only"
      for="password_modal"
      value="password" />

    <x-form.text class="block w-full placeholder:capitalize"
      id="password_modal"
      name="password"
      type="password"
      placeholder="password" />

    <x-form.error :messages="$errors->userDeletion->get('password')" />

    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader class="bg-red-500 hover:bg-red-400 focus:bg-red-600 focus:ring-red-400 active:bg-red-600">
        <x-slot:text
          ref="submit">delete account</x-slot>
        <x-slot:loader
          class="text-red-400"></x-slot>
      </x-button.loader>
    </div>
  </form>
</x-modal>
