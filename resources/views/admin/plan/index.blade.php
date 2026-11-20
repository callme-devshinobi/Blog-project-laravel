<x-admin-layout title="Plan & billing">

    @php
      $check = session('check');
      $source = fn ($fromPanel) => $fromPanel ? 'saved in this admin panel' : 'from your .env file';
    @endphp
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if (session('warning'))
      <div class="adm-banner" role="alert">{{ session('warning') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    @if ($mode === 'live' && app()->environment('local'))
      <div class="adm-banner" role="alert">
        <b>A live Paystack key is saved on a local copy of the app.</b> Real cards would be charged here. Use a test key while developing.
      </div>
    @endif
  
    @if ($secretUnreadable)
      <div class="adm-alert adm-err" role="alert">
        The saved secret key can't be read any more (the app's encryption key changed). Enter the secret key again below.
      </div>
    @endif
  
    @if ($check)
      <div class="adm-alert {{ $check['key_ok'] && ! $check['plan_error'] ? 'adm-ok' : 'adm-err' }}">
        @if (! $check['key_set'])
          No secret key is set yet.
        @elseif (! $check['key_ok'])
          The secret key was not accepted: {{ $check['error'] }}
        @else
          <b>Connected to Paystack in {{ $check['mode'] }} mode.</b>
          @if ($check['plan'])
            Plan found: {{ $check['plan']['name'] ?? '' }}, ₦{{ number_format(($check['plan']['amount'] ?? 0) / 100) }} {{ $check['plan']['interval'] ?? '' }}.
            @if (((int) ($check['plan']['amount'] ?? 0)) !== ((int) config('devnotes.billing.price_ngn')) * 100)
              <br><b>The plan's price doesn't match the price above.</b> Customers are charged the plan's price. Create a new plan to fix it.
            @endif
          @elseif ($check['plan_error'])
            <br>But the plan code was not found in this account: {{ $check['plan_error'] }}. Create a new plan below, or paste the right code.
          @else
            No plan code is set yet.
          @endif
        @endif
      </div>
    @endif
  
    <div class="db-grid">
  
      {{-- LEFT: forms --}}
      <div>
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">🏷️</span> Plan details</h3></div>
  
          <form method="POST" action="{{ route('admin.plan.update') }}" class="adm-actions">
            @csrf
            @method('PUT')
  
            <div>
              <label class="adm-label" for="plan_name">Plan name</label>
              <input type="text" id="plan_name" name="plan_name" class="adm-input" maxlength="30" required
                     value="{{ old('plan_name', config('devnotes.billing.plan_name')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="plan_tagline">Short description</label>
              <input type="text" id="plan_tagline" name="plan_tagline" class="adm-input" maxlength="160"
                     value="{{ old('plan_tagline', config('devnotes.billing.tagline')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="plan_features">What's included (one per line, up to 8)</label>
              <textarea id="plan_features" name="plan_features" class="adm-input" rows="5">{{ old('plan_features', implode("\n", config('devnotes.billing.features', []))) }}</textarea>
            </div>
  
            <div>
              <label class="adm-label" for="price_ngn">Monthly price (₦)</label>
              <input type="number" id="price_ngn" name="price_ngn" class="adm-input" min="100" max="10000000" required
                     value="{{ old('price_ngn', config('devnotes.billing.price_ngn')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="grace_days">Days of grace after a failed payment</label>
              <input type="number" id="grace_days" name="grace_days" class="adm-input" min="0" max="14" required
                     value="{{ old('grace_days', config('devnotes.billing.grace_days')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="checkout_open">Accepting new subscriptions</label>
              <select id="checkout_open" name="checkout_open" class="adm-input">
                @php $open = (string) old('checkout_open', config('devnotes.billing.checkout_open') ? '1' : '0'); @endphp
                <option value="1" @selected($open === '1')>Yes</option>
                <option value="0" @selected($open === '0')>No, close checkout for now</option>
              </select>
            </div>
  
            <p>The name is shown on the billing page and in the Settings plan panel. Some short messages elsewhere still say "Pro".</p>
            <button type="submit" class="adm-btn adm-btn-primary">Save plan details</button>
          </form>
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🔐</span> Paystack keys</h3></div>
  
          <form method="POST" action="{{ route('admin.plan.payments') }}" class="adm-actions" autocomplete="off">
            @csrf
            @method('PUT')
  
            <div>
              <label class="adm-label" for="paystack_secret">Secret key</label>
              <input type="password" id="paystack_secret" name="paystack_secret" class="adm-input"
                     placeholder="Paste a key to replace the saved one" autocomplete="new-password">
            </div>
  
            <div>
              <label class="adm-label" for="paystack_plan_code">Plan code</label>
              <input type="text" id="paystack_plan_code" name="paystack_plan_code" class="adm-input"
                     placeholder="PLN_xxxxxxxxxxxx" value="{{ old('paystack_plan_code') }}" autocomplete="off">
            </div>
  
            <div>
              <label class="adm-label" for="current_password">Your admin password</label>
              <input type="password" id="current_password" name="current_password" class="adm-input"
                     placeholder="Needed to change payment settings" required autocomplete="current-password">
            </div>
  
            <p>Leave a field empty to keep what is saved. The secret key is stored encrypted and can never be shown again. Test keys (sk_test_) go with test plans, and live keys (sk_live_) with live plans.</p>
            <button type="submit" class="adm-btn adm-btn-primary">Save payment settings</button>
          </form>
  
          @if ($secretFromPanel || $planCodeFromPanel)
            <form method="POST" action="{{ route('admin.plan.payments.clear') }}" class="adm-actions" style="padding-top:0;"
                  onsubmit="return confirm('Remove the keys saved in this panel and use the .env values instead?')">
              @csrf
              @method('DELETE')
              <div class="adm-danger-box">
                <h4>Remove saved keys</h4>
                <p style="margin-bottom:10px;">Forgets the key and plan code saved here, so the values in your .env file are used again.</p>
                <input type="password" name="current_password" class="adm-input" placeholder="Your admin password" required autocomplete="current-password">
                <button type="submit" class="adm-btn adm-btn-danger">Remove saved keys</button>
              </div>
            </form>
          @endif
        </section>
  
      </div>
  
      {{-- RIGHT: status and tools --}}
      <aside class="db-right">
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">📡</span> Status</h3></div>
  
          <div class="adm-kv">
            <span>Mode</span>
            <b>
              @if ($mode === 'live') <span class="adm-badge adm-badge-susp">Live (real money)</span>
              @elseif ($mode === 'test') <span class="adm-badge adm-badge-live">Test</span>
              @else Not set
              @endif
            </b>
          </div>
          <div class="adm-kv">
            <span>Secret key</span>
            <b>{{ $secretHint ?? 'Not set' }}@if ($secretHint)<br><small style="color:var(--text-muted);font-weight:400;">{{ $source($secretFromPanel) }}</small>@endif</b>
          </div>
          <div class="adm-kv">
            <span>Plan code</span>
            <b>{{ config('services.paystack.plan_code') ?: 'Not set' }}@if (config('services.paystack.plan_code'))<br><small style="color:var(--text-muted);font-weight:400;">{{ $source($planCodeFromPanel) }}</small>@endif</b>
          </div>
          <div class="adm-kv"><span>Price</span><b>{{ config('devnotes.billing.price_label') }}</b></div>
          <div class="adm-kv"><span>New subscriptions</span><b>{{ config('devnotes.billing.checkout_open') ? 'Open' : 'Closed' }}</b></div>
          <div class="adm-kv"><span>Active paid subscribers</span><b>{{ $payingNow }}</b></div>
        </section>
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">🔗</span> Webhook address</h3></div>
          <div class="adm-actions">
            <input type="text" id="webhookUrl" class="adm-input" readonly value="{{ $webhookUrl }}">
            <button type="button" class="adm-btn" id="copyWebhook">Copy</button>
            <p>Paste this in your Paystack dashboard as the webhook URL. It needs to be a public https address.</p>
          </div>
        </section>
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">🛠️</span> Tools</h3></div>
  
          <div class="adm-actions">
            <form method="POST" action="{{ route('admin.plan.check') }}">
              @csrf
              <button type="submit" class="adm-btn">Check connection</button>
            </form>
            <p>Tests the saved key and plan code with Paystack. It changes nothing.</p>
  
            <form method="POST" action="{{ route('admin.plan.create') }}"
                  onsubmit="return confirm('Create a new monthly plan at Paystack for ₦{{ number_format(config('devnotes.billing.price_ngn')) }}?')">
              @csrf
              <div class="adm-danger-box" style="border-color:var(--border);">
                <h4 style="color:var(--text);">Create a new plan at Paystack</h4>
                <p style="margin-bottom:10px;">Creates a monthly plan at the price above, in the same account as the saved key, and saves its code here. New subscribers pay this price. Existing subscribers keep their old plan.</p>
                <input type="password" name="current_password" class="adm-input" placeholder="Your admin password" required autocomplete="current-password">
                <button type="submit" class="adm-btn adm-btn-primary">Create plan</button>
              </div>
            </form>
          </div>
        </section>
  
      </aside>
  
    </div>
  
    <script>
      document.getElementById('copyWebhook').addEventListener('click', function () {
        const btn = this;
        navigator.clipboard.writeText(document.getElementById('webhookUrl').value).then(() => {
          btn.textContent = 'Copied ✓';
          setTimeout(() => btn.textContent = 'Copy', 1800);
        });
      });
    </script>
  
  </x-admin-layout>