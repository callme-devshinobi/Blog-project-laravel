<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password','display_name'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // A user has many posts
public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}

// $user->author_name  =>  display name if they set one, otherwise their name
protected function authorName(): Attribute
{
    return Attribute::make(
        get: fn () => $this->display_name ?: $this->name,
    );
}

// $user->initials  =>  "Dennis Onwuegbu" becomes "DO"
protected function initials(): Attribute
{
    return Attribute::make(
        get: fn () => Str::of($this->display_name ?: $this->name)
            ->explode(' ')
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->take(2)
            ->implode(''),
    );
}

}
