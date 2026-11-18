<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'provider', 'provider_subscription_id', 'provider_customer_id',
        'provider_token', 'status', 'current_period_end', 'cancel_at_period_end',
        'canceled_at', 'note',
    ];

    protected $casts = [
        'current_period_end'   => 'datetime',
        'canceled_at'          => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'provider_token'       => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The moment access stops (after the grace period), or null if it never does. */
    public function accessUntil(): ?Carbon
    {
        if (! $this->current_period_end) {
            return null;
        }

        // a cancelled subscription simply runs to the end of the period that was paid for
        $grace = $this->status === 'canceled' ? 0 : (int) config('devnotes.billing.grace_days', 3);

        return $this->current_period_end->copy()->addDays($grace);
    }

    public function grantsAccess(): bool
    {
        if ($this->status === 'expired') {
            return false;
        }

        $until = $this->accessUntil();

        return $until ? $until->isFuture() : $this->status === 'active';
    }
}