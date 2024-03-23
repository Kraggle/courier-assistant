<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Depot extends Model {

    protected $fillable = [
        'location',
        'identifier',
    ];

    /** 
     * Get the users associated with the depot.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routes(): HasMany {
        return $this->hasMany(Route::class);
    }

    /** 
     * Get the rates associated with the depot.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rates(): HasMany {
        return $this->hasMany(Rate::class);
    }
}
