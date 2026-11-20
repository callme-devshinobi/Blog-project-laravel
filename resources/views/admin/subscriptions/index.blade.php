<x-admin-layout title="Subscriptions">

    @php
      $badge = ['active' => 'adm-badge-live', 'past_due' => 'adm-badge-draft', 'canceled' => 'adm-badge-draft', 'expired' => 'adm-badge-susp'];
    @endphp
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <section class="db-stats">
      <div class="db-stat">
        <span class="db-stat-icon db-si-green">⭐</span>
        <span><b>{{ $stats['active'] }}</b><span>Active ({{ $stats['paying'] }} paying, {{ $stats['manual'] }} granted)</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-green">💰</span>
        <span><b>₦ {{ number_format($stats['revenue_month'] / 100) }}</b><span>This month (₦{{ number_format($stats['revenue_total'] / 100) }} in total)</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-amber">⏳</span>
        <span><b>{{ $stats['expiring'] }}</b><span>Ending within 7 days</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-purple">📉</span>
        <span><b>{{ $stats['ended'] }}</b><span>Cancelled or expired</span></span>
      </div>
    </section>
  
    <div class="db-grid">
  
      <section class="db-panel">
        <div class="db-panel-head">
          <h3><span class="db-p-icon">💳</span> All subscriptions ({{ $subscriptions->total() }})</h3>
  
          <form method="GET" action="{{ route('admin.subscriptions') }}" class="adm-filter">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search name or email…" aria-label="Search subscriptions">
            <select name="status" onchange="this.form.submit()" aria-label="Filter by status">
              <option value="">Any status</option>
              @foreach (['active' => 'Active', 'past_due' => 'Payment late', 'canceled' => 'Cancelled', 'expired' => 'Expired'] as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
              @endforeach
            </select>
            <select name="provider" onchange="this.form.submit()" aria-label="Filter by provider">
              <option value="">Any source</option>
              @foreach (['manual' => 'Granted by admin', 'paystack' => 'Paystack', 'stripe' => 'Stripe'] as $value => $label)
                <option value="{{ $value }}" @selected($provider === $value)>{{ $label }}</option>
              @endforeach
            </select>
            <button type="submit">Search</button>
          </form>
        </div>
  
        @forelse ($subscriptions as $s)
          <div class="db-row">
            <div class="db-info">
              <h4>
                {{ $s->user?->name ?? 'Deleted account' }}
                <span class="adm-badge {{ $badge[$s->status] ?? 'adm-badge-draft' }}">{{ ucfirst(str_replace('_', ' ', $s->status)) }}</span>
                <span class="adm-badge adm-badge-admin">{{ ucfirst($s->provider) }}</span>
              </h4>
              <p>
                <span>{{ $s->user?->email }}</span>
                <span>
                  @if ($s->current_period_end)
                    {{ $s->status === 'active' && ! $s->cancel_at_period_end ? 'Renews' : 'Ends' }} {{ $s->current_period_end->format('M j, Y') }}
                  @else
                    No end date
                  @endif
                </span>
                <span>Started {{ $s->created_at->format('M j, Y') }}</span>
              </p>
            </div>
  
            <a href="{{ route('admin.subscriptions.show', $s) }}" class="db-rowlink">Manage</a>
          </div>
        @empty
          <p class="db-empty">No subscriptions match that search.</p>
        @endforelse
      </section>
  
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">🎁</span> Grant Pro</h3></div>
  
          <form method="POST" action="{{ route('admin.subscriptions.grant') }}" class="adm-actions">
            @csrf
            <div>
              <label class="adm-label" for="g_email">Account email</label>
              <input type="email" id="g_email" name="email" class="adm-input" value="{{ old('email') }}" required>
            </div>
            <div>
              <label class="adm-label" for="g_days">Days of Pro</label>
              <input type="number" id="g_days" name="days" class="adm-input" min="1" max="3650" value="{{ old('days', 30) }}">
            </div>
            <label style="display:flex;gap:8px;align-items:center;font-size:14px;">
              <input type="checkbox" name="lifetime" value="1" @checked(old('lifetime'))> No end date
            </label>
            <div>
              <label class="adm-label" for="g_note">Note (only admins see it)</label>
              <input type="text" id="g_note" name="note" class="adm-input" maxlength="150" value="{{ old('note') }}" placeholder="e.g. Testing, partner, support case">
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">Grant Pro</button>
            <p>For testing, partners and support cases. Paying customers will appear in this list by themselves once checkout is connected.</p>
          </form>
        </section>
        <section class="db-panel">
            <div class="db-panel-head"><h3><span class="db-p-icon">💰</span> Recent payments</h3></div>
            @forelse ($recentPayments as $p)
              <div class="adm-kv">
                <span>{{ $p->user?->name ?? 'Unknown' }} · {{ $p->paid_at?->format('M j') }}</span>
                <b>{{ $p->formatted_amount }}</b>
              </div>
            @empty
              <p class="db-empty">No payments yet.</p>
            @endforelse
          </section>
      </aside>
  
    </div>
  
    {{ $subscriptions->links('vendor.pagination.devnotes') }}
  
  </x-admin-layout>