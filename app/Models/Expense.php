<?php

namespace App\Models;

use App\Helpers\Lists;
use Illuminate\Support\Facades\Vite;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Expense Model
 */
class Expense extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'describe',
        'cost',
        'ignore',
        'type',
        'image',
        'repeat_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'ignore' => 'boolean',
    ];

    /**
     * Get the user this belongs to.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the types list value from the type key.
     * 
     * @return string
     */
    public function getType() {
        return Lists::expenseTypes($this->type);
    }

    /**
     * Check if the expense has an image.
     * 
     * @return bool
     */
    public function hasImage() {
        return $this->image ? Storage::disk('gcs')->exists($this->image) : false;
    }

    /**
     * Get the image url.
     * 
     * @return string
     */
    public function getImageURL() {
        $url = $this->hasImage() ? Storage::url($this->image) : Vite::asset('resources/images/no-image.svg');
        return preg_match('/.pdf$/', $url) ? Vite::asset('resources/images/no-pdf.svg') : $url;
    }

    /**
     * See if this expense is in the future.
     * 
     * @return bool
     */
    public function isFuture() {
        return $this->date > now();
    }

    /**
     * Is pdf file.
     * 
     * @return bool
     */
    public function isPDF() {
        return preg_match('/.pdf$/', $this->getImageURL() ?? '') ?? false;
    }

    /**
     * Check if this is a repeat expense.
     * 
     * @return bool
     */
    public function isRepeat(): bool {
        return $this->repeat != null;
    }

    /**
     * Get the repeat rule for this expense.
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repeat(): BelongsTo {
        return $this->belongsTo(RepeatRule::class, 'repeat_id');
    }

    /**
     * Override the delete function to delete uploaded images.
     */
    public static function boot() {
        parent::boot();

        self::deleting(function ($expense) {
            if ($expense->hasImage())
                Storage::disk('gcs')->delete($expense->image);
        });
    }
}
