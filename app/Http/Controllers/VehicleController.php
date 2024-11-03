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
            'reg' => ['required', Rule::unique('vehicles')->where(fn($query) => $query->where('user_id', $user->id))],
        ]);

        $user->vehicles()->create([
            'reg' => $reg,
        ]);

        return back()->with('info', Msg::added('vehicle'));
    }
}
