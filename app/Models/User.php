<?php

namespace App\Models;

use App\Helpers\K;
use App\Models\DSP;
use Laravel\Cashier\Billable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dsp_id',
        'options->depot_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'options' => 'object',
    ];

    /**
     * See if the user has set their Depot.
     * 
     * @return bool
     */
    public function hasDepot(): bool {
        return $this->options->depot_id ?? false;
    }

    /**
     * Get the users routes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routes(): HasMany {
        return $this->hasMany(Route::class);
    }

    /**
     * Get the users routes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function route(): ?Route {
        return $this->routes->sortByDesc('date')->first();
    }

    /**
     * Get the users routes between dates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routesByDate($start, $end) {
        return $this->routes->where('date', '>', K::date($start)->sub('day', 1))->where('date', '<=', K::date($end))->sortBy('date');
    }

    /**
     * Get the users routes for a given week number and year.
     * 
     * @param $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routesByWeek($date) {
        $start = K::date($date)->startOfWeek();
        $end = $start->clone()->endOfWeek();

        return $this->routesByDate($start, $end);
    }


    /**
     * Get the users routes for a given year.
     * 
     * @param $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routesByYear($year) {
        $start = K::date("$year-01-01");
        $end = K::date("$year-12-31");
        return $this->routesByDate($start, $end)->sortByDesc('date');
    }

    /** 
     * Get the users DSPs.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function dsps(): BelongsToMany {
        return $this->belongsToMany(DSP::class, 'dsp_user', 'user_id', 'dsp_id')->withPivot('date')->orderByPivot('date', 'desc')->withTimestamps();
    }

    /**
     * Get the users DSP by date.
     * 
     * @return \App\Models\DSP
     */
    public function dspByDate($date = ''): ?DSP {
        return $this->dsps()->wherePivot('date', '<=', K::date($date))->first();
    }

    /**
     * Get the users first DSP.
     * 
     * @return \App\Models\DSP
     */
    public function firstDSP(): DSP {
        return $this->dsps->last();
    }

    /** 
     * Get the users latest DSP.
     * 
     * @return \App\Models\DSP
     */
    public function dsp(): ?DSP {
        return $this->dsps()->first();
    }

    /**
     * See if the user has added a DSP.
     * 
     * @return bool
     */
    public function hasDSP(): bool {
        return $this->dsp() !== null;
    }


    /**
     * See if the user has a DSP by date.
     * 
     * @return bool
     */
    public function hasDSPByDate($date = ''): bool {
        return $this->dspByDate($date) !== null;
    }

    /**
     * Get the users latest rate by type.
     * 
     * @return Rate|null
     */
    public function rate(int $depot_id, $date = '', string $type = "md"): ?Rate {
        if ($this->hasDSP()) {
            $date = K::date($date)->format('Y-m-d');
            $dsp_id = $this->dspByDate($date)->id;
            return Rate::where('dsp_id', $dsp_id)->where('depot_id', $depot_id)->where('type', $type)->where('date', '<=', $date)->orderBy('date', 'desc')->first();
        }
        return null;
    }

    /**
     * Is the fuel rate set for the given week?
     * 
     * @return bool
     */
    public function weeksFuelRateIsSet(int $depot_id, $date): bool {
        $date = K::firstDayOfWeek($date)->format('Y-m-d');
        if (!$this->hasDSPByDate($date)) return false;
        $dsp_id = $this->dspByDate($date)->id;
        return  Rate::where('dsp_id', $dsp_id)->where('depot_id', $depot_id)->where('type', 'fuel')->where('date', $date)->first() !== null;
    }

    /**
     * See if the user has a rate.
     * 
     * @return bool
     */
    public function hasRate(): bool {
        return $this->hasDSP() && $this->dsp()->rates->count() > 0;
    }

    /**
     * Get the users vehicles.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function vehicles(): HasMany {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the users vehicles by date.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function vehiclesByDate() {
        return $this->vehicles->sortByDesc('updated_at');
    }

    /**
     * Get the users latest vehicle.
     * 
     * @return \App\Models\Vehicle
     */
    public function vehicle(): ?Vehicle {
        return $this->vehiclesByDate()->first();
    }

    /**
     * See if the user already has a vehicle with given reg.
     * 
     * @param string $reg
     * @return bool
     */
    public function hasVehicleByReg(string $reg): bool {
        return $this->vehicles->where('reg', $reg)->count() > 0;
    }

    /**
     * Check if the user has at least one vehicle.
     * 
     * @return bool 
     */
    public function hasVehicle(): bool {
        return $this->vehicle() !== null;
    }

    /**
     * Get the users refuels.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuels(): HasManyThrough {
        return $this->hasManyThrough(Refuel::class, Vehicle::class)->orderByDesc('date');
    }

    /**
     * Get the users refuels.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuelsByDate($start, $end): Collection {
        return $this->refuels->where('date', '>', K::date($start)->sub('day', 1))->where('date', '<=', K::date($end));
    }

    /**
     * Get the user latest refuel.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuel(): Refuel {
        return $this->refuels()->first();
    }

    /**
     * Get the users refuels upto the given date.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuelsToDate($date): Collection {
        return $this->refuels->where('date', '<=', K::date($date));
    }

    /**
     * Get the users refuels from the given date.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuelsFromDate($date): Collection {
        return $this->refuels->where('date', '>=', K::date($date));
    }

    /**
     * Get the users next refuel from the given date.
     * 
     * @return Refuel|null
     */
    public function nextRefuel($date): ?Refuel {
        return $this->refuels->where('date', '>', K::date($date))->last();
    }

    /**
     * Get the users previous refuel from the given date.
     * 
     * @return Refuel|null
     */
    public function previousRefuel($date): ?Refuel {
        return $this->refuels->where('date', '<', K::date($date))->first();
    }

    /**
     * See if the user has a route for the given date.
     * 
     * @param string|Carbon $date
     * @return boolean
     */
    public function hasRoute($date): bool {
        return $this->routes->where('date', K::date($date))->count() > 0;
    }

    /**
     * See if the user has a routes.
     * 
     * @return boolean
     */
    public function hasRoutes(): bool {
        return $this->routes->count() > 0;
    }

    /**
     * Get the users expenses.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses(): HasMany {
        return $this->hasMany(Expense::class)->where('date', '<=',  K::date())->orderBy('date', 'desc');
    }

    /**
     * See if the user has any expenses.
     * 
     * @return boolean 
     */
    public function hasExpenses() {
        return $this->expenses->count() > 0;
    }

    /**
     * See if the user has a specific expense.
     * 
     * @param $date,
     * @param string $describe
     * @param float $cost
     * @return boolean 
     */
    public function hasExpense($date, string $describe, float $cost): bool {
        return $this->expenses->where('date', K::date($date))->where('describe', $describe)->where('cost', $cost)->count() > 0;
    }

    /**
     * Get the users expenses between dates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function expensesByDate($start, $end) {
        return $this->expenses->where('date', '>', K::date($start)->sub('day', 1))->where('date', '<=', K::date($end))->sortBy('date');
    }

    /**
     * Get the users tax years.
     */
    public function taxYears() {
        $now   = K::date();
        $year  = $now->year;
        $month = $now->month;
        $start = K::date(($month > 3 ? $year : $year - 1) . "-04-01");
        $end   = K::date(($month > 3 ? $year + 1 : $year) . "-03-31");
        $years = [];

        $routes = $this->routesByDate($start, $end)->take(1);

        do {
            $date = K::date($routes->first()->date);

            $years[] = (object) [
                'start' => $start->copy(),
                'end'   => $end->copy(),
                'tab'   => "{$start->format('y')}/{$end->format('y')}",
                'year'  => $start->copy()->year,
                'weeks' => max(1, $date->diffInWeeks($end > now() ? now() : $end)),
            ];

            $routes = $this->routesByDate($start->subYears(1), $end->subYears(1))->take(1);
        } while ($routes->count() > 0);

        return collect($years);
    }

    /**
     * Get the users taxes.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxes(): HasMany {
        return $this->hasMany(Tax::class);
    }

    /**
     * Get the users taxes by year.
     * 
     * @return \App\Models\Tax
     */
    public function taxByYear($year): ?Tax {
        return $this->taxes()->where('tax_year', $year)->first();
    }

    /**
     * See if the user has any taxes.
     * 
     * @return boolean 
     */
    public function hasTaxes() {
        return $this->taxes->count() > 0;
    }

    /**
     * Override the default delete function to get all associated data.
     */
    public static function boot() {
        parent::boot();

        self::deleting(function ($user) {
            $user->subscription('default')->cancelNow();
            $user->routes()->each(fn ($item) => $item->delete());
            $user->vehicles()->each(fn ($item) => $item->delete());
            $user->expenses()->each(fn ($item) => $item->delete());
            $user->taxes()->each(fn ($item) => $item->delete());
            $user->dsps()->detach();
        });
    }
}