<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\SimpleExcel\SimpleExcelReader;

class RateController extends FilesController {
    /**
     * Display the rates associated with the users current DSP.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show() {
        if (!Gate::allows('is-ready'))
            return redirect('dsp.show')->with('error', __('You first have to add or select your Delivery Service Provider.'));

        return view('rate.show');
    }

    /**
     * Add a new rate to the users current DSP.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {
        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'amount' => ['required', 'decimal:0,4'],
            'type' => ['required', 'string'],
            'depot_id' => ['required', 'exists:depots,id']
        ]);

        $request->user()->dsp()->rates()->create($request->all());

        return back()->with('success', Msg::added(__('rate')));
    }

    /**
     * Edit an existing rate.
     * 
     * @param  Request  $request
     * @param  \App\Expense  $rate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Rate $rate) {
        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'amount' => ['required', 'decimal:0,4'],
            'type' => ['required', 'string'],
            'depot_id' => ['required', 'exists:depots,id']
        ]);

        $rate->update($request->all());

        return back()->with('success', Msg::edited(__('rate')));
    }


    /**
     * Bulk add new rates.
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk(Request $request) {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return back()->with('error', Msg::invalidFile());
        }

        $user = K::user();
        $depot_id = $request->input('depot_id');

        $required = ['amount', 'date', 'type'];
        $headers = SimpleExcelReader::create($request->file('file'), 'csv')->getHeaders();
        foreach ($required as $r) {
            if (!in_array($r, $headers))
                return back()->with('error', Msg::bulkHeaders($required));
        }

        SimpleExcelReader::create($request->file('file'), 'csv')->getRows()
            ->each(function (array $row) use ($user, $depot_id) {
                $row = K::castArray($row, [
                    'date' => 'date:Y-m-d',
                    'depot_id' => ["default:$depot_id", 'int'],
                    'amount' => 'float',
                    'type' => 'string'
                ]);

                if (!$row['depot_id'])
                    $row['depot_id'] = $depot_id;

                if ($user->dsp()->hasRate($row['date'], $row['type'], $row['depot_id']))
                    return;

                $user->dsp()->rates()->create($row);
            });

        return back()->with('success', Msg::added(__('rates')));
    }

    /**
     * Delete a rate from the users current DSP.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rate $rate) {
        $rate->delete();

        return back()->with('success', Msg::deleted(__('rate')));
    }

    /**
     * Export all of the user's rates.
     * 
     * @param \Illuminate\Http\Request
     * @return void
     */
    public function exportAll(Request $request) {
        $this->export($request->user()->dsp()->rates, ['depot_id', 'date', 'type', 'amount'], 'rates');
    }
}
