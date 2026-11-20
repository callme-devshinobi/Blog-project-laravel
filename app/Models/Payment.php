<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'subscription_id', 'provider', 'reference',
        'amount', 'currency', 'status', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbols = ['NGN' => '₦', 'USD' => '$'];

        return ($symbols[$this->currency] ?? $this->currency . ' ') . number_format($this->amount / 100, 2);
    }
}