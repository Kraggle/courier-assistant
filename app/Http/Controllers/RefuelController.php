<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Refuel;
use App\Models\Vehicle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Spatie\SimpleExcel\SimpleExcelReader;

class RefuelController extends FilesController {

    /**
     * Show the vehicle's refuels.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request) {
        return view('refuel.show', ['user' => $request->user()]);
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

        $refuels = $user->refuelsWithFilters($request->all());
        $paged = $refuels->forPage($page, $length);
        $items = collect();
        $paged->each(function ($r) use ($items) {
            $items->push(collect([
                'id' => $r->id,
                'date' => K::displayDate($r->date, 'D, jS M \'y'),
                'cost' => K::formatCurrency($r->cost),
                'mileage' => number_format($r->mileage),
                'vehicle' => $r->vehicle->reg,
                'miles' => number_format($r->miles),
                'fuel_rate' => K::formatCurrency($r->fuel_rate, true),
                'has_image' => $r->hasImage(),
                'modal' => [
                    'edit' => [
                        'title.text' => 'Edit refuel',
                        'form.action' => route('refuel.edit', $r->id),
                        'vehicle.value' => old('vehicle', $r->vehicle->id),
                        'date.value' => old('date', $r->date->format('Y-m-d')),
                        'mileage.value' => old('mileage', $r->mileage),
                        'cost.value' => old('cost', $r->cost),
                        'first.checked' => old('first', K::isTrue($r->first)),
                        'image-wrap.set-inputs' => old('image-wrap', ''),
                        'image-wrap.set-img' => $r->getImageURL() ?? Vite::asset('resources/images/no-image.svg'),
                        'destroy.removeclass' => 'hidden',
                        'destroy.data' => [
                            'modal' => [
                                'form.action' => route('refuel.destroy', $r->id),
                            ],
                        ],
                        'submit.text' => 'save',
                    ],
                    'receipt' => [
                        'image.src' => $r->getImageURL(),
                        'form.action' => route('refuel.download'),
                        'path.value' => $r->image,
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
     * Add a new refuel to the vehicle.
     * 
     * @param Request $request
     * @param Vehicle $vehicle
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request) {

        $request->validate([
            'vehicle' => ['required', 'exists:vehicles,id'],
            'date' => ['required', 'date:Y-m-d'],
            'cost' => ['nullable', 'decimal:0,2'],
            'mileage' => ['required', 'int'],
            'first' => ['int'],
            'image' => 'mimes:jpeg,jpg,png,pdf'
        ]);

        $vehicle = K::user()->vehicles()->find($request->vehicle);
        $last = $vehicle->refuels->first();

        $user = $request->user();
        $link = $request->hasFile('image') ? $this->uploadFile($request->file('image'), "images/{$user->id}/refuel") : null;

        $first = K::isTrue($request->first);

        // set first to true if there is no last refuel or mileage is too great
        if ((!$first && !$last) || ($last && $request->mileage - $last->mileage > 600))
            $first = true;

        $vehicle->refuels()->create(K::merge($request->all(), [
            'first' => $first,
            'image' => $link,
            'cost' => $request->cost ?? 0
        ]));

        $this->calculateRefuels($vehicle);

        return back()->with('success', Msg::added('refuel'));
    }

    /**
     * Edit an existing refuel.
     * 
     * @param Request $request
     * @param Refuel $refuel
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Refuel $refuel) {
        $last = $refuel->vehicle->previousRefuel($request->date);
        $last_mileage = $last->mileage ?? 0;

        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'cost' => ['nullable', 'decimal:0,2'],
            'mileage' => ['required', 'int', "gt:$last_mileage"],
            'first' => ['nullable', 'int'],
            'image' => 'mimes:jpeg,jpg,png,pdf'
        ]);

        if ($request->hasFile('image')) {
            if ($refuel->hasImage())
                $this->deleteFile($refuel->image);

            $link = $this->uploadFile($request->file('image'), "images/{$request->user()->id}/refuel");
            $refuel->image = $link;
            $refuel->save();
        }

        $first = K::isTrue($request->first);

        $refuel->update(K::merge($request->except('image'), [
            'cost' => $request->cost ?? 0,
            'first' => $first,
        ]));
        $refuel->save();

        $this->calculateRefuels($refuel->vehicle);

        return back()->with('success', Msg::edited('refuel'));
    }

    /**
     * Recalculate the miles and fuel rates for each of the vehicle's refuels.
     * 
     * @param Vehicle $vehicle
     */
    private function calculateRefuels(Vehicle $vehicle) {
        $vehicle->refresh();

        if (!$vehicle->hasRefuels()) return;

        $refuels = $vehicle->refuels->sortBy('date')->sortBy('mileage');
        $last = $refuels->first();

        foreach ($refuels as $refuel) {
            if ($refuel === $last || $refuel->cost == 0)
                $refuel->first = true;

            if ($refuel->first) {
                $refuel->update([
                    'fuel_rate' => 0.22,
                    'miles' => 0
                ]);
            } else {
                $miles = $refuel->mileage - $last->mileage;
                $refuel->update([
                    'fuel_rate' => $refuel->cost / $miles,
                    'miles' => $miles
                ]);
            }

            $refuel->save();
            $last = $refuel;
        }
    }

    /**
     * Bulk add new refuels to the vehicle.
     * 
     * @param Request $request
     * @param Vehicle $vehicle
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk(Request $request, Vehicle $vehicle) {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return back()->with('error', Msg::invalidFile());
        }

        $required = ['mileage', 'date', 'cost'];
        $headers = SimpleExcelReader::create($request->file('file'), 'csv')->getHeaders();
        foreach ($required as $r) {
            if (!in_array($r, $headers))
                return back()->with('error', Msg::bulkHeaders($required));
        }

        SimpleExcelReader::create($request->file('file'), 'csv')->getRows()
            ->each(function (array $row) use ($vehicle) {
                if ($vehicle->hasRefuelForMileage(K::cast($row['mileage'], 'int')))
                    return;

                $row = K::castArray($row, [
                    'date' => 'date:Y-m-d',
                    'mileage' => 'int',
                    'cost' => ['default:0', 'float'],
                    'first' => ['default:0', 'bool']
                ]);

                $row['first'] = $row['cost'] == 0;

                $vehicle->refuels()->create($row);
            });

        $this->calculateRefuels($vehicle);

        return back()->with('success', Msg::added('refuels'));
    }

    /**
     * Delete an existing refuel.
     * 
     * @param Request $request
     * @param Refuel $refuel
     */
    public function destroy(Request $request, Refuel $refuel) {
        if ($refuel->hasImage())
            $this->deleteFile($refuel->image);
        $refuel->delete();

        $this->calculateRefuels($refuel->vehicle);

        return back()->with('success', Msg::deleted('refuel'));
    }

    /**
     * Export all of the user's refuels.
     * 
     * @param \Illuminate\Http\Request
     * @param \App\Models\Vehicle $vehicle
     * @return void
     */
    public function export(Request $request, Vehicle $vehicle) {
        $this->doExport($vehicle->refuels, ['date', 'mileage', 'cost', 'first', 'image'], "refuels-{$vehicle->reg}");
    }
}
