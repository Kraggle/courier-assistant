<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Models\Tax;
use App\Helpers\Msg;
use App\Models\User;
use App\Models\Route;
use Illuminate\Http\Request;

class TaxController extends Controller {
    /**
     * Show the tax summary page.
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(int $year) {
        $user  = K::user();
        $tax = $this->generateOrUpdate($user, $year);

        return view('tax.show', [
            'user' => $user,
            'year' => $year,
            'tax' => $tax
        ]);
    }

    /**
     * Edit the users tax.
     * 
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Tax $tax,
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Tax $tax) {
        $tax->update([
            'force_update' => 1
        ]);

        return back()->with('success', 'Your tax has been regenerated.');
    }

    /**
     * Generate the tax record for the given year and user.
     * 
     * @param \App\Models\User $user
     * @param int $year
     * @return \App\Models\Tax
     */
    private function generateOrUpdate(User $user, int $year) {
        $tax = $user->taxByYear($year);
        if (!$tax) {
            $tax = $user->taxes()->create([
                'tax_year' => $year,
                'force_update' => 1
            ]);
        }

        $years = $user->taxYears();
        $current = $years->where('year', $year)->first();
        $start = $current->start;
        $end = $current->end;
        $end = $end > now() ? now() : $end;
        $updated = $tax->updated_at;

        $updated_at = collect([
            [
                'date' => $user->routesByDate($start, $end)
                    ->sortByDesc('updated_at')
                    ->first()?->updated_at ?? K::date('2000-01-01')
            ],
            [
                'date' => $user->expensesByDate($start, $end)
                    ->sortByDesc('updated_at')
                    ->first()?->updated_at ?? K::date('2000-01-01')
            ],
            [
                'date' => $user->refuelsByDate($start, $end)
                    ->sortByDesc('updated_at')
                    ->first()?->updated_at ?? K::date('2000-01-01')
            ]
        ])->sortByDesc('date')->first()['date'];

        if (!($tax->force_update || $updated_at > $updated))
            return $tax;

        K::log('updating');

        $routes = $user->routesByDate($start, $end)->lazy();
        $mileage = $routes->sum('mileage');
        $fuel_pay = $routes->sum('fuel_pay');
        $fuel_spend = $routes->sum('fuel_spend');
        $time = $routes->sum('time');
        $count = $routes->count();
        $total = $routes->sum('total_pay');
        $bonus = $routes->sum('bonus');
        $miles = $routes->sum('miles');

        $claimable = $mileage > 10000 ? 10000 * 0.45 + ($mileage - 10000) * 0.25 : $mileage * 0.45;
        $hours = floor($time / 3600);
        $minutes = ($time % 3600) / 60;

        $expenses = $user->expensesByDate($start, $end)->lazy();
        $expense_sum =  $fuel_pay + $expenses->sum('cost');

        $actual = $total - $expense_sum;

        $tax->update([
            'properties->miles->driven' => $miles,
            'properties->miles->reimbursed' => $mileage,
            'properties->miles->claimable' => $claimable,
            'properties->fuel->paid' => $fuel_pay,
            'properties->fuel->spent' => $fuel_spend,
            'properties->fuel->earned' => $fuel_pay - $fuel_spend,
            'properties->work->days' => $count,
            'properties->work->week' => round($count / $current->weeks),
            'properties->work->hours' => $time / $count,
            'properties->work->total' => $time,
            'properties->income->total->all' => $total,
            'properties->income->total->day' => $total / $count,
            'properties->income->total->hour' => K::getHourly($total, $hours, $minutes),
            'properties->income->actual->all' => $actual,
            'properties->income->actual->day' => $actual / $count,
            'properties->income->actual->hour' => K::getHourly($actual, $hours, $minutes),
            'properties->income->bonus' => $bonus,
            'properties->expense->work' => $expenses->where('type', 'work')->sum('cost'),
            'properties->expense->vehicle' => $expenses->whereIn('type', ['vehicle', 'maintenance'])->sum('cost') + $fuel_pay,
            'properties->expense->office' => $expenses->where('type', 'office')->sum('cost'),
            'properties->expense->interest' => $expenses->where('type', 'interest')->sum('cost'),
            'properties->expense->professional' => $expenses->where('type', 'professional')->sum('cost'),
            'properties->expense->total' => $expense_sum,
            'force_update' => 0
        ]);

        $expenses = null;

        return $tax;
    }
}
