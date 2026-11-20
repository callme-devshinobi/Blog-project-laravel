<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Paystack;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim(str_replace(['%', '_'], '', (string) $request->query('q', '')));
        $status   = $request->query('status');
        $provider = $request->query('provider');

        $subscriptions = Subscription::with('user')
            ->when($q !== '', fn ($query) => $query->whereHas('user', fn ($u) =>
                $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->when(in_array($status, ['active', 'past_due', 'canceled', 'expired'], true),
                fn ($query) => $query->where('status', $status))
            ->when(in_array($provider, ['manual', 'paystack', 'stripe'], true),
                fn ($query) => $query->where('provider', $provider))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $paid = Payment::where('status', 'success')->where('currency', 'NGN');

        $stats = [
            'active'        => Subscription::where('status', 'active')->count(),
            'paying'        => Subscription::where('status', 'active')->whereIn('provider', ['paystack', 'stripe'])->count(),
            'manual'        => Subscription::where('status', 'active')->where('provider', 'manual')->count(),
            'expiring'      => Subscription::where('status', 'active')
                                    ->whereBetween('current_period_end', [now(), now()->addDays(7)])->count(),
            'ended'         => Subscription::whereIn('status', ['canceled', 'expired'])->count(),
            'revenue_month' => (clone $paid)->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
            'revenue_total' => (clone $paid)->sum('amount'),
        ];

        $recentPayments = Payment::with('user')->where('status', 'success')->latest('id')->take(8)->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'recentPayments', 'q', 'status', 'provider'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        return view('admin.subscriptions.show', [
            'subscription' => $subscription,
            'payments'     => Payment::where('user_id', $subscription->user_id)->latest('id')->take(10)->get(),
            'logs'         => ActivityLog::where('subject_type', $subscription->getMorphClass())
                                  ->where('subject_id', $subscription->id)
                                  ->latest('id')->take(10)->get(),
        ]);
    }

    /** Give someone Pro by hand: for testing, partners and support cases. */
    public function grant(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'days'     => ['nullable', 'integer', 'min:1', 'max:3650', 'required_without:lifetime'],
            'lifetime' => ['nullable', 'boolean'],
            'note'     => ['nullable', 'string', 'max:150'],
        ], ['email.exists' => 'No account has that email address.']);

        $user     = User::where('email', $data['email'])->firstOrFail();
        $lifetime = $request->boolean('lifetime');

        $subscription = Subscription::create([
            'user_id'            => $user->id,
            'plan'               => 'pro',
            'provider'           => 'manual',
            'status'             => 'active',
            'current_period_end' => $lifetime ? null : now()->addDays((int) $data['days']),
            'note'               => $data['note'] ?? null,
        ]);

        ActivityLog::record(
            'subscription.granted',
            'Pro granted to ' . $user->name . ($lifetime ? ' with no end date' : ' for ' . $data['days'] . ' days'),
            $subscription
        );

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Pro granted to ' . $user->name . '.');
    }

    public function extend(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:3650'],
        ]);

        if (! $subscription->current_period_end) {
            return back()->withErrors(['action' => 'This subscription has no end date, so there is nothing to extend.']);
        }

        $from = $subscription->current_period_end->isFuture() ? $subscription->current_period_end : now();

        $subscription->forceFill([
            'current_period_end'   => $from->copy()->addDays((int) $data['days']),
            'status'               => 'active',
            'cancel_at_period_end' => false,
            'canceled_at'          => null,
        ])->save();

        ActivityLog::record('subscription.extended', 'Pro extended by ' . $data['days'] . ' days for ' . $subscription->user?->name, $subscription);

        return back()->with('success', 'Extended by ' . $data['days'] . ' days.');
    }

    /** Stop a Paystack subscription renewing. The customer keeps Pro until the paid period ends. */
    public function stopRenewal(Subscription $subscription, Paystack $paystack)
    {
        if ($subscription->provider !== 'paystack' || ! $subscription->provider_subscription_id
            || ! in_array($subscription->status, ['active', 'past_due'], true)) {
            return back()->withErrors(['action' => 'There is no renewing Paystack subscription to stop.']);
        }

        try {
            $paystack->disable($subscription);
        } catch (\Throwable $e) {
            logger()->warning('Admin stop-renewal failed', ['message' => $e->getMessage()]);

            return back()->withErrors(['action' => 'Paystack did not accept the request. Try again, or do it in the Paystack dashboard.']);
        }

        $subscription->forceFill(['status' => 'canceled', 'cancel_at_period_end' => true, 'canceled_at' => now()])->save();

        ActivityLog::record('subscription.canceled', 'An admin stopped renewal for ' . $subscription->user?->name, $subscription);

        return back()->with('success', 'It will not renew. Pro stays on until the paid period ends.');
    }

    /** End Pro now. For Paystack subscriptions this also stops the billing. */
    public function expire(Subscription $subscription, Paystack $paystack)
    {
        $warning = '';

        if ($subscription->provider === 'paystack' && $subscription->provider_subscription_id
            && in_array($subscription->status, ['active', 'past_due'], true)) {
            try {
                $paystack->disable($subscription);
            } catch (\Throwable $e) {
                logger()->warning('Admin expire: could not stop billing', ['message' => $e->getMessage()]);
                $warning = ' But Paystack could not be told to stop billing, so cancel it in the Paystack dashboard too.';
            }
        }

        $subscription->forceFill([
            'status'             => 'expired',
            'current_period_end' => now(),
            'canceled_at'        => now(),
        ])->save();

        ActivityLog::record('subscription.expired', 'Pro was ended by an admin for ' . $subscription->user?->name, $subscription);

        return back()->with('success', 'Pro access has been ended.' . $warning);
    }
}