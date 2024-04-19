@define($gap = 'gap-4')

<x-layout.app :center="true"
  :title="__('email verification')">
  <x-section.one class="self-center"
    maxWidth="md">

    <div class="{{ $gap }} flex flex-col">

      <div class="text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
      </div>

      @if (session('status') == 'verification-link-sent')
        <div class="text-sm font-medium text-green-600">
          {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
      @endif

      <div class="flex items-center justify-between">
        <form method="POST"
          action="{{ route('verification.send') }}">
          @csrf

          <div>
            <x-button.dark>
              {{ __('Resend Verification Email') }}
            </x-button.dark>
          </div>
        </form>

        <form method="POST"
          action="{{ route('logout') }}">
          @csrf

          <button class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            type="submit">
            {{ __('Log Out') }}
          </button>
        </form>
      </div>
    </div>

  </x-section.one>
</x-layout.app>
