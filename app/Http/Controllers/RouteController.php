<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Depot;
use App\Models\Route;
use Illuminate\Http\Request;
use App\Http\Requests\RouteRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelReader;

class RouteController extends FilesController {
    /**
     * Display all of the users routes.
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show() {
        return view('route.show', ['user' => Auth::user()]);
    }

    /**
     * Get more weeks of the users routes via ajax.
     * 
     * @return string
     */
    public function get(Request $request) {
        $user = K::user();

        $date = K::date($request->date);
        $weeks = [
            $date->copy()->sub('week', 1),
            $date->copy()->sub('week', 2),
            $date->copy()->sub('week', 3),
            $date->copy()->sub('week', 4),
        ];

        $render = '';
        foreach ($weeks as $date) {
            $render .= view('route.table', ['routes' => $user->routesByWeek($date)->sortByDesc('date')])->render();
        }

        return $render;
    }

    /**
     * Create a new route.
     * 
     * @param  \App\Http\Requests\RouteRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(RouteRequest $request) {
        $request->user()->routes()->create($request->all());

        return back()->with('success', Msg::added(__('route')));
    }

    /**
     * Edit an existing route.
     * 
     * @param  \App\Http\Requests\RouteRequest  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(RouteRequest $request, Route $route) {
        $route->update($request->all());

        return back()->with('success', Msg::edited(__('route')));
    }

    /**
     * Edit an existing route.
     * 
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function week(Request $request) {
        $user = $request->user();

        for ($i = 0; $i < count($request->id); $i++) {
            $route = $user->routes()->find($request->id[$i]);
            $route->update([
                'invoice_mileage' => $request->invoice_mileage[$i],
                'bonus' => $request->bonus[$i],
                'vat' => K::isTrue($request->vat[$i] ?? 0),
            ]);
        }

        return back()->with('success', __('Successfully updated the week!'));
    }

    /**
     * Bulk add new routes.
     * 
     * @param Request $request
     * @param Vehicle $vehicle
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk(Request $request) {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return back()->with('error', Msg::invalidFile());
        }

        $user = K::user();
        $depot_id = $request->input('depot_id');

        $required = ['date', 'start_time', 'end_time', 'start_mileage', 'end_mileage'];
        $headers = SimpleExcelReader::create($request->file('file'), 'csv')->getHeaders();
        foreach ($required as $r) {
            if (!in_array($r, $headers))
                return back()->with('error', Msg::bulkHeaders($required));
        }

        SimpleExcelReader::create($request->file('file'), 'csv')->getRows()
            ->each(function (array $row) use ($user, $depot_id) {
                $row['date'] = K::cast($row['date'], 'date:Y-m-d');

                if (!isset($row['depot_id']))
                    $row['depot_id'] = $depot_id;

                if ($user->hasRoute($row['date']))
                    return;

                $row = K::castArray($row, [
                    'date' => 'date:Y-m-d',
                    'start_time' => 'date:g:i a',
                    'end_time' => ['nullable', 'date:g:i a'],
                    'vat' => ['default:0', 'boolean'],
                    'start_mileage' => ['nullable', 'integer'],
                    'end_mileage' => ['nullable', 'integer'],
                    'invoice_mileage' => ['nullable', 'integer'],
                    'stops' => ['nullable', 'integer'],
                    'ttfs' => ['default:60', 'integer'],
                    'depot_id' => ["default:$depot_id", 'integer'],
                    'type' => ['default:md', 'string'],
                    'bonus' => ['nullable', 'float'],
                ]);

                $user->routes()->create($row);
            });

        return back()->with('success', Msg::added(__('routes')));
    }

    /**
     * Delete an existing route.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Route $route) {
        $route->delete();

        return back()->with('success', Msg::deleted(__('route')));
    }

    /**
     * Export all of the user's routes.
     * 
     * @param \Illuminate\Http\Request
     * @return void
     */
    public function exportAll(Request $request) {
        $this->export($request->user()->routes, ['depot_id', 'date', 'start_time', 'end_time', 'start_mileage', 'end_mileage', 'stops', 'invoice_mileage', 'bonus', 'type', 'vat', 'ttfs'], 'routes');
    }
}
