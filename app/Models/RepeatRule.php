<?php

namespace App\Models;

use App\Helpers\K;
use App\Casts\RepeatItem;
use App\Casts\RepeatRules;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepeatRule extends Model {
    protected $table = 'repeat_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'rules->repeat',
        'rules->every',
        'rules->every_x',
        'rules->month',
        'item->describe',
        'item->cost',
        'item->type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rules' => RepeatRules::class,
        'item' => RepeatItem::class,
    ];

    /**
     * Get the expenses that belong to this rule
     */
    public function expenses(): HasMany {
        return $this->hasMany(Expense::class, 'repeat_id')->orderBy('date');
    }

    /**
     * Get the user that owns this rule.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Create the expenses for this rule.
     * 
     * @param Carbon|null $start
     * @return self
     */
    public function createRepeats(Carbon $start = null) {
        $start = $start ?? $this->start_date;
        $dates = $this->generateDates($start);
        $this->deleteRepeats($start);
        $expenses = [];

        foreach ($dates as $date) {
            $expenses[] = K::merge((array) $this->item, [
                'date' => $date,
                'user_id' => $this->user_id,
            ]);
        }

        $this->expenses()->createMany($expenses);

        return $this;
    }

    /**
     * Delete repeats from the given date.
     * 
     * @param Carbon $start
     */
    public function deleteRepeats(Carbon $start) {
        $this->expenses()->where('date', '>=', $start)->delete();
    }

    /**
     * Generate the dates from the given start date
     * 
     * @param Carbon $start
     * 
     * @return Array<Carbon>
     */
    function generateDates(Carbon $start) {
        $dates = [];
        $date = $start->clone();
        $end = $this->end_date;

        $x = $this->rules->every_x;
        $every = $this->rules->every;
        $month = $this->rules->month;
        $nth = $start->weekOfMonth;
        $day = $start->dayOfWeek;

        while ($date->lessThanOrEqualTo($end)) {
            $dates[] = $date->clone();

            $date->add($every, $x);

            if ($every == 'month') {
                switch ($month) {
                    case 'nth':
                        $date->nthOfMonth($nth, $day);
                        break;
                    case 'last':
                        $date->lastOfMonth($day);
                        break;
                }
            }
        }

        return $dates;
    }

    /**
     * Add additional function to events.
     */
    public static function boot() {
        parent::boot();

        self::deleting(function ($model) {
            $model->deleteRepeats($model->start_date);
        });
    }
}
