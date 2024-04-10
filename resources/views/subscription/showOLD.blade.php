@php
  $list = [__('Rooftop accurate address finder with navigation links.'), __('Shared geolocational information displayed on map.'), __('Accurately track and view your daily and weekly earnings.'), __('All your data displayed in a readable format.'), __('Store all your refuels and calculate the costs.'), __('Perform accurate calculations for predicted income.'), __('Store and calculate all of your expenses.'), __('Get an overview of everything calculated for your taxes.')];
@endphp

<x-layout.app :center="true">
  <x-section.wrap maxWidth="xl"
    px=""
    py="">

    <div class="flex flex-col gap-4 text-center text-lg">

      <h1 class="text-tracking bg-violet-700 px-4 pb-3 pt-3 font-serif text-4xl font-black text-white md:px-6 md:pt-5">
        {{ __('7 Day FREE Trial') }}
      </h1>

      <div class="flex flex-col gap-4 px-4 pb-3 md:px-6 md:pb-5">
        <p class="text-gray-700">{{ __('You pay nothing for the first 7 days. Then only...') }}</p>

        <div class="flex flex-col"><span class="text-6xl font-extrabold text-orange-600">£4.99</span><span class="-mt-1 text-lg font-normal text-gray-400">{{ __('per month') }}</span></div>

        <ul class="flex flex-col gap-2 text-left text-base">
          @foreach ($list as $item)
            <li>
              <x-icon class="far fa-check pr-2 text-base text-green-500"></x-icon>
              {{ $item }}
            </li>
          @endforeach
        </ul>

        <div class="flex">
          <x-button.dark x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'stripe-pay')"
            class="grow justify-center"
            color="bg-orange-600 text-2xl hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700"
            size="md">
            {{ __('try it free') }}
          </x-button.dark>
        </div>
      </div>

    </div>

    {{-- <div x-data="" --}}
    {{-- x-init="setTimeout(() => { $dispatch('open-modal', 'stripe-pay'), 1000 })"></div> --}}

    @push('modals')
      <x-modal class="p-4 md:p-6"
        name="stripe-pay"
        maxWidth="sm"
        help-root
        overflow="">

        <img class="absolute bottom-0 left-3 w-28 translate-y-1/2 rounded-md bg-white"
          src="{{ Vite::asset('resources/images/stripe-powered.svg') }}"
          alt="stripe">

        <form class="relative flex flex-col gap-4"
          id="payment-form"
          data-stripe="{{ $key }}"
          data-secret="{{ $intent->client_secret }}"
          method="POST"
          action="{{ route('subscription.success') }}">
          @csrf
          @method('PUT')

          {{-- name --}}
          @define($key = 'billing-name')
          <x-form.wrap :key="$key"
            :value="__('Name on Card')">

            <x-form.text class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              :value="old($key)"
              autofocus
              autocomplete="username" />

          </x-form.wrap>

          @define($class = 'border border-gray-300 focus-within:border-indigo-500 focus-within:ring-indigo-500 rounded-md shadow-sm px-3 py-2')

          {{-- card number --}}
          @define($key = '')
          <x-form.wrap :key="$key"
            :value="__('Card Number')">

            <div class="{{ $class }}"
              id="number-element"></div>

          </x-form.wrap>

          <div class="grid grid-cols-2 gap-4">
            {{-- expiry --}}
            @define($key = '')
            <x-form.wrap :key="$key"
              :value="__('Expiry')">

              <div class="{{ $class }}"
                id="expiry-element"></div>

            </x-form.wrap>

            {{-- cvc --}}
            @define($key = '')
            <x-form.wrap :key="$key"
              :value="__('CVC')">

              <div class="{{ $class }}"
                id="cvc-element"></div>

            </x-form.wrap>
          </div>

          {{-- post code --}}
          @define($key = 'post_code')
          <x-form.wrap :key="$key"
            :value="__('Post Code')">

            <x-form.text class="block w-full uppercase"
              id="{{ $key }}"
              name="{{ $key }}"
              :value="old($key)" />

          </x-form.wrap>

          {{-- coupon --}}
          @define($key = 'coupon')
          <x-form.wrap class="hidden"
            :key="$key"
            :value="__('coupon')">

            <x-form.text-prefix class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              :value="old($key)">

              <x-icon class="fas fa-check hidden text-green-400"
                id="couponCorrect" />
              <x-icon class="fas fa-times text-red-400"
                id="couponIncorrect" />

            </x-form.text-prefix>

          </x-form.wrap>

          <div class="text-sm text-gray-400">
            <p>{{ __('You will not be charged until the trial period has ended!') }}</p>
            <p>{{ __('You can cancel during the trial period to avoid payment.') }}</p>
          </div>

          <div class="align-center flex justify-end">
            <x-button.dark color="bg-violet-800 text-2xl hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900">
              {{ __('subscribe') }}
            </x-button.dark>
          </div>

        </form>
      </x-modal>
    @endPush

  </x-section.wrap>
</x-layout.app>

<script src="https://js.stripe.com/v3/"></script>

<script type="module">
  $('#coupon').on('change', function() {
    $.getJSON("{{ route('coupon') }}", {
      coupon: $(this).val()
    }, data => {
      const c = data.active;
      $('#couponIncorrect')[c ? 'addClass' : 'removeClass']('hidden');
      $('#couponCorrect')[c ? 'removeClass' : 'addClass']('hidden');
    })
  });

  const stripe = Stripe($('#payment-form').data('stripe'))

  const numberId = '#number-element',
    expiryId = '#expiry-element',
    cvcId = '#cvc-element',
    formId = '#payment-form',
    elements = stripe.elements({
      fonts: [{
        cssSrc: 'https://fonts.googleapis.com/css2?family=Advent+Pro'
      }]
    });

  const elementStyles = {
    base: {
      fontFamily: 'Advent Pro, sans-serif',
      fontSize: '16px',
      color: 'rgb(17, 24, 39)',
      lineHeight: '1.428571',

      '::placeholder': {
        color: 'rgba(0,0,0,0.4)'
      }
    }
  }

  const elementClasses = {
    focus: 'focus',
    empty: 'empty',
    invalid: 'invalid'
  }

  const cardNumber = elements.create('cardNumber', {
    style: elementStyles,
    classes: elementClasses
  });
  cardNumber.mount(numberId);

  const cardExpiry = elements.create('cardExpiry', {
    style: elementStyles,
    classes: elementClasses
  });
  cardExpiry.mount(expiryId);

  const cardCvc = elements.create('cardCvc', {
    style: elementStyles,
    classes: elementClasses
  });
  cardCvc.mount(cvcId);

  registerElements([cardNumber, cardExpiry, cardCvc], $(formId));

  /**
   * Register the stripe payment method and elements.
   * 
   * @param {object[]} elements The stripe elements.
   * @param {jQueryElement} form The entire form.
   */
  function registerElements(elements, form) {

    const secret = form.data('secret');

    // Listen on the form's 'submit' handler...
    form.on('submit', async function(e) {
      if ($('input[name=token]', this).length) return;
      e.preventDefault();

      // Gather additional customer data we may have collected in our form.
      const name = $('input[name=billing-name]').val();

      const {
        setupIntent,
        error
      } = await stripe.confirmCardSetup(
        secret, {
          payment_method: {
            card: elements[0],
            billing_details: {
              name
            }
          }
        }
      );

      if (error) {
        if (error.type == 'validation_error') {
          // TODO: handle card vaildation errors
        }
        console.log(error);
        return;
      }

      $('<input>', {
        type: 'hidden',
        name: 'token',
        value: setupIntent.payment_method
      }).prependTo(form);

      console.log(setupIntent);

      form.trigger('submit');
    });
  }
</script>
