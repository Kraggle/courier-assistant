<?php

namespace App\Models;

use App\Helpers\K;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DSP extends Model {
    use LogsActivity;

    protected $table = 'dsps';

    protected $fillable = [
        'name',
        'identifier',
        'in_hand',
        'pay_day',
    ];

    protected $appends = [
        'count'
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logOnly(['name', 'identifier', 'in_hand', 'pay_day'])
            ->logOnlyDirty();
    }

    /** 
     * Get the users associated with the DSP.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'dsp_user', 'dsp_id', 'user_id')->withPivot('date')->withTimestamps();
    }

    /** 
     * Get the rates associated with the DSP.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rates(): HasMany {
        return $this->hasMany(Rate::class, 'dsp_id')->orderByDesc('date');
    }

    /**
     * Get the latest rate associated with the DSP.
     * 
     * @return \App\Models\Rate
     */
    public function rate(): ?Rate {
        return $this->rates()->first();
    }

    /**
     * Get th number of users associated with the DSP.
     * 
     * @return int
     */
    protected function count(): Attribute {
        return new Attribute(
            get: fn () => $this->users()->count(),
        );
    }

    /**
     * See if the user has a rate for the given date.
     * 
     * @param $date
     * @param string $type
     * @param int $depot_id
     * @return boolean
     */
    public function hasRate($date, string $type, int $depot_id): bool {
        return $this->rates->where('date', K::date($date))->where('type', $type)->where('depot_id', $depot_id)->count() > 0;
    }
}
