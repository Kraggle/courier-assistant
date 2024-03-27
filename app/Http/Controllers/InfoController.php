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

        return back()->with('success', Msg::added(__('marker')));
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

        return back()->with('success', Msg::edited(__('marker')));
    }

    /**
     * Delete an info block.
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy(Request $request) {
        $request->validate([
            'id' => ['required', 'exists:info,id']
        ]);

        $info = Info::find($request->id);

        if (!Gate::allows('delete-info', $info))
            return back()->with('error', __('You do not have permission to delete this marker.'));

        $info->delete();

        return back()->with('success', Msg::deleted(__('marker')));
    }

    /**
     * Get all info blocks.
     * 
     * @return json
     */
    function info() {
        return response()->json(Info::all());
    }

    /**
     * Get the Google Maps info.
     * 
     * @return json
     */
    function keys() {
        return response()->json([
            'api' => env('GOOGLE_MAPS_API_KEY'),
            'id' => env('GOOGLE_MAPS_MAP_ID'),
        ]);
    }
}
