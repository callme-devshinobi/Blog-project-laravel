<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
    'slug',
    'title',
    'excerpt',
    'body',
    'image',
    'image_alt',
    'category',
    'tags',
    'author',
    'author_initials',
    'read_time',
    'published_at'
];

// Turn the JSON text in the database into a PHP array (and back when saving)
protected $casts = [
    'tags' => 'array',
    'published_at' => 'date',
];
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory;
}
