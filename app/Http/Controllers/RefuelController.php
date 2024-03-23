<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Refuel;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Spatie\SimpleExcel\SimpleExcelReader;

class RefuelController extends FilesController {

    /**
     * Show the vehicle's refuels.
     * 
     * @param Request $request
     * @param Vehicle $vehicle
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Vehicle $vehicle) {
        if (!Gate::allows('view-vehicle', $vehicle))
            return redirect('/')->with('error', __('You do not have permission to view this vehicle.'));

        return view('refuel.show', ['user' => $request->user(), 'vehicle' => $vehicle]);
    }

    /**
     * Add a new refuel to the vehicle.
     * 
     * @param Request $request
     * @param Vehicle $vehicle
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Vehicle $vehicle) {
        $last = $vehicle->refuels->first();
        $last_mileage = $last->mileage ?? 0;

        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'cost' => ['nullable', 'decimal:0,2'],
            'mileage' => ['required', 'int', "gt:$last_mileage"],
            'first' => ['nullable', 'int'],
            'image' => 'mimes:jpeg,jpg,png,pdf'
        ]);

        $user = $request->user();
        $link = $request->hasFile('image') ? $this->uploadFile($request->file('image'), "images/{$user->id}/refuel") : null;

        $first = K::isTrue($request->first);

        // set first to true if there is no last refuel
        if ((!$first && !$last) || ($last && $request->mileage - $last->mileage > 600))
            $first = true;

        $vehicle->refuels()->create(K::merge($request->all(), [
            'first' => $first,
            'image' => $link,
            'cost' => $request->cost ?? 0
        ]));

        $this->calculateRefuels($vehicle);

        return back()->with('success', Msg::added(__('refuel')));
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

        return back()->with('success', Msg::edited(__('refuel')));
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
        $last = $refuels[0];

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

        return back()->with('success', Msg::added(__('refuels')));
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

        return back()->with('success', Msg::deleted(__('refuel')));
    }

    /**
     * Export all of the user's refuels.
     * 
     * @param \Illuminate\Http\Request
     * @param \App\Models\Vehicle $vehicle
     * @return void
     */
    public function exportAll(Request $request, Vehicle $vehicle) {
        $this->export($vehicle->refuels, ['date', 'mileage', 'cost', 'first', 'image'], "refuels-{$vehicle->reg}");
    }
}
