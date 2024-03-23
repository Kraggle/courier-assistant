<?php

namespace App\Http\Controllers;

use App\Helpers\Msg;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller {

    /**
     * Show the form for creating a new vehicle.
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show() {
        return view('vehicle.create');
    }

    /**
     * Store a newly created vehicle in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return redirect
     */
    public function create(Request $request) {
        $user = $request->user();
        $reg = Str::upper($request->reg);

        $request->validate([
            'reg' => ['required', Rule::unique('vehicles')->where(fn ($query) => $query->where('user_id', $user->id))],
        ]);

        $vehicle = $user->vehicles()->create([
            'reg' => $reg,
        ]);

        $user->refresh();
        if ($user->vehicles()->count() == 1)
            return redirect()->route('dashboard')->with('success', __("You are all setup now, why don't you try adding your first route?"));

        return redirect()->route('refuels', $vehicle->id)->with('success', Msg::added(__('vehicle')));
    }
}
