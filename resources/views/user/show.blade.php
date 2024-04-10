@define($gap = 'gap-3'; $user = K::user();)

<x-layout.app :title="__('profile')">

  {{-- profile --}}
  <x-section.one class="{{ $gap }} flex flex-col"
    maxWidth="2xl">
    <h2 class="text-lg font-medium capitalize text-gray-900">
      {{ __('profile information') }}
    </h2>

    <p class="text-sm text-gray-600">
      {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification"
      method="post"
      action="{{ route('verification.send') }}">
      @csrf
    </form>

    <form class="{{ $gap }} flex flex-col"
      method="post"
      action="{{ route('user.update') }}">
      @csrf
      @method('patch')

      {{-- name --}}
      @define($key = 'name')
      <x-form.wrap :key="$key"
        :value="__('name')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="text"
          :value="old($key, $user->name)"
          autofocus
          autocomplete="name" />

      </x-form.wrap>

      {{-- email --}}
      @define($key = 'email')
      <x-form.wrap :key="$key"
        :value="__('email')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="email"
          :value="old($key, $user->email)"
          autocomplete="username" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
          <div class="{{ $gap }} flex flex-col">
            <p class="text-sm text-gray-800">
              {{ __('Your email address is unverified.') }}

              <button class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                form="send-verification">
                {{ __('Click here to re-send the verification email.') }}
              </button>
            </p>
          </div>
        @endif

      </x-form.wrap>

      <div class="{{ $gap }} flex items-center justify-end">
        <x-button.dark>{{ __('save') }}</x-button.dark>
      </div>
    </form>
  </x-section.one>

  {{-- update password --}}
  <x-section.one class="{{ $gap }} flex flex-col"
    maxWidth="2xl">
    <h2 class="text-lg font-medium capitalize text-gray-900">
      {{ __('update password') }}
    </h2>

    <p class="text-sm text-gray-600">
      {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form class="{{ $gap }} flex flex-col"
      method="post"
      action="{{ route('password.update') }}">
      @csrf
      @method('put')

      {{-- current password --}}
      @define($key = 'current_password')
      <x-form.wrap :key="$key"
        :value="__('current password')">

        <x-form.text class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          type="password"
          autocomplete="current-password" />

      </x-form.wrap>

      {{-- new password --}}
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

      <div class="{{ $gap }} flex items-center justify-end">
        <x-button.dark>{{ __('save') }}</x-button.dark>
      </div>
    </form>
  </x-section.one>

  {{-- subscription --}}
  <x-section.one class="{{ $gap }} flex flex-col"
    maxWidth="2xl">
    <h2 class="text-lg font-medium capitalize text-gray-900">
      {{ __('subscription') }}
    </h2>

    {{-- check if subscribed --}}
    @define($sub = $user->subscription('default'))
    @if ($user->subscribed())
      @if ($sub->onGracePeriod())
        {{-- cancelled --}}
        <p class="text-sm text-gray-600">
          {{ __('You are not currently subscribed to this app, your subscription ends on :date, but you can resume from here.', ['date' => K::displayDate($sub->ends_at, 'M jS')]) }}
        </p>

        <div class="{{ $gap }} flex items-center justify-end">
          <x-button.dark x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'resume-subscription')"
            id="resumeSubscription">
            {{ __('resume') }}
          </x-button.dark>
        </div>
      @else
        @if ($sub->onTrial())
          {{-- on trial --}}
          <p class="text-sm text-gray-600">
            {{ __('You are currently on your trial period! This ends in :days days.', ['days' => K::date($sub->trial_ends_at)->diffInDays(now())]) }}
          </p>
        @else
          {{-- subscribed --}}
          <p class="text-sm text-gray-600">
            {{ __('You are currently subscribed to this app on a monthly subscription! Thank you!') }}
          </p>
        @endif

        <div class="{{ $gap }} flex items-center justify-end">
          <x-button.dark x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'cancel-subscription')"
            id="cancelSubscription">
            {{ __('cancel') }}
          </x-button.dark>
        </div>
      @endif
    @else
      <p class="text-sm text-gray-600">
        {{ __('You are not yet subscribed to this app. To subscribe, please click below.') }}
      </p>

      <div class="{{ $gap }} flex items-center justify-end">
        <x-button.dark :href="route('subscription')">{{ __('subscribe') }}</x-button.dark>
      </div>
    @endif
  </x-section.one>

  {{-- delete --}}
  <x-section.one class="{{ $gap }} flex flex-col"
    maxWidth="2xl">
    <h2 class="text-lg font-medium capitalize text-gray-900">
      {{ __('delete account') }}
    </h2>

    <p class="text-sm text-gray-600">
      {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <div class="{{ $gap }} flex items-center justify-end">
      <x-button.danger x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('delete account') }}</x-button.danger>
    </div>
  </x-section.one>

  @include('user.modal.delete')

  @if ($user->subscribed())
    @if ($sub->onGracePeriod())
      @include('user.modal.resume')
    @else
      @include('user.modal.cancel')
    @endif
  @endif
</x-layout.app>
