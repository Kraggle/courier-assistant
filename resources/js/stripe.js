import { loadStripe as Stripe } from '@stripe/stripe-js';

(async () => {
	const data = await fetch('/subscription', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			_token: $('[name="_token"]').first().val()
		})
	}).then(r => r.json());

	const stripe = await Stripe(data.pk);
	if (!stripe) return;

	const appearance = {
		theme: 'stripe',
		labels: 'floating',
		variables: {
			fontFamily: 'Advent Pro, sans-serif',
			primaryColor: '#5b21b6',
			colorDanger: '#dc2626'
		},
		rules: {
			'.Label--resting': {
				fontSize: '0.875rem',
				lineHeight: '1rem',
				fontWeight: '500',
			},
			'.Label--floating': {
				fontSize: '0.675rem',
				lineHeight: '1rem',
				fontWeight: '500',
			},
			'.Input': {
				padding: '0.5rem 0.75rem',
			}
		}
	},
		elements = stripe.elements({ clientSecret: data.cs, appearance }),
		paymentElement = elements.create('payment', { layout: 'tabs' });
	paymentElement.mount('#payment-element');

	$('#payment-form').on('submit', async function(e) {
		e.preventDefault();
		setLoading(true);

		const { error } = await stripe.confirmSetup({
			elements,
			confirmParams: {
				return_url: data.url
			}
		});

		if (error.type === 'card_error' || error.type === 'validation_error')
			showMessage(error.message);
		else
			showMessage(data.str.error);

		setLoading(false);
	});

	const clientSecret = new URLSearchParams(window.location.search).get(
		'payment_intent_client_secret'
	);
	if (!clientSecret) return;

	const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
	if (!paymentIntent) return;

	switch (paymentIntent.status) {
		case "succeeded":
			showMessage(data.str.success);
			break;
		case 'processing':
			showMessage(data.str.process);
			break;
		case 'requires_payment_method':
			showMessage(data.str.failed);
			break;
		default:
			showMessage(data.str.wrong);
			break;
	}
})();

function showMessage(message) {
	$('#payment-message').text(message).removeClass('hidden');

	setTimeout(() => {
		$('#payment-message').addClass('hidden').text('');
	}, 4000);
}

function setLoading(isLoading) {
	const btn = $('#payment-button');
	btn.prop('disabled', isLoading);
	btn.find('.text').css('opacity', isLoading ? 0 : 1);
	btn.find('.loader').css('opacity', isLoading ? 1 : 0);
}