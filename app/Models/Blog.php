<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['user_id', 'subdomain', 'title','contact_email', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getPublicBaseAttribute(): string
{
    if ($this->custom_domain && $this->custom_domain_verified_at && ! $this->suspended_at && $this->user?->hasPro()) {
        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'https';

        return $scheme . '://' . $this->custom_domain;
    }

    return rtrim(route('blog.show', $this), '/');
}

    protected $casts = [
        'tech_stack' => 'array',
        'suspended_at' => 'datetime',
    ];
}