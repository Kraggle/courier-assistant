<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model {
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /** 
     * Get the posts that have this tag.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts(): BelongsToMany {
        return $this->belongsToMany(Post::class);
    }
}
