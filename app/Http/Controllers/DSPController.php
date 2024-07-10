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
            'in_hand' => ['required', 'integer'],
            'pay_day' => ['required', 'integer', 'in:0,1,2,3,4,5,6'],
        ]);

        DSP::create([
            'identifier' => Str::upper($request->identifier),
            'name' => Str::title($request->name),
            'in_hand' => $request->in_hand,
            'pay_day' => $request->pay_day,
        ]);

        return back()->with('success', Msg::added('DSP'));
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

        return back()->with('success', Msg::added('DSP connection'));
    }

    public function edit(Request $request, DSP $dsp) {
        $request->user()->dsps()->updateExistingPivot($dsp->id, ['date' => K::date($request->date)]);

        return back()->with('success', Msg::edited('DSP connection'));
    }

    /**
     * Destroy a DSP connection.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detach(Request $request, DSP $dsp) {
        $request->user()->dsps()->detach($dsp->id);

        return back()->with('success', Msg::deleted('DSP connection'));
    }
}
