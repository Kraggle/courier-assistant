<?php

namespace App\Models;

use App\Helpers\K;
use App\Helpers\Lists;
use App\Models\Refuel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Route extends Model {

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',
        'depot_id',
        'date',
        'start_time',
        'end_time',
        'start_mileage',
        'end_mileage',
        'stops',
        'invoice_mileage',
        'bonus',
        'note',
        'type',
        'ttfs',
        'vat',
    ];

    /**
     * Attributes to be appended to the model.
     * 
     * @var array
     */
    protected $appends = [
        'time',
        'hours',
        'minutes',
        'time_in_minutes',
        'time_string',
        'miles',
        'day_rate',
        'invoice_fuel_rate',
        'fuel_rate',
        'fuel_pay',
        'fuel_spend',
        'total_pay',
        'total_hourly',
        'actual_pay',
        'actual_hourly',
        'stops_hourly',
        'mileage'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'vat' => 'boolean',
    ];

    /**
     * Get the users associated with the route.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rate for this route.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rate($type = null): ?Rate {
        return $this->user->rate($this->depot_id, K::date($this->date), $type ?? $this->type);
    }

    /**
     * See if this route has a rate.
     */
    public function hasRate($type = null) {
        return $this->rate($type) !== null;
    }

    /**
     * Get the total time for the route.
     */
    protected function time(): Attribute {
        return new Attribute(
            get: fn () => $this->start_time->diffInSeconds($this->end_time ?? $this->start_time),
        );
    }

    /**
     * Get the total hours for the route.
     */
    protected function hours(): Attribute {
        return new Attribute(
            get: fn () => floor($this->time / 3600) ?? 0,
        );
    }

    /**
     * Get the total minutes for the route.
     */
    protected function minutes(): Attribute {
        return new Attribute(
            get: fn () => ($this->time % 3600) / 60 ?? 0,
        );
    }

    /**
     * Get the total time in minutes
     */
    protected function timeInMinutes(): Attribute {
        return new Attribute(
            get: fn () => $this->hours * 60 + $this->minutes,
        );
    }

    /**
     * Get the time string for the route.
     */
    protected function timeString(): Attribute {
        return new Attribute(
            get: fn () => K::pluralize('% hr', '% hrs', $this->hours) . ($this->minutes > 0 ? K::pluralize(' % min', ' % mins', $this->minutes) : ''),
        );
    }

    /**
     * Get the total actually driven miles for the route.
     */
    protected function miles(): Attribute {
        return new Attribute(
            get: fn () => $this->end_mileage ? $this->end_mileage - $this->start_mileage : 0,
        );
    }

    /**
     * See if this route has miles.
     */
    public function hasMiles() {
        return $this->miles > 0;
    }


    /**
     * Get the day rate for the route.
     */
    protected function dayRate(): Attribute {
        return new Attribute(
            get: fn () => $this->rate()->amount ?? 0,
        );
    }

    /**
     * Get the day rate for the route.
     */
    protected function invoiceFuelRate(): Attribute {
        return new Attribute(
            get: fn () => $this->rate('Fuel')->amount ?? 0.22,
        );
    }

    /**
     * Get the fuel rate for the route.
     */
    protected function fuelRate(): Attribute {
        return new Attribute(
            get: fn () => $this->user->refuelsToDate(K::date($this->date))->first()->fuel_rate ?? 0.22,
        );
    }

    /**
     * Get the fuel pay for the route.
     */
    protected function fuelPay(): Attribute {
        return new Attribute(
            get: fn () => $this->invoice_fuel_rate * $this->mileage,
        );
    }

    /**
     * Get the fuel spend for the route.
     */
    protected function fuelSpend(): Attribute {
        return new Attribute(
            get: fn () => $this->fuel_rate * $this->miles,
        );
    }

    /**
     * Get the total pay for the route.
     */
    protected function totalPay(): Attribute {
        return new Attribute(
            get: fn () => ($this->day_rate + $this->bonus + $this->fuel_pay) * ($this->vat ? 1.2 : 1),
        );
    }

    /**
     * Get the total hourly pay for the route.
     */
    protected function totalHourly(): Attribute {
        return new Attribute(
            get: fn () => $this->time ? $this->total_pay / ($this->timeInMinutes / 60) : 0,
        );
    }

    /**
     * Get the actual pay for the route.
     */
    protected function actualPay(): Attribute {
        return new Attribute(
            get: fn () => $this->total_pay - $this->fuel_spend,
        );
    }

    /**
     * Get the actual hourly pay for the route.
     */
    protected function actualHourly(): Attribute {
        return new Attribute(
            get: fn () => $this->time ? $this->actual_pay / ($this->timeInMinutes / 60) : 0,
        );
    }

    /**
     * Get the stop per hour for the route.
     */
    protected function stopsHourly(): Attribute {
        return new Attribute(
            get: fn () => $this->time ? round($this->stops / (($this->timeInMinutes - ($this->ttfs * 2)) / 60), 1) : null,
        );
    }

    /**
     * Get the depot for the route.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function depot(): BelongsTo {
        return $this->belongsTo(Depot::class);
    }

    /**
     * Get the invoice mileage or fallback to driven mileage for the route.
     */
    protected function mileage(): Attribute {
        return new Attribute(
            get: fn () => $this->invoice_mileage ?: $this->miles,
        );
    }

    /**
     * Get the types list value from the type key.
     * 
     * @return string
     */
    public function getType() {
        return Lists::routeTypes($this->type);
    }

    /**
     * Has the user added a bonus or a note?
     * 
     * @return boolean
     */
    public function hasExtra() {
        return $this->bonus || $this->note;
    }
}
