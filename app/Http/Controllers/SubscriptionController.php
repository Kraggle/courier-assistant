<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SubscriptionController extends Controller {
    /**
     * Show the subscription price page. 
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show() {
        $user = K::user();
        if ($user->subscribed())
            return redirect()->route('dashboard');

        return view('subscription.show');
    }

    /**
     * The subscription checkout.
     * 
     * @param \Illuminate\Http\Request $request
     * @return 
     */
    public function success(Request $request) {
        $user = K::user();

        $user->createOrGetStripeCustomer();
        $user->updateStripeCustomer([
            'address' => [
                'country' => 'GB',
                'postal_code' => $request->postal_code
            ]
        ]);

        $sub = $user->newSubscription('default', env('STRIPE_PRICE'));

        if ($request->coupon) {
            $coupon =  $user->findActivePromotionCode($request->coupon);
            $sub->withPromotionCode($coupon->id);
        }

        if (!$user->hadTrial()) {
            $sub->trialDays(7);
            $user->update([
                'options->had_trial' => 1
            ]);
        }

        try {
            $sub->create($request->get('token'));
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('dashboard')]
            );
        }

        return redirect('/')->with('success', 'Your subscription has been created successfully.');
    }

    /**
     * Check to see if a coupon is valid.
     * 
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function coupon(Request $request) {
        $coupon = K::user()->findActivePromotionCode($request->coupon);
        return json_encode(['active' => !!$coupon]);
    }

    /**
     * Get the keys.
     * 
     * @return \Illuminate\Http\JSONResponse
     */
    public function keys() {
        return response()->json([
            'pk' => config('cashier.key'),
            'url' => route('subscription.success'),
            'str' => [
                'error' => 'An unexpected error has occurred.',
                'success' => 'Payment succeeded!',
                'process' => 'Your payment is processing.',
                'failed' => 'Your payment was not successful, please try again.',
                'wrong' => 'Something went wrong.',
            ]
        ]);
    }

    /**
     * Cancel the users subscription.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request) {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->subscription('default')->cancel();
        return back()->with('info', 'Your subscription has been cancelled successfully.');
    }

    /**
     * Resume the users subscription.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resume(Request $request) {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->subscription('default')->resume();
        return back()->with('info', 'Your subscription has been resumed successfully.');
    }
}
