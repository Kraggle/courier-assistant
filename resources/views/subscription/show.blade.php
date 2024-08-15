@php
  $list = ['Rooftop accurate address finder with navigation links.', 'Shared geolocational information displayed on map.', 'Accurately track and view your daily and weekly earnings.', 'All your data displayed in a readable format.', 'Store all your refuels and calculate the costs.', 'Perform accurate calculations for predicted income.', 'Store and calculate all of your expenses.', 'Get an overview of everything calculated for your taxes.'];

  $trial = $user->hadTrial();
  $due = $sub && ($sub->pastDue() || $sub->hasIncompletePayment());
@endphp

<x-layout.app title="subscribe"
  :center="true">
  <input id="cs"
    type="hidden"
    value="{{ $cs }}">

  <x-section.one class="p-0 md:p-0"
    maxWidth="xl">

    <div class="flex flex-col gap-4 text-center text-lg">

      <h1 class="text-tracking bg-violet-700 px-4 pb-3 pt-3 font-serif text-4xl font-black text-white md:px-6 md:pt-5">
        @if ($due || $trial)
          Subscription
        @else
          7 Day FREE Trial
        @endif
      </h1>

      <div class="flex flex-col gap-4 px-4 pb-3 md:px-6 md:pb-5">
        <p class="text-gray-700">
          @if ($trial || $due)
            You pay only...
          @else
            You pay nothing for the first 7 days. Then only...
          @endif
        </p>

        <div class="flex flex-col">
          <span class="text-6xl font-extrabold text-orange-600">£4.99</span>
          <span class="-mt-1 text-lg font-normal text-gray-400">per month</span>
        </div>

        <ul class="flex flex-col gap-2 text-left text-base">
          @foreach ($list as $item)
            <li>
              <x-icon class="far fa-check pr-2 text-base text-green-500"></x-icon>
              {{ $item }}
            </li>
          @endforeach
        </ul>

        @if ($due)
          <x-button.dark class="w-full"
            class="bg-orange-600 text-xl hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700"
            size="md"
            :href="route('billing')">
            Please confirm your payment
          </x-button.dark>
        @else
          <x-button.dark class="w-full"
            class="bg-orange-600 text-2xl hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700"
            size="md"
            open-modal="stripe-pay">
            @if ($trial)
              pay now
            @else
              try it free
            @endif
          </x-button.dark>
        @endif
      </div>

    </div>

    @push('modals')
      <x-modal class=""
        name="stripe-pay"
        maxWidth="md"
        help-root>

        <form class="flex flex-col gap-4 overflow-y-auto overflow-x-hidden p-4 md:p-6"
          id="payment-form"
          method="POST">
          @csrf
          @method('put')

          <div id="payment-element"></div>

          <div class="flex items-end justify-between">

            <img class="w-28"
              src="{{ Vite::asset('resources/images/stripe-powered.svg') }}"
              alt="stripe">

            <x-button.loader class="ignore relative bg-violet-800 text-xl hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900"
              id="payment-button">
              <x-slot:text>subscribe</x-slot>
              <x-slot:loader></x-slot>
            </x-button.loader>
          </div>

          <div class="hidden text-sm text-red-600"
            id="payment-message"></div>

        </form>
      </x-modal>
    @endPush

  </x-section.one>
</x-layout.app>

@vite(['resources/js/stripe.js'])
