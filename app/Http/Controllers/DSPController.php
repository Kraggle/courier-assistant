<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Models\DSP;
use App\Helpers\Msg;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DSPController extends Controller {

    /**
     * Select or add a new DSP.
     * 
     * @return \Illuminate\View\View
     */
    public function show() {
        return view('dsp.show');
    }

    /**
     * Create a new DSP.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request) {
        $request->validate([
            'identifier' => ['required', 'max:5', 'unique:dsps'],
            'name' => ['required', 'max:60'],
        ]);

        DSP::firstOrCreate([
            'identifier' => Str::upper($request->identifier),
            'name' => Str::title($request->name),
        ]);

        return back()->with('success', Msg::added(__('DSP')));
    }

    /**
     * Connect a user to a DSP.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attach(Request $request) {
        $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'dsp_id' => ['required', 'exists:dsps,id']
        ]);

        $user = $request->user();
        $user->dsps()->attach([
            $request->dsp_id => ['date' => K::date($request->date)]
        ]);

        return back()->with('success', Msg::added(__('DSP connection')));
    }

    public function edit(Request $request, DSP $dsp) {
        $request->user()->dsps()->updateExistingPivot($dsp->id, ['date' => K::date($request->date)]);

        return back()->with('success', Msg::edited(__('DSP connection')));
    }

    /**
     * Destroy a DSP connection.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detach(Request $request, DSP $dsp) {
        $request->user()->dsps()->detach($dsp->id);

        return back()->with('success', Msg::deleted(__('DSP connection')));
    }
}
