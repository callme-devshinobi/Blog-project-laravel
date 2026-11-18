<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark subscriptions whose paid period and grace period have passed as expired';

    public function handle()
    {
        Subscription::with('user')
            ->whereIn('status', ['active', 'past_due', 'canceled'])
            ->whereNotNull('current_period_end')
            ->each(function (Subscription $s) {
                if (! $s->grantsAccess()) {
                    $s->forceFill(['status' => 'expired'])->save();
                    ActivityLog::record('subscription.expired', 'Pro ended for ' . $s->user?->name, $s);
                }
            });

        return self::SUCCESS;
    }
}