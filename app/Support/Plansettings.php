<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PlanSettings
{
    /** True when a saved secret key exists but can't be decrypted (the APP_KEY changed). */
    public static bool $secretUnreadable = false;

    private static ?array $loaded = null;

    /** Saved values exactly as stored (the secret is still encrypted). */
    public static function all(): array
    {
        if (static::$loaded !== null) {
            return static::$loaded;
        }

        try {
            return static::$loaded = DB::table('settings')->pluck('value', 'key')->all();
        } catch (\Throwable $e) {
            return static::$loaded = [];   // the table doesn't exist yet
        }
    }

    public static function get(string $key): ?string
    {
        $value = static::all()[$key] ?? null;

        return $value === '' ? null : $value;
    }

    public static function has(string $key): bool
    {
        return static::get($key) !== null;
    }

    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
        }

        static::$loaded = null;
    }

    public static function forget(array $keys): void
    {
        DB::table('settings')->whereIn('key', $keys)->delete();

        static::$loaded = null;
    }

    public static function encryptSecret(string $secret): string
    {
        return Crypt::encryptString($secret);
    }

    /** Copy the saved values into the app's config, so the rest of the code needs no changes. */
    public static function apply(): void
    {
        $s = static::all();

        if (! $s) {
            return;
        }

        if (filled($s['paystack_secret'] ?? null)) {
            try {
                config(['services.paystack.secret' => Crypt::decryptString($s['paystack_secret'])]);
            } catch (\Throwable $e) {
                static::$secretUnreadable = true;
            }
        }

        if (filled($s['paystack_plan_code'] ?? null)) {
            config(['services.paystack.plan_code' => $s['paystack_plan_code']]);
        }

        if (filled($s['price_ngn'] ?? null)) {
            $price = (int) $s['price_ngn'];

            config([
                'devnotes.billing.price_ngn'   => $price,
                'devnotes.billing.price_label' => '₦' . number_format($price) . ' per month',
            ]);
        }

        if (filled($s['plan_name'] ?? null)) {
            config(['devnotes.billing.plan_name' => $s['plan_name']]);
        }

        if (array_key_exists('plan_tagline', $s)) {
            config(['devnotes.billing.tagline' => (string) $s['plan_tagline']]);
        }

        if (isset($s['plan_features'])) {
            $features = json_decode($s['plan_features'], true);

            if (is_array($features)) {
                config(['devnotes.billing.features' => $features]);
            }
        }

        if (isset($s['grace_days']) && $s['grace_days'] !== '') {
            config(['devnotes.billing.grace_days' => (int) $s['grace_days']]);
        }

        if (isset($s['checkout_open'])) {
            config(['devnotes.billing.checkout_open' => $s['checkout_open'] === '1']);
        }
    }
}