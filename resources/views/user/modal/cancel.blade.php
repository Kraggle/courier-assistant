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
      <x-button.light x-on:click="$dispatch('close')">
        cancel
      </x-button.light>

      <x-button.danger>
        cancel subscription
      </x-button.danger>
    </div>
  </form>
</x-modal>
