<?php

namespace App\Services;

use App\Models\User;
use Laravel\Ai\Ai;

use function Laravel\Ai\agent;

class UserAiProvider
{
    /** The writer's own provider, ready for prompt(). Null if they have no access or no key. */
    public static function for(User $user)
    {
        if (! $user->hasPro()) {
            return null;
        }

        $credential = $user->aiCredential;

        if (! $credential) {
            return null;
        }

        $config = ['driver' => $credential->provider, 'key' => $credential->api_key];

        $model = config("devnotes.ai.providers.{$credential->provider}.model");
        if ($model) {
            $config['models'] = ['text' => ['default' => $model]];
        }

        return Ai::build($config);
    }

    /** A tiny request that checks the key works. It never logs the key or the provider's error text. */
    public static function test(User $user): bool
    {
        $provider = static::for($user);

        if (! $provider) {
            return false;
        }

        try {
            agent(instructions: 'Reply with the single word: ok')
                ->prompt('ping', provider: [$provider], timeout: 30);

            return true;
        } catch (\Throwable $e) {
            // log only the kind of error: some providers put the key in the request address
            logger()->warning('AI key test failed', [
                'provider' => $user->aiCredential?->provider,
                'error'    => class_basename($e),
            ]);

            return false;
        }
    }
}