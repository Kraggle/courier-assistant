@php
  $list = ['Rooftop accurate address finder with navigation links.', 'Shared geolocational information displayed on map.', 'Accurately track and view your daily and weekly earnings.', 'All your data displayed in a readable format.', 'Store all your refuels and calculate the costs.', 'Perform accurate calculations for predicted income.', 'Store and calculate all of your expenses.', 'Get an overview of everything calculated for your taxes.'];

  $trial = K::user()->hadTrial();
@endphp

<x-layout.app title="subscribe"
  :center="true">
  <x-section.one class="p-0 md:p-0"
    maxWidth="xl">

    <div class="flex flex-col gap-4 text-center text-lg">

      <h1 class="text-tracking bg-violet-700 px-4 pb-3 pt-3 font-serif text-4xl font-black text-white md:px-6 md:pt-5">
        @if ($trial)
          7 Day FREE Trial
        @else
          Subscription
        @endif
      </h1>

      <div class="flex flex-col gap-4 px-4 pb-3 md:px-6 md:pb-5">
        <p class="text-gray-700">
          @if ($trial)
            You pay nothing for the first 7 days. Then only...
          @else
            You pay only...
          @endif
        </p>

        <div class="flex flex-col"><span class="text-6xl font-extrabold text-orange-600">£4.99</span><span class="-mt-1 text-lg font-normal text-gray-400">per month</span></div>

        <ul class="flex flex-col gap-2 text-left text-base">
          @foreach ($list as $item)
            <li>
              <x-icon class="far fa-check pr-2 text-base text-green-500"></x-icon>
              {{ $item }}
            </li>
          @endforeach
        </ul>

        <div class="flex">
          <x-button.dark class="grow justify-center"
            class="bg-orange-600 text-2xl hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700"
            open-modal="stripe-pay"
            size="md">
            try it free
          </x-button.dark>
        </div>
      </div>

    </div>

    @push('modals')
      <x-modal class="relative overflow-auto"
        name="stripe-pay"
        maxWidth="sm"
        help-root>

        <img class="absolute bottom-0 left-3 z-50 w-28 translate-y-1/2 rounded-md bg-white"
          src="{{ Vite::asset('resources/images/stripe-powered.svg') }}"
          alt="stripe">

        <form class="flex max-h-[calc(100vh_-_80px)] flex-col gap-4 overflow-y-auto overflow-x-hidden p-4 md:p-6"
          id="payment-form"
          method="POST">
          @csrf
          @method('put')

          <div id="payment-element"></div>

          <div class="align-center flex justify-end">
            <x-button.dark class="no-loader relative bg-violet-800 text-2xl hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
              id="payment-button">
              <span class="text opacity-100">subscribe</span>
              <div class="loader absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0"
                role="status">
                <svg xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 animate-spin fill-white text-violet-600"
                  aria-hidden="true"
                  viewBox="0 0 100 101"
                  fill="none">
                  <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor" />
                  <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill" />
                </svg>
                <span class="sr-only">Loading...</span>
              </div>
            </x-button.dark>
          </div>

          <div class="hidden text-sm text-red-600"
            id="payment-message"></div>

        </form>
      </x-modal>
    @endPush

  </x-section.one>
</x-layout.app>

@vite(['resources/js/stripe.js'])
