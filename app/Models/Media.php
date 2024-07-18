<?php

namespace App\Models;

use Illuminate\Support\Facades\Vite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model {
    use HasFactory;

    protected $fillable = [
        'path',
        'caption'
    ];

    protected $appends = [
        'url'
    ];

    /**
     * Check if the media exists.
     * 
     * @return bool
     */
    public function exists() {
        return Storage::disk('gcs')->exists($this->path);
    }

    /**
     * Get the url for the image.
     */
    protected function url(): Attribute {
        return new Attribute(
            get: fn () => $this->exists() ? Storage::url($this->path) : Vite::asset('resources/images/no-image.svg'),
        );
    }
}
