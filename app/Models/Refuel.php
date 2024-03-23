<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refuel extends Model {

    /**
     * All of the fillable attributes.
     * 
     * @var array
     */
    protected $fillable = [
        'vehicle_id',
        'mileage',
        'miles',
        'date',
        'cost',
        'fuel_rate',
        'first',
        'image',
    ];

    /**
     * All of the attributes to be cast.
     * 
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'first' => 'boolean'
    ];

    /**
     * All of the relationships to be touched.
     * 
     * @var array
     */
    protected $touches = [
        'vehicle'
    ];

    /**
     * Get the vehicle associated with the refuel.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Check if the expense ahs an image.
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
        return $this->hasImage() ? Storage::url($this->image) : null;
    }

    /**
     * Override the delete function to delete uploaded images.
     */
    public static function boot() {
        parent::boot();

        self::deleting(function ($refuel) {
            if ($refuel->hasImage())
                Storage::disk('gcs')->delete($refuel->image);
        });
    }
}
