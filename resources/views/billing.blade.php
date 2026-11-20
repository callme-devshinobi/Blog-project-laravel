<x-dashboard-layout title="Billing">

  @php
    $planName    = config('devnotes.billing.plan_name', 'Pro');
    $checkoutOpen = config('devnotes.billing.checkout_open', true);
    $sub         = $subscription;
    $paystackSub = $sub && $sub->provider === 'paystack';
    $confirmed   = $paystackSub && $sub->provider_subscription_id;
    $ending      = $sub && ($sub->cancel_at_period_end || $sub->status === 'canceled');
    $pastDue     = $sub && $sub->status === 'past_due';
    $showSubscribe = ! $isPro || ($sub && $sub->provider === 'manual');
  @endphp

  @if (session('success'))
    <div class="adm-alert adm-ok">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="adm-alert adm-err" role="alert">
      <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  @if ($pastDue)
    <div class="adm-banner" role="alert">
      <b>Your last payment didn't go through.</b> Please update your card or bank so {{ $planName }} isn't switched off.
    </div>
  @endif

  <div class="db-grid">

    <div>
      <section class="db-panel">
        <div class="db-panel-head"><h3><span class="db-p-icon">⭐</span> Your plan</h3></div>

        @if ($sub)
          <div class="adm-kv"><span>Plan</span><b>{{ $planName }}</b></div>
          <div class="adm-kv">
            <span>Status</span>
            <b>
              @if ($pastDue) Payment problem
              @elseif ($ending) Ending
              @else Active
              @endif
            </b>
          </div>
          @if ($sub->current_period_end)
            <div class="adm-kv"><span>{{ $ending ? $planName . ' ends' : 'Next payment' }}</span><b>{{ $sub->current_period_end->format('M j, Y') }}</b></div>
          @endif
          <div class="adm-kv"><span>Billed by</span><b>{{ $sub->provider === 'manual' ? 'Granted by DevNotes' : ucfirst($sub->provider) }}</b></div>
          @if ($paystackSub && ! $confirmed)
            <div class="adm-kv"><span>Note</span><b>Waiting for Paystack to confirm. Refresh in a moment.</b></div>
          @endif
        @elseif ($isPro)
          <p class="db-empty">This account has {{ $planName }} access as an administrator.</p>
        @else
          <div class="adm-actions">
            <p><b>{{ $planName }}</b> · {{ $price }}</p>
            @if (config('devnotes.billing.tagline'))
              <p>{{ config('devnotes.billing.tagline') }}</p>
            @endif
            <p>@foreach (config('devnotes.billing.features', []) as $feature)✓ {{ $feature }}<br>@endforeach</p>
          </div>
        @endif
      </section>

      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">🧾</span> Payments</h3></div>
        @forelse ($payments as $p)
          <div class="adm-kv">
            <span>{{ $p->paid_at?->format('M j, Y') ?? $p->created_at->format('M j, Y') }}</span>
            <b>{{ $p->formatted_amount }}</b>
          </div>
        @empty
          <p class="db-empty">No payments yet.</p>
        @endforelse
      </section>
    </div>

    <aside class="db-right">
      <section class="db-panel">
        <div class="db-panel-head"><h3><span class="db-p-icon">⚙️</span> Billing</h3></div>

        <div class="adm-actions">

          @if ($showSubscribe)
            @if ($paystackReady && $checkoutOpen)
              <form method="POST" action="{{ route('billing.paystack.checkout') }}">
                @csrf
                <button type="submit" class="adm-btn adm-btn-primary">Subscribe · {{ $price }}</button>
              </form>
              <p>Pay with a card or by bank direct debit. It renews monthly until you cancel, and you can cancel any time.</p>
            @else
              <p>{{ $paystackReady ? 'New subscriptions are closed right now.' : 'Online payments aren\'t switched on yet.' }}</p>
            @endif
          @endif

          @if ($confirmed && ! $ending)
            <form method="POST" action="{{ route('billing.paystack.card') }}">
              @csrf
              <button type="submit" class="adm-btn">💳 Update card or bank</button>
            </form>

            <form method="POST" action="{{ route('billing.cancel') }}"
                  onsubmit="return confirm('Cancel your subscription? You keep {{ $planName }} until the end of the period you paid for.')">
              @csrf
              <button type="submit" class="adm-btn adm-btn-danger">Cancel subscription</button>
            </form>
          @endif

          @if ($confirmed && $ending && $sub->current_period_end?->isFuture())
            <form method="POST" action="{{ route('billing.resume') }}">
              @csrf
              <button type="submit" class="adm-btn adm-btn-primary">↩ Resume subscription</button>
            </form>
            <p>Your subscription won't renew, but {{ $planName }} stays on until the date shown.</p>
          @endif

        </div>
      </section>
    </aside>

  </div>
</x-dashboard-layout>