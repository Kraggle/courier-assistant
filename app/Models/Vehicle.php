<?php

namespace App\Models;

use App\Helpers\K;
use App\Models\User;
use App\Models\Refuel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model {
    protected $fillable = [
        'user_id',
        'reg'
    ];

    /**
     * Get the user associated with the vehicle.
     * 
     * @return User
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the refuels associated with the vehicle.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuels(): HasMany {
        return $this->hasMany(Refuel::class)->orderByDesc('date')->orderByDesc('mileage');
    }

    /**
     * See if the vehicle has any refuels.
     * 
     * @return bool
     */
    public function hasRefuels(): bool {
        return $this->refuels->count() > 0;
    }

    /**
     * Check if the vehicle already has a refuel for the given mileage.
     * 
     * @param int $mileage
     * 
     * @return bool
     */
    public function hasRefuelForMileage(int $mileage): bool {
        return $this->refuels->where('mileage', $mileage)->count() > 0;
    }

    /**
     * Get the user latest refuel.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuel(): Refuel {
        return $this->refuels->first();
    }

    /**
     * Get the users refuels upto the given date.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuelsToDate($date): Collection {
        return $this->refuels->where('date', '<=', K::date($date))->sortByDesc('date');
    }

    /**
     * Get the users refuels from the given date.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refuelsFromDate($date): Collection {
        return $this->refuels->where('date', '>=', K::date($date))->sortByDesc('date')->sortByDesc('mileage');
    }

    /**
     * Get the users next refuel from the given date.
     * 
     * @return Refuel|null
     */
    public function nextRefuel($date): ?Refuel {
        return $this->refuels->where('date', '>', K::date($date))->sortByDesc('date')->sortByDesc('mileage')->last();
    }

    /**
     * Get the users previous refuel from the given date.
     * 
     * @return Refuel|null
     */
    public function previousRefuel($date): ?Refuel {
        return $this->refuels->where('date', '<', K::date($date))->sortByDesc('date')->sortByDesc('mileage')->first();
    }

    /**
     * Override the delete function to delete uploaded images.
     */
    public static function boot() {
        parent::boot();

        self::deleting(function ($vehicle) {
            $vehicle->refuels()->each(fn ($item) => $item->delete());
        });
    }
}
