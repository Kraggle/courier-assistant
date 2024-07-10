<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Info;
use Illuminate\Http\Request;
use App\Http\Requests\InfoRequest;
use Illuminate\Support\Facades\Gate;

class InfoController extends Controller {
    function show() {
        return view('map.show');
    }

    /**
     * Add a new info block.
     * 
     * @param InfoRequest $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    function add(InfoRequest $request) {

        Info::create($request->all());

        return back()->with('success', Msg::added('marker'));
    }

    /**
     * Update an info block.
     * 
     * @param InfoRequest $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(InfoRequest $request) {

        $info = Info::find($request->id);
        $info->update($request->all());

        return back()->with('success', Msg::edited('marker'));
    }

    /**
     * Update an info blocks location.
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JSONResponse
     */
    function location(Request $request) {

        $request->validate([
            'id' => ['required', 'exists:info,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $info = Info::find($request->id);
        $info->update([
            'position->lat' => $request->lat,
            'position->lng' => $request->lng,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Delete an info block.
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy(Request $request) {
        $request->validate([
            'id' => ['required', 'exists:info,id']
        ]);

        $info = Info::find($request->id);

        if (!Gate::allows('delete-info', $info))
            return back()->with('error', 'You do not have permission to delete this marker.');

        $info->delete();

        return back()->with('success', Msg::deleted('marker'));
    }

    /**
     * Get all info blocks.
     * 
     * @return \Illuminate\Http\JSONResponse
     */
    function info() {
        return response()->json(Info::all());
    }

    /**
     * Get the Google Maps info.
     * 
     * @return \Illuminate\Http\JSONResponse
     */
    function keys() {
        return response()->json([
            'api' => env('GOOGLE_MAPS_API_KEY'),
            'id' => env('GOOGLE_MAPS_MAP_ID'),
        ]);
    }
}
