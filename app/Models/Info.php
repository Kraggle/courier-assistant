<?php

namespace App\Models;

use App\Helpers\K;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Info extends Model {
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'year',
        'position->lat',
        'position->lng',
        'address',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'position' => 'object',
    ];

    /**
     * Attributes to be appended to the model.
     * 
     * @var array
     */
    protected $appends = [
        'creator',
        'changes'
    ];

    /**
     * The table name in the database.
     */
    protected $table = 'info';

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Get the creator of the info.
     */
    protected function creator(): Attribute {
        return new Attribute(
            get: fn () => $this->createLog()->causer->name
        );
    }

    /**
     * Get the changes for the info.
     */
    protected function changes(): Attribute {
        return new Attribute(
            get: function () {
                $logs = [];
                foreach ($this->changeLogs() as $log) {
                    // K::log($log);

                    $props = [
                        'attributes' => $this->combine($log->properties['attributes']),
                        'old' => $this->combine($log->properties['old'])
                    ];

                    $logs[] = [
                        'date' => K::displayDate($log->created_at, 'd-m-Y'),
                        'properties' => $props,
                        'user' => $log->causer->name,
                    ];
                }
                return $logs;
            }
        );
    }

    private function combine($array) {
        $result = [];
        foreach ($array as $k => $v) {
            if ($k == 'position')
                $result[$k] = "{$v['lat']}, {$v['lng']}";
            else
                $result[$k] = $v;
        }
        return $result;
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
}
