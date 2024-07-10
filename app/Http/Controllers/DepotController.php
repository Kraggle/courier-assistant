<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Depot;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DepotController extends Controller {
    /**
     * Create a new depot.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create() {
        return view('depot.create');
    }

    /**
     * Add a new depot.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $request->validate([
            'identifier' => ['required', 'max:5', 'unique:depots'],
            'location' => ['required', 'max:60'],
        ]);

        Depot::firstOrCreate([
            'location' => Str::title($request->location),
            'identifier' => Str::upper($request->identifier),
        ]);

        return back()->with('success', Msg::added('depot'));
    }
}
