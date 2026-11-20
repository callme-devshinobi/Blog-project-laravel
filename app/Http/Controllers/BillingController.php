<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Services\Paystack;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function show(Request $request, Paystack $paystack)
    {
        $user = $request->user();

        return view('billing', [
            'isPro'         => $user->hasPro(),
            'subscription'  => $user->activeSubscription(),
            'payments'      => Payment::where('user_id', $user->id)->where('status', 'success')->latest('id')->take(10)->get(),
            'paystackReady' => $paystack->configured(),
            'price'         => config('devnotes.billing.price_label'),
        ]);
    }

    public function paystackCheckout(Request $request, Paystack $paystack)
    {
        $user = $request->user();

        if (! $paystack->configured()) {
            return back()->withErrors(['billing' => 'Online payments are not switched on yet.']);
        }

        if (! config('devnotes.billing.checkout_open', true)) {
            return back()->withErrors(['billing' => 'New subscriptions are closed right now.']);
        }

        $current = $user->activeSubscription();

        if ($current && $current->provider === 'paystack') {
            return back()->withErrors(['billing' => $current->status === 'canceled'
                ? 'Your Pro plan is still running. Use "Resume subscription" to keep it.'
                : 'You already have a Pro subscription.']);
        }

        try {
            $checkout = $paystack->initialize($user, route('billing.paystack.callback'));
        } catch (\Throwable $e) {
            logger()->warning('Paystack checkout failed to start', ['user' => $user->id, 'message' => $e->getMessage()]);

            return back()->withErrors(['billing' => 'We couldn\'t start the payment. Please try again in a moment.']);
        }

        Payment::create([
            'user_id'   => $user->id,
            'provider'  => 'paystack',
            'reference' => $checkout['reference'],
            'amount'    => ((int) config('devnotes.billing.price_ngn')) * 100,
            'currency'  => 'NGN',
            'status'    => 'pending',
        ]);

        return redirect()->away($checkout['authorization_url']);
    }

    /** Where Paystack sends the customer back to after paying. */
    public function paystackCallback(Request $request, Paystack $paystack)
    {
        $user      = $request->user();
        $reference = (string) ($request->query('reference') ?: $request->query('trxref'));

        // only payments this person started can be confirmed by this person
        $payment = Payment::where('provider', 'paystack')
            ->where('reference', $reference)
            ->where('user_id', $user->id)
            ->first();

        if (! $payment) {
            return redirect()->route('billing')->withErrors(['billing' => 'We couldn\'t find that payment.']);
        }

        try {
            $transaction = $paystack->verify($reference);
        } catch (\Throwable $e) {
            logger()->warning('Paystack verify failed', ['message' => $e->getMessage()]);

            return redirect()->route('billing')->withErrors([
                'billing' => 'We couldn\'t confirm your payment yet. If you were charged, Pro will switch on shortly.',
            ]);
        }

        if (($transaction['status'] ?? null) !== 'success') {
            if (in_array($transaction['status'] ?? null, ['failed', 'abandoned'], true)) {
                $payment->forceFill(['status' => 'failed'])->save();
            }

            return redirect()->route('billing')->withErrors(['billing' => 'The payment wasn\'t completed, so you haven\'t been charged.']);
        }

        $paystack->processCharge($transaction, $user);

        return redirect()->route('billing')->with('success', 'Payment received. Welcome to Pro!');
    }

    /** Send the customer to Paystack's page for updating their card or bank. */
    public function paystackCard(Request $request, Paystack $paystack)
    {
        $sub = $this->paystackSubscription($request);

        abort_unless($sub && $sub->provider_subscription_id, 404);

        try {
            return redirect()->away($paystack->manageLink($sub->provider_subscription_id));
        } catch (\Throwable $e) {
            logger()->warning('Paystack manage link failed', ['message' => $e->getMessage()]);

            return back()->withErrors(['billing' => 'We couldn\'t open the payment page. Please try again in a moment.']);
        }
    }

    public function cancel(Request $request, Paystack $paystack)
    {
        $sub = $this->paystackSubscription($request);

        if (! $sub || ! $sub->provider_subscription_id || ! in_array($sub->status, ['active', 'past_due'], true)) {
            return back()->withErrors(['billing' => 'There is no subscription to cancel.']);
        }

        try {
            $paystack->disable($sub);
        } catch (\Throwable $e) {
            logger()->warning('Paystack cancel failed', ['message' => $e->getMessage()]);

            return back()->withErrors(['billing' => 'We couldn\'t cancel your subscription. Please try again in a moment.']);
        }

        $sub->forceFill(['status' => 'canceled', 'cancel_at_period_end' => true, 'canceled_at' => now()])->save();

        ActivityLog::record('subscription.canceled', $request->user()->name . ' cancelled their subscription', $sub);

        return back()->with('success', 'Your subscription won\'t renew. You keep Pro until ' . $sub->current_period_end?->format('M j, Y') . '.');
    }

    public function resume(Request $request, Paystack $paystack)
    {
        $sub = $this->paystackSubscription($request);

        if (! $sub || ! $sub->provider_subscription_id || $sub->status !== 'canceled' || ! $sub->current_period_end?->isFuture()) {
            return back()->withErrors(['billing' => 'There is nothing to resume.']);
        }

        try {
            $paystack->enable($sub);
            $paystack->sync($sub);
        } catch (\Throwable $e) {
            logger()->warning('Paystack resume failed', ['message' => $e->getMessage()]);

            return back()->withErrors(['billing' => 'We couldn\'t resume your subscription. Please try again in a moment.']);
        }

        ActivityLog::record('subscription.resumed', $request->user()->name . ' resumed their subscription', $sub);

        return back()->with('success', 'Your subscription will renew as normal.');
    }

    private function paystackSubscription(Request $request)
    {
        return $request->user()->subscriptions()
            ->where('provider', 'paystack')
            ->whereIn('status', ['active', 'past_due', 'canceled'])
            ->latest('id')
            ->first();
    }
}