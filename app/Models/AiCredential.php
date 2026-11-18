<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiCredential extends Model
{
    protected $fillable = ['user_id', 'provider', 'api_key', 'last_four', 'last_checked_at', 'last_check_ok'];

    // the key can never leak through toArray() / JSON
    protected $hidden = ['api_key'];

    protected $casts = [
        'api_key'         => 'encrypted',
        'last_checked_at' => 'datetime',
        'last_check_ok'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}