<?php

namespace App\Models;

use App\Models\DSP;
use App\Models\Depot;
use App\Helpers\Lists;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model {
    use LogsActivity;

    protected $fillable = [
        'dsp_id',
        'depot_id',
        'date',
        'type',
        'amount'
    ];

    /**
     * Attributes to be appended to the model.
     * 
     * @var array
     */
    protected $appends = [
        'creator',
        'depot_identifier',
        'type_display',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logOnly(['depot_id', 'date', 'type', 'amount'])
            ->logOnlyDirty();
    }

    /**
     * Get the DSP associated with the rate.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function dsp(): BelongsTo {
        return $this->belongsTo(DSP::class);
    }

    /**
     * Get the depot associated with the rate.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function depot(): BelongsTo {
        return $this->belongsTo(Depot::class);
    }

    /**
     * Get the types list value from the type key.
     * 
     * @param boolean $html
     * @return string
     */
    public function getType($html = true, $class = '') {
        $type = $this->type_display;
        if ($html) return str_replace(':class', $class, preg_replace(
            '/(\([^)]+\))/',
            '<span class=":class pl-1 text-xs text-gray-400">\1</span>',
            $type
        ));
        return $type;
    }

    /**
     * Get the logs associated with the rate.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function logs(): Collection {
        return Activity::where('subject_type', self::class)->where('subject_id', $this->id)->get();
    }

    /**
     * See if the rate has logs.
     * 
     * @return bool
     */
    public function hasLogs(): bool {
        return $this->logs()->count() > 0;
    }

    /**
     * Get the logs associated with the rate.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function changeLogs(): Collection {
        return Activity::where('subject_type', self::class)->where('subject_id', $this->id)->where('event', 'updated')->orderBy('created_at', 'desc')->get();
    }

    /**
     * See if the rate has changes.
     * 
     * @return bool
     */
    public function hasChangeLogs(): bool {
        return $this->changeLogs()->count() > 0;
    }

    /**
     * Get the logs associated with the rate.
     * 
     * @return Activity
     */
    public function createLog(): ?Activity {
        return Activity::where('subject_type', self::class)->where('subject_id', $this->id)->where('event', 'created')->get()->first();
    }

    /**
     * See if the rate has creates.
     * 
     * @return bool
     */
    public function hasCreateLog(): bool {
        return $this->createLog() != null;
    }

    /**
     * Get the creator for the rate.
     */
    protected function creator(): Attribute {
        return new Attribute(
            get: fn() => $this->createLog()->causer->name ?? User::all()->first()->name,
        );
    }

    /**
     * Get the creator for the rate.
     */
    protected function depotIdentifier(): Attribute {
        return new Attribute(
            get: fn() => $this->depot->identifier,
        );
    }

    /**
     * Get the creator for the rate.
     */
    protected function typeDisplay(): Attribute {
        return new Attribute(
            get: fn() => Lists::rateTypes($this->type),
        );
    }
}
