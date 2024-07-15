<?php

namespace App\Models;

use App\Helpers\K;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'slug',
        'user_id',
        'title',
        'type',
        'banner',
        'content',
        'is_live',
    ];

    /**
     * Attributes to be appended to the model.
     * 
     * @var array
     */
    protected $appends = [
        'thumbs_up',
        'thumbs_down',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'vat' => 'is_live',
    ];

    /**
     * Get the user that created the post.
     * 
     * @return BelongsTo
     */
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviews for the post.
     * 
     * @return HasMany
     */
    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the tags for the post.
     * 
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the categories for the post.
     * 
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the total thumbs up for the post.
     */
    protected function thumbsUp(): Attribute {
        return new Attribute(
            get: fn () => $this->reviews()->where('thumbs_up', 1)->count(),
        );
    }

    /**
     * Get the total thumbs down for the post.
     */
    protected function thumbsDown(): Attribute {
        return new Attribute(
            get: fn () => $this->reviews()->where('thumbs_up', 0)->count(),
        );
    }

    /**
     * Check if the post has a specific tag.
     * 
     * @param string|Tag $tag
     * @return bool
     */
    public function hasTag(string|Tag $tag): bool {
        $tag = $tag instanceof Tag ? $tag->name : $tag;
        return $this->tags()->where('name', $tag)->first() != null;
    }

    /**
     * Check if the post has a specific category.
     * 
     * @param string|Category $category
     * @return bool
     */
    public function hasCategory(string|Category $category): bool {
        $category = $category instanceof Category ? $category->name : $category;
        return $this->categories->where('name', $category)->first() != null;
    }
}
