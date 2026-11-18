<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'admin:make {email : Email of an existing account} {--revoke : Remove admin rights instead}';

    protected $description = 'Give (or remove) admin rights for an existing user';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No account with that email address.');
            return self::FAILURE;
        }

        $revoke = $this->option('revoke');

        $user->forceFill(['is_admin' => ! $revoke])->save();

        ActivityLog::record(
            $revoke ? 'admin.revoked' : 'admin.granted',
            ($revoke ? 'Admin rights removed from ' : 'Admin rights given to ') . $user->name . ' (from the command line)',
            $user
        );

        $this->info($user->name . ($revoke ? ' is no longer an admin.' : ' is now an admin.'));

        return self::SUCCESS;
    }
}