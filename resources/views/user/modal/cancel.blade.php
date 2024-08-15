<x-modal class="p-4 md:p-6"
  name="cancel-subscription"
  maxWidth="md">

  <form class="flex flex-col"
    method="post"
    action="{{ route('subscription.cancel') }}">
    @csrf
    @method('DELETE')

    <h2 class="text-lg font-medium text-gray-900">
      Are you sure you want to cancel your subscription?
    </h2>

    <p class="text-sm text-gray-600">
      Once your subscribed period has ended you will not be able to add any new data. You will have to subscribe once again to access your account. After 3 months your data will be removed. We will notify you when this is drawing near.
    </p>

    @define($key = 'password')
    <x-form.wrap>

      <x-form.text class="block w-full placeholder:capitalize"
        name="{{ $key }}"
        type="password"
        placeholder="password" />

    </x-form.wrap>

    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader class="bg-red-500 hover:bg-red-400 focus:bg-red-600 focus:ring-red-400 active:bg-red-600">
        <x-slot:text
          ref="submit">cancel subscription</x-slot>
        <x-slot:loader
          class="text-red-400"></x-slot>
      </x-button.loader>
    </div>
  </form>
</x-modal>
