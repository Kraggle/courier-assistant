<?php

namespace App\Models;

use App\Helpers\K;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tax extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tax_year',
        'claim_miles',
        'force_update',
        'properties->miles->driven',
        'properties->miles->reimbursed',
        'properties->miles->claimable',
        'properties->fuel->paid',
        'properties->fuel->spent',
        'properties->fuel->earned',
        'properties->work->days',
        'properties->work->week',
        'properties->work->hours',
        'properties->work->total',
        'properties->income->total->all',
        'properties->income->total->day',
        'properties->income->total->hour',
        'properties->income->actual->all',
        'properties->income->actual->day',
        'properties->income->actual->hour',
        'properties->income->bonus',
        'properties->expense->work',
        'properties->expense->vehicle',
        'properties->expense->maintenance',
        'properties->expense->office',
        'properties->expense->interest',
        'properties->expense->charges',
        'properties->expense->professional',
        'properties->expense->total',
    ];

    protected $casts = [
        'properties' => 'object'
    ];

    /**
     * Get the user that owns the tax.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    private $properties = [
        'miles' => [],
        'fuel' => [],
        'work' => [],
        'income' => [
            'total' => [],
            'actual' => []
        ],
        'expense' => [],
    ];

    public function emptyProperties() {
        $this->properties = K::toObject($this->properties);
    }
}
