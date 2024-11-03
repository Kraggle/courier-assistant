<?php

namespace App\Models;

use App\Helpers\K;
use Illuminate\Support\Str;
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
    public function ratesRaw(): HasMany {
        return $this->hasMany(Rate::class, 'dsp_id');
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
     * See if this DSP has rates
     * 
     * @return boolean
     */
    public function hasRates(): bool {
        return $this->rates()->count() > 0;
    }

    /**
     * Get the users expenses with filters.
     * 
     * @param $opts array
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ratesWithFilters($opts = []) {
        extract(K::merge([
            'by' => 'date',
            'dir' => 'desc',
            'search' => ''
        ], $opts));

        return $this->ratesRaw
            ->when($dir == 'asc', function ($query) use ($by) {
                return $query->sortBy($by)->values();
            })
            ->when($dir == 'desc', function ($query) use ($by) {
                return $query->sortByDesc($by)->values();
            })
            ->when($search, function ($query) use ($search) {
                return $query->filter(function ($expense) use ($search) {
                    $columns = ['type_display', 'creator', 'depot_identifier'];
                    foreach ($columns as $column) {
                        if (strpos(Str::lower($expense->{$column}), Str::lower($search)) !== false) {
                            return true;
                        }
                    }
                });
            });
    }

    /**
     * Get th number of users associated with the DSP.
     * 
     * @return int
     */
    protected function count(): Attribute {
        return new Attribute(
            get: fn() => $this->users()->count(),
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
