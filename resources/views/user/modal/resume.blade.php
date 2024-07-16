<x-modal class="p-4 md:p-6"
  name="resume-subscription"
  maxWidth="md">

  <form class="flex flex-col"
    method="post"
    action="{{ route('subscription.resume') }}">
    @csrf
    @method('PATCH')

    <h2 class="text-lg font-medium text-gray-900">
      You are about to resume your subscription!
    </h2>

    <p class="text-sm text-gray-600">
      Thank you for returning, you will not regret it.
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

      <x-button.dark class="bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800">
        resume subscription
      </x-button.dark>
    </div>
  </form>
</x-modal>
