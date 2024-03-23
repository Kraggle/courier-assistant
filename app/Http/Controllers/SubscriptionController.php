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

        return view('subscription.show', [
            'intent' => $user->createSetupIntent(),
            'key' => config('cashier.key')
        ]);
    }

    /**
     * The subscription checkout.
     * 
     * @param \Illuminate\Http\Request $request
     * @return 
     */
    public function success(Request $request) {
        $user = K::user();

        $coupon = $request->coupon ? $user->findActivePromotionCode($request->coupon) : [];

        $user->createOrGetStripeCustomer();
        $user->updateStripeCustomer([
            'address' => [
                'country' => 'GB',
                'postal_code' => $request->postal_code
            ]
        ]);
        $user->newSubscription('default', env('STRIPE_PRICE'))
            ->trialDays(7)
            ->withPromotionCode($coupon->id ?? null)
            ->create($request->get('token'));
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
        return back()->with('info', __('Your subscription has been cancelled successfully.'));
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
        return back()->with('info', __('Your subscription has been resumed successfully.'));
    }
}
