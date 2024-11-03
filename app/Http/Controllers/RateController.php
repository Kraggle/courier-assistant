<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Rate;
use App\Models\User;
use App\Helpers\Lists;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Spatie\SimpleExcel\SimpleExcelReader;

class RateController extends FilesController {
    /**
     * Display the rates associated with the users current DSP.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show() {
        if (!Gate::allows('is-ready'))
            return redirect('dsp.show')->with('error', 'You first have to add or select your Delivery Service Provider.');

        return view('rate.show');
    }

    /**
     * Get the users refuels from the database with filters.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request) {
        $user = K::user();

        extract(K::merge([
            'page' => 1,
            'length' => 10,
        ], $request->all()));

        $refuels = $user->dsp()->ratesWithFilters($request->all());
        $paged = $refuels->forPage($page, $length);
        $items = collect();
        $paged->each(function ($r) use ($items) {
            $logs = [];
            if ($r->hasChangeLogs()) {
                foreach ($r->changeLogs() as $log)
                    $logs[] = [
                        'date' => K::displayDate($log->created_at, 'd-m-Y'),
                        'properties' => $log->properties,
                        'user' => $log->causer->name,
                    ];
            }

            $items->push(collect([
                'id' => $r->id,
                'date' => K::displayDate($r->date, 'jS M Y'),
                'type' => $r->getType(true, 'hidden sm:inline'),
                'amount' => K::formatCurrency($r->amount, $r->amount < 0.9999),
                'depot_identifier' => $r->depot_identifier,
                'creator' => $r->creator,
                'has_changes' => $r->hasChangeLogs(),
                'modal' => [
                    'edit' => [
                        'title.text' => Msg::edit('rate'),
                        'form.action' => route('rate.edit', $r->id),
                        'date.value' => old('date', $r->date),
                        'type.value' => old('type', $r->type),
                        'depot_id.value' => old('depot_id', $r->depot_id),
                        'amount.value' => old('amount', $r->amount),
                        'destroy.removeclass' => 'hidden',
                        'destroy.data' => [
                            'modal' => [
                                'form.action' => route('rate.destroy', $r->id),
                            ],
                        ],
                        'submit.text' => 'save',
                    ],
                    'changes' => [
                        'title.text' => 'changes',
                        'tbody.changes' => $logs,
                    ]
                ]
            ]));
        });

        return response()->json([
            'items' => $items,
            'filtered' => $refuels->count(),
            'total' => $user->refuels->count(),
        ]);
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

        return back()->with('success', Msg::added('rate'));
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

        return back()->with('success', Msg::edited('rate'));
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

        return back()->with('success', Msg::added('rates'));
    }

    /**
     * Delete a rate from the users current DSP.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rate $rate) {
        $rate->delete();

        return back()->with('success', Msg::deleted('rate'));
    }

    /**
     * Export all of the user's rates.
     * 
     * @param \Illuminate\Http\Request
     * @return void
     */
    public function export(Request $request) {
        $this->doExport($request->user()->dsp()->rates, ['depot_id', 'date', 'type', 'amount'], 'rates');
    }
}
