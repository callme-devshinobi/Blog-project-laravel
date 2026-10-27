<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'image_alt',
        'tags',
        'read_time',
        'status',
        'published_at',
    ];

    // Turn the JSON text in the database into a PHP array, and the timestamp into a date object
    protected $casts = [
        'tags'         => 'array',
        'published_at' => 'datetime',
    ];
    // $post->image_url  =>  the featured image, or a placeholder if there isn't one
  // $post->image_url  =>  a full web address for the featured image
protected function imageUrl(): Attribute
{
    return Attribute::make(
        get: function () {
            if (! $this->featured_image) {
                return 'https://picsum.photos/seed/' . $this->id . '/800/450';   // placeholder
            }

            // The seeded posts store a full web address already
            if (Str::startsWith($this->featured_image, ['http://', 'https://'])) {
                return $this->featured_image;
            }

            // Uploaded files live in storage/app/public
            return asset('storage/' . $this->featured_image);
        },
    );
}
    // A post belongs to one category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // A post belongs to one user (the author)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // Lets you write Post::published() to get only live posts
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }
}