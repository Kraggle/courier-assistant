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
        $first = K::isTrue($request->input('first', false));
        $i = 0;
        $count = intval($request->input('count', 0));

        $date = K::firstDayOfWeek($request->date);

        $routes_available = $user->hasRoutesToDate($date);

        $weeks = [];
        while (count($weeks) < 4 && $routes_available) {
            if ((!$first) || ($i && $first)) {
                $date->sub('week', 1);
            }
            $i++;
            $week = $date->copy();
            $routes = $user->routesByWeek($week)->sortByDesc('date');
            $routes_available = $user->hasRoutesToDate($week);

            if ($routes->isEmpty())
                continue;

            $weeks[] = $routes;
            $count += $routes->count();
        }

        $items = collect();
        foreach ($weeks as $routes) {
            $r = $routes->first();
            $d = $r->dsp();

            $weekFilter = collect();
            $routes->each(fn($item) => $weekFilter->add($item->only([
                'id',
                'date',
                'invoice_mileage',
                'bonus',
                'vat'
            ])));

            $week = $r->date->week();
            $time = $routes->sum('time');
            $hours = floor($time / 3600);
            $minutes = ($time % 3600) / 60;
            $total = $routes->sum('total_pay');
            $actual = $routes->sum('actual_pay');

            $items->push(collect([
                'is' => 'week',
                'week' => $week,
                'year' => $r->date->year,
                'time' => $time,
                'duration' => K::pluralize('% hr', '% hrs', $hours) . ($minutes > 0 ? K::pluralize(' and % min', ' and % mins', $minutes) : ''),
                'miles' => $routes->sum('miles'),
                'fuel_spend' => '@ ' . K::formatCurrency($routes->sum('fuel_spend')),
                'mileage' => $routes->sum('mileage'),
                'fuel_pay' => '@ ' . K::formatCurrency($routes->sum('fuel_pay')),
                'total_pay' => K::formatCurrency($total),
                'total_hourly' => '@ ' . K::formatCurrency(K::getHourly($total, $hours, $minutes)) . ' ph',
                'actual_pay' => K::formatCurrency($actual),
                'actual_hourly' => '@ ' . K::formatCurrency(K::getHourly($actual, $hours, $minutes)) . ' ph',
                'stops' => $routes->sum('stops'),
                'stops_avg' => '@ ' . round($routes->where('stops')->avg('stops_hourly'), 1) . ' ph',
                'pay_day' => K::displayDate(K::getPayDay($r->date, $d->in_hand, $d->pay_day)),
                'rate_is_set' => $user->weeksFuelRateIsSet($r->depot_id, $r->date),
                'modal' => [
                    'week' => [
                        'title.text' => $week,
                        'week.data' => [
                            'routes' => $weekFilter,
                        ],
                    ],
                    'rate' => [
                        'title.text' => Msg::add('rate'),
                        'form.action' => route('rate.add'),
                        'type.value' => old('type', 'fuel'),
                        'date.value' => old('date', K::firstDayOfWeek($r->date)->format('Y-m-d')),
                        'depot_id.value' => old('depot_id', $r->depot_id),
                        'amount.value' => old('amount', ''),
                        'destroy.addclass' => 'hidden',
                        'submit.text' => 'add',
                    ]
                ]
            ]));

            $routes->each(function ($r) use ($items) {
                $items->push(collect([
                    'is' => 'route',
                    'id' => $r->id,
                    'date' => $r->date,
                    'date_ymd' => $r->date->format('Y-m-d'),
                    'date_full' => K::displayDate($r->date, 'D, jS M y'),
                    'date_year' => $r->date->year,
                    'date_display' => K::displayDate($r->date),
                    'time' => K::formatTime($r->start_time) . ' - ' . ($r->end_time ? K::formatTime($r->end_time) : '??'),
                    'time_string' => $r->time_string,
                    'miles' => $r->miles,
                    'fuel_spend' => '@ ' . K::formatCurrency($r->fuel_spend),
                    'mileage' => $r->mileage,
                    'fuel_pay' => '@ ' . K::formatCurrency($r->fuel_pay),
                    'total_pay' => K::formatCurrency($r->total_pay),
                    'total_hourly' => '@ ' . K::formatCurrency($r->total_hourly) . ' ph',
                    'actual_pay' => K::formatCurrency($r->actual_pay),
                    'actual_hourly' => '@ ' . K::formatCurrency($r->actual_hourly) . ' ph',
                    'stops' => $r->stops,
                    'stops_hourly' => '@ ' . $r->stops_hourly . ' ph',
                    'depot_identifier' => $r->depot->identifier,
                    'depot_location' => $r->depot->location,
                    'type' => $r->getType(),
                    'has_extra' => $r->hasExtra(),
                    'modal' => ['route' => [
                        'title.text' => Msg::edit('route'),
                        'form.action' => route('route.edit', $r->id),
                        'type.value' => old('type', $r->type),
                        'depot_id.value' => old('depot_id', $r->depot_id),
                        'date.value' => old('date', K::date($r->date)->format('Y-m-d')),
                        'start_time.value' => old('start_time', K::date($r->start_time)->format('g:i A')),
                        'end_time.value' => old('end_time', $r->end_time ? K::date($r->end_time)->format('g:i A') : ''),
                        'start_mileage.value' => old('start_mileage', $r->start_mileage),
                        'start_mileage_plus.value' => old('start_mileage_plus', ''),
                        'end_mileage.value' => old('end_mileage', $r->end_mileage),
                        'end_mileage_plus.value' => old('end_mileage_plus', ''),
                        'invoice_mileage.value' => old('invoice_mileage', $r->invoice_mileage),
                        'stops.value' => old('stops', $r->stops),
                        'bonus.value' => old('bonus', $r->bonus),
                        'vat.checked' => old('vat', $r->vat),
                        'ttfs.value' => old('ttfs', $r->ttfs),
                        'note.value' => old('note', $r->note),
                        'destroy.removeclass' => 'hidden',
                        'destroy.data' => [
                            'modal' => [
                                'form.action' => route('route.destroy', $r->id),
                            ],
                        ],
                        'submit.text' => 'save',
                        'more-btn.text' => 'Show More',
                        'more.addclass' => 'hidden',
                    ], 'extra' => [
                        'bonus.text' => $r->bonus,
                        'note.text' => $r->note,
                        'note-wrap.' . ($r->note ? 'remove' : 'add') . 'class' => 'hidden',
                        'bonus-wrap.' . ($r->bonus ? 'remove' : 'add') . 'class' => 'hidden',
                    ]],
                ]));
            });
        }

        return response()->json([
            'items' => $items,
            'available' => $routes_available,
            'total' => $count,
        ]);
    }

    /**
     * Get more weeks of the users routes via ajax.
     * 
     * @return string
     */
    public function getRender(Request $request) {
        /* TODO:: make sure this works with a gap of more than 4 weeks */
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

        return back()->with('success', Msg::added('route'));
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

        return back()->with('success', Msg::edited('route'));
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

        return back()->with('success', 'Successfully updated the week!');
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

        return back()->with('success', Msg::added('routes'));
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

        return back()->with('success', Msg::deleted('route'));
    }

    /**
     * Export all of the user's routes.
     * 
     * @param \Illuminate\Http\Request
     * @return void
     */
    public function export(Request $request) {
        $this->doExport($request->user()->routes, ['depot_id', 'date', 'start_time', 'end_time', 'start_mileage', 'end_mileage', 'stops', 'invoice_mileage', 'bonus', 'type', 'vat', 'ttfs'], 'routes');
    }
}
