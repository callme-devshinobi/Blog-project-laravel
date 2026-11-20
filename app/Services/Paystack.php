<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Paystack
{
    public function configured(): bool
    {
        return filled(config('services.paystack.secret')) && filled(config('services.paystack.plan_code'));
    }

    // ------------------------------------------------------------------
    // Talking to Paystack
    // ------------------------------------------------------------------

    private function http(): PendingRequest
    {
        return Http::baseUrl('https://api.paystack.co')
            ->withToken((string) config('services.paystack.secret'))
            ->acceptJson()
            ->timeout(20);
    }

    /** Call Paystack and return its "data" part, or throw a clean exception. */
    private function call(string $method, string $path, array $payload = []): array
    {
        $response = $method === 'get'
            ? $this->http()->get($path)
            : $this->http()->post($path, $payload);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException('Paystack said: ' . ($response->json('message') ?: 'HTTP ' . $response->status()));
        }

        return (array) $response->json('data', []);
    }

    /** Start a checkout for the Pro plan. Returns the page to send the customer to. */
    public function initialize(User $user, string $callbackUrl): array
    {
        $data = $this->call('post', '/transaction/initialize', [
            'email'        => $user->email,
            'amount'       => ((int) config('devnotes.billing.price_ngn')) * 100,   // kobo; the plan's price is what is charged
            'plan'         => config('services.paystack.plan_code'),
            'callback_url' => $callbackUrl,
            'metadata'     => ['user_id' => $user->id, 'purpose' => 'pro_subscription'],
        ]);

        return [
            'authorization_url' => $data['authorization_url'],
            'reference'         => $data['reference'],
        ];
    }

    public function verify(string $reference): array
    {
        return $this->call('get', '/transaction/verify/' . rawurlencode($reference));
    }

    public function fetchSubscription(string $code): array
    {
        return $this->call('get', '/subscription/' . rawurlencode($code));
    }

    /** A Paystack page where the customer can update their card or bank. */
    public function manageLink(string $code): string
    {
        $data = $this->call('get', '/subscription/' . rawurlencode($code) . '/manage/link');

        return (string) ($data['link'] ?? throw new RuntimeException('Paystack sent no link'));
    }

    private function tokenFor(Subscription $sub): string
    {
        if (! $sub->provider_token) {
            $remote = $this->fetchSubscription($sub->provider_subscription_id);
            $sub->forceFill(['provider_token' => $remote['email_token'] ?? null])->save();
        }

        return $sub->provider_token ?: throw new RuntimeException('Paystack sent no subscription token');
    }

    /** Stop renewing: the customer keeps Pro until the end of the period they paid for. */
    public function disable(Subscription $sub): void
    {
        $this->call('post', '/subscription/disable', [
            'code'  => $sub->provider_subscription_id,
            'token' => $this->tokenFor($sub),
        ]);
    }

    public function enable(Subscription $sub): void
    {
        $this->call('post', '/subscription/enable', [
            'code'  => $sub->provider_subscription_id,
            'token' => $this->tokenFor($sub),
        ]);
    }

    /** Used before an account is deleted, so a card is never charged for a deleted account. */
    public function cancelAllFor(User $user): void
    {
        $subs = $user->subscriptions()
            ->where('provider', 'paystack')
            ->whereNotNull('provider_subscription_id')
            ->whereIn('status', ['active', 'past_due'])
            ->get();

        foreach ($subs as $sub) {
            $this->disable($sub);
        }
    }

    // ------------------------------------------------------------------
    // Keeping our records in step with Paystack
    // ------------------------------------------------------------------

    /** Pull the latest state of a subscription from Paystack and save it here. */
    public function sync(Subscription $local): Subscription
    {
        return $this->applyRemote($local, $this->fetchSubscription($local->provider_subscription_id));
    }

    public function applyRemote(Subscription $local, array $data): Subscription
    {
        $remote = $data['status'] ?? null;

        $status = match ($remote) {
            'active'                 => 'active',
            'attention'              => 'past_due',
            'non-renewing'           => 'canceled',
            'cancelled', 'completed' => 'expired',
            default                  => $local->status ?? 'active',
        };

        // The paid-until date only moves forward when the customer has actually paid.
        // After a failed payment we keep the old date, so nobody gets a free month.
        $end = $local->current_period_end;
        if (in_array($status, ['active', 'canceled'], true) && ! empty($data['next_payment_date'])) {
            $end = Carbon::parse($data['next_payment_date']);
        }

        $ending = in_array($status, ['canceled', 'expired'], true);

        $local->forceFill([
            'status'               => $status,
            'current_period_end'   => $end,
            'cancel_at_period_end' => $ending,
            'canceled_at'          => $ending ? ($local->canceled_at ?? now()) : null,
            'provider_token'       => $data['email_token'] ?? $local->provider_token,
            'provider_customer_id' => $data['customer']['customer_code'] ?? $local->provider_customer_id,
        ])->save();

        return $local;
    }

    /** Handle one webhook event. Safe to run twice for the same event. */
    public function handleEvent(string $event, array $data): void
    {
        match ($event) {
            'charge.success'      => $this->processCharge($data),
            'subscription.create' => $this->onSubscriptionCreated($data),
            'invoice.payment_failed',
            'invoice.update',
            'subscription.not_renew',
            'subscription.disable' => $this->onSubscriptionChanged($event, $data),
            default               => null,
        };
    }

    /** A payment went through (the first one, or a renewal). */
    public function processCharge(array $data, ?User $knownUser = null): void
    {
        $reference    = $data['reference'] ?? null;
        $customerCode = $data['customer']['customer_code'] ?? null;

        if (! $reference || ($data['status'] ?? 'success') !== 'success') {
            return;
        }

        // Ignore payments that belong to other things sold through the same Paystack account
        $ours = ($data['metadata']['purpose'] ?? null) === 'pro_subscription'
            || Payment::where('provider', 'paystack')->where('reference', $reference)->exists()
            || ($customerCode && Subscription::where('provider', 'paystack')->where('provider_customer_id', $customerCode)->exists());

        if (! $ours) {
            return;
        }

        $user = $knownUser ?? $this->resolveUser($data);

        $payment   = Payment::firstOrNew(['provider' => 'paystack', 'reference' => $reference]);
        $firstTime = $payment->status !== 'success';

        $payment->fill([
            'user_id'  => $payment->user_id ?? $user?->id,
            'amount'   => (int) ($data['amount'] ?? $payment->amount ?? 0),
            'currency' => $data['currency'] ?? ($payment->currency ?: 'NGN'),
            'status'   => 'success',
            'paid_at'  => ! empty($data['paid_at']) ? Carbon::parse($data['paid_at']) : ($payment->paid_at ?? now()),
        ])->save();

        // The first payment of a new subscription: switch Pro on straight away
        if ($user && ($data['metadata']['purpose'] ?? null) === 'pro_subscription') {
            $this->ensureProvisional($user, $customerCode);
        }

        // Renewals (and the first charge, once the subscription exists): refresh from Paystack
        if ($customerCode) {
            $sub = Subscription::where('provider', 'paystack')
                ->where('provider_customer_id', $customerCode)
                ->whereNotNull('provider_subscription_id')
                ->latest('id')->first();

            if ($sub) {
                $payment->forceFill(['subscription_id' => $sub->id])->save();

                try {
                    $this->sync($sub);
                } catch (\Throwable $e) {
                    logger()->warning('Paystack sync after payment failed', ['error' => class_basename($e)]);
                }
            }
        }

        if ($firstTime) {
            ActivityLog::record(
                'subscription.payment',
                'Payment of ' . $payment->formatted_amount . ' received' . ($user ? ' from ' . $user->name : ''),
                $payment
            );
        }
    }

    /** Give Pro right after payment, before Paystack's subscription message has arrived. */
    private function ensureProvisional(User $user, ?string $customerCode): void
    {
        $recent = Subscription::where('user_id', $user->id)
            ->where('provider', 'paystack')
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if ($recent) {
            return;
        }

        Subscription::create([
            'user_id'              => $user->id,
            'plan'                 => 'pro',
            'provider'             => 'paystack',
            'provider_customer_id' => $customerCode,
            'status'               => 'active',
            'current_period_end'   => now()->addMonth(),
            'note'                 => 'Switched on after payment. Waiting for Paystack to confirm the subscription.',
        ]);
    }

    private function onSubscriptionCreated(array $data): void
    {
        $code = $data['subscription_code'] ?? null;
        $plan = $data['plan']['plan_code'] ?? null;

        if (! $code || ($plan && $plan !== config('services.paystack.plan_code'))) {
            return;   // missing data, or a plan that isn't ours
        }

        $sub = Subscription::where('provider', 'paystack')->where('provider_subscription_id', $code)->first();

        if (! $sub) {
            $user = $this->resolveUser($data);

            if (! $user) {
                logger()->warning('Paystack subscription with no matching account', ['subscription' => $code]);
                return;
            }

            // fill in the provisional row if one was created after payment
            $sub = Subscription::where('user_id', $user->id)
                    ->where('provider', 'paystack')
                    ->whereNull('provider_subscription_id')
                    ->latest('id')->first()
                ?? new Subscription(['user_id' => $user->id, 'plan' => 'pro', 'provider' => 'paystack']);

            $sub->provider_subscription_id = $code;
            $sub->note = null;
        }

        $this->applyRemote($sub, $data);

        ActivityLog::record('subscription.started', 'Pro subscription confirmed by Paystack for ' . $sub->user?->name, $sub);
    }

    private function onSubscriptionChanged(string $event, array $data): void
    {
        $code = $data['subscription_code'] ?? $data['subscription']['subscription_code'] ?? null;

        if (! $code) {
            return;
        }

        $sub = Subscription::where('provider', 'paystack')->where('provider_subscription_id', $code)->first();

        if (! $sub) {
            return;
        }

        $before = $sub->status;
        $this->sync($sub);   // Paystack's own answer decides

        if ($event === 'invoice.payment_failed') {
            ActivityLog::record('subscription.payment_failed', 'A renewal payment failed for ' . $sub->user?->name, $sub);
        } elseif ($sub->status !== $before) {
            ActivityLog::record('subscription.updated', 'Subscription for ' . $sub->user?->name . ' changed from ' . $before . ' to ' . $sub->status, $sub);
        }
    }

    /** Work out which account a Paystack message is about. */
    private function resolveUser(array $data): ?User
    {
        if (! empty($data['metadata']['user_id']) && $user = User::find($data['metadata']['user_id'])) {
            return $user;
        }

        if (! empty($data['reference'])
            && $payment = Payment::where('provider', 'paystack')->where('reference', $data['reference'])->first()) {
            if ($payment->user) {
                return $payment->user;
            }
        }

        $customerCode = $data['customer']['customer_code'] ?? null;
        if ($customerCode && $sub = Subscription::where('provider', 'paystack')->where('provider_customer_id', $customerCode)->latest('id')->first()) {
            if ($sub->user) {
                return $sub->user;
            }
        }

        $email = $data['customer']['email'] ?? null;

        return $email ? User::whereRaw('lower(email) = ?', [strtolower($email)])->first() : null;
    }

        /** Create a monthly plan at Paystack and return its details (including plan_code). */
        public function createPlan(string $name, int $amountNgn): array
        {
            return $this->call('post', '/plan', [
                'name'     => $name,
                'interval' => 'monthly',
                'amount'   => $amountNgn * 100,   // kobo
            ]);
        }
    
        /** Check the saved key and plan without changing anything. The result never includes the key. */
        public function checkSetup(): array
        {
            $key  = (string) config('services.paystack.secret');
            $code = (string) config('services.paystack.plan_code');
    
            $result = [
                'key_set'    => $key !== '',
                'mode'       => str_starts_with($key, 'sk_live_') ? 'live' : (str_starts_with($key, 'sk_test_') ? 'test' : 'unknown'),
                'key_ok'     => false,
                'plan'       => null,
                'plan_error' => null,
                'error'      => null,
            ];
    
            if ($key === '') {
                return $result;
            }
    
            try {
                $this->call('get', '/plan?perPage=1');   // proves the key works
                $result['key_ok'] = true;
            } catch (\Throwable $e) {
                $result['error'] = $e->getMessage();
    
                return $result;
            }
    
            if ($code !== '') {
                try {
                    $result['plan'] = $this->call('get', '/plan/' . rawurlencode($code));
                } catch (\Throwable $e) {
                    $result['plan_error'] = $e->getMessage();
                }
            }
    
            return $result;
        }
}