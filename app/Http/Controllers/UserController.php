<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Models\DSP;
use App\Helpers\Msg;
use App\Models\Depot;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class UserController extends Controller {
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View {
        return view('user.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     * 
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('user.edit')->with('status', Msg::added('profile'));
    }

    /**
     * Update the users options.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function options(Request $request): RedirectResponse {
        $request->validate([
            'depot_id' => ['exists:depots,id'],
        ]);

        $options = [];

        if ($request->has('depot_id')) {
            $options['options->depot_id'] = $request->depot_id;
        }

        $request->user()->update($options);

        return back()->with('success', Msg::edited('options'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
