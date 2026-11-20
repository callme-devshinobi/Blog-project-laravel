<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Services\Paystack;
use App\Support\PlanSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $secret = (string) config('services.paystack.secret');

        return view('admin.plan.index', [
            'mode'              => str_starts_with($secret, 'sk_live_') ? 'live' : (str_starts_with($secret, 'sk_test_') ? 'test' : null),
            'secretHint'        => PlanSettings::get('paystack_secret_hint')
                                    ?: ($secret !== '' ? substr($secret, 0, 8) . '••••' . substr($secret, -4) : null),
            'secretFromPanel'   => PlanSettings::has('paystack_secret'),
            'secretUnreadable'  => PlanSettings::$secretUnreadable,
            'planCodeFromPanel' => PlanSettings::has('paystack_plan_code'),
            'webhookUrl'        => route('webhooks.paystack'),
            'payingNow'         => Subscription::where('provider', 'paystack')->where('status', 'active')->count(),
        ]);
    }

    /** Name, description, features, price, grace period and the open/closed switch. */
    public function updateDetails(Request $request)
    {
        $data = $request->validate([
            'plan_name'     => ['required', 'string', 'min:2', 'max:30'],
            'plan_tagline'  => ['nullable', 'string', 'max:160'],
            'plan_features' => ['nullable', 'string', 'max:1500'],
            'price_ngn'     => ['required', 'integer', 'min:100', 'max:10000000'],
            'grace_days'    => ['required', 'integer', 'min:0', 'max:14'],
            'checkout_open' => ['required', 'in:0,1'],
        ]);

        $features = collect(preg_split('/\R/', (string) ($data['plan_features'] ?? '')))
            ->map(fn ($line) => Str::limit(trim($line), 120, ''))
            ->filter()
            ->take(8)
            ->values()
            ->all();

        $oldPrice    = (int) config('devnotes.billing.price_ngn');
        $priceChange = $oldPrice !== (int) $data['price_ngn'];

        PlanSettings::set([
            'plan_name'     => trim($data['plan_name']),
            'plan_tagline'  => trim((string) ($data['plan_tagline'] ?? '')),
            'plan_features' => json_encode($features),
            'price_ngn'     => (string) $data['price_ngn'],
            'grace_days'    => (string) $data['grace_days'],
            'checkout_open' => $data['checkout_open'],
        ]);

        ActivityLog::record(
            'plan.updated',
            'An admin edited the plan details' . ($priceChange ? ' (price ₦' . number_format($oldPrice) . ' to ₦' . number_format($data['price_ngn']) . ')' : '')
        );

        $redirect = back()->with('success', 'Plan details saved.');

        if ($priceChange && filled(config('services.paystack.plan_code'))) {
            $redirect->with('warning', 'A Paystack plan has a fixed price, so the plan you already have still charges ₦' . number_format($oldPrice)
                . '. To charge the new price, click "Create a new plan at Paystack" below. Existing subscribers stay on their old plan and price.');
        }

        return $redirect;
    }

    /** The secret key and plan code. Needs the admin's password. */
    public function updatePayments(Request $request)
    {
        $data = $request->validate([
            'paystack_secret'    => ['nullable', 'string', 'max:200', 'regex:/^sk_(test|live)_[A-Za-z0-9]+$/'],
            'paystack_plan_code' => ['nullable', 'string', 'max:60', 'regex:/^PLN_[A-Za-z0-9]+$/'],
            'current_password'   => ['required', 'current_password'],
        ], [
            'paystack_secret.regex'             => 'That doesn\'t look like a Paystack secret key. It should start with sk_test_ or sk_live_.',
            'paystack_plan_code.regex'          => 'A plan code looks like PLN_xxxxxxxxxxxx.',
            'current_password.current_password' => 'Your password is wrong.',
        ]);

        $changed = [];

        if (filled($data['paystack_secret'] ?? null)) {
            $key = $data['paystack_secret'];

            PlanSettings::set([
                'paystack_secret'      => PlanSettings::encryptSecret($key),
                'paystack_secret_hint' => substr($key, 0, 8) . '••••' . substr($key, -4),
            ]);

            $changed[] = 'secret key (' . (str_starts_with($key, 'sk_live_') ? 'live' : 'test') . ')';
        }

        if (filled($data['paystack_plan_code'] ?? null)) {
            PlanSettings::set(['paystack_plan_code' => $data['paystack_plan_code']]);
            $changed[] = 'plan code';
        }

        if (! $changed) {
            return back()->withErrors(['paystack_secret' => 'Enter a new secret key or plan code to save.']);
        }

        ActivityLog::record('plan.payments_updated', 'An admin changed the Paystack ' . implode(' and ', $changed));

        $redirect = back()->with('success', 'Saved. Click "Check connection" to make sure the key and plan belong together.');

        if (Subscription::where('provider', 'paystack')->where('status', 'active')->exists()) {
            $redirect->with('warning', 'You have active subscriptions. They belong to the Paystack account of the old key, so their renewals and webhooks will be rejected while this key is in use.');
        }

        return $redirect;
    }

    /** Forget the keys saved here and go back to the values in .env. */
    public function clearPayments(Request $request)
    {
        $request->validate(
            ['current_password' => ['required', 'current_password']],
            ['current_password.current_password' => 'Your password is wrong.']
        );

        PlanSettings::forget(['paystack_secret', 'paystack_secret_hint', 'paystack_plan_code']);

        ActivityLog::record('plan.payments_cleared', 'An admin removed the Paystack settings saved in the admin panel');

        return back()->with('success', 'Removed. The values in your .env file are used again.');
    }

    public function check(Paystack $paystack)
    {
        return back()->with('check', $paystack->checkSetup());
    }

    /** Create the monthly plan at Paystack with the current price, using the saved key. */
    public function createPaystackPlan(Request $request, Paystack $paystack)
    {
        $request->validate(
            ['current_password' => ['required', 'current_password']],
            ['current_password.current_password' => 'Your password is wrong.']
        );

        if (blank(config('services.paystack.secret'))) {
            return back()->withErrors(['paystack_secret' => 'Save a Paystack secret key first.']);
        }

        $name  = config('devnotes.billing.plan_name') . ' (monthly)';
        $price = (int) config('devnotes.billing.price_ngn');

        try {
            $plan = $paystack->createPlan($name, $price);
        } catch (\Throwable $e) {
            logger()->warning('Admin could not create a Paystack plan', ['message' => $e->getMessage()]);

            return back()->withErrors(['paystack_secret' => 'Paystack did not create the plan. ' . $e->getMessage()]);
        }

        PlanSettings::set(['paystack_plan_code' => $plan['plan_code']]);

        ActivityLog::record('plan.created', 'An admin created a Paystack plan at ₦' . number_format($price) . ' per month (' . $plan['plan_code'] . ')');

        return back()->with('success', 'Plan created at Paystack and saved (' . $plan['plan_code'] . '). New subscribers will pay ₦' . number_format($price) . ' a month.');
    }
}