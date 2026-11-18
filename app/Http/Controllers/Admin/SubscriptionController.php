<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Models\User;
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

        $stats = [
            'active'   => Subscription::where('status', 'active')->count(),
            'paying'   => Subscription::where('status', 'active')->whereIn('provider', ['paystack', 'stripe'])->count(),
            'manual'   => Subscription::where('status', 'active')->where('provider', 'manual')->count(),
            'expiring' => Subscription::where('status', 'active')
                              ->whereBetween('current_period_end', [now(), now()->addDays(7)])->count(),
            'ended'    => Subscription::whereIn('status', ['canceled', 'expired'])->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'q', 'status', 'provider'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        return view('admin.subscriptions.show', [
            'subscription' => $subscription,
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

    public function expire(Subscription $subscription)
    {
        $subscription->forceFill([
            'status'             => 'expired',
            'current_period_end' => now(),
            'canceled_at'        => now(),
        ])->save();

        ActivityLog::record('subscription.expired', 'Pro was ended by an admin for ' . $subscription->user?->name, $subscription);

        return back()->with('success', 'Pro access has been ended.');
    }
}