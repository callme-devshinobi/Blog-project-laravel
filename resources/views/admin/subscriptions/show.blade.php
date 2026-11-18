<x-admin-layout title="Manage subscription">

    @php
      $s = $subscription;
      $badge = ['active' => 'adm-badge-live', 'past_due' => 'adm-badge-draft', 'canceled' => 'adm-badge-draft', 'expired' => 'adm-badge-susp'];
      $until = $s->accessUntil();
    @endphp
  
    <p style="margin-bottom:16px;"><a href="{{ route('admin.subscriptions') }}" style="color:var(--indigo);font-weight:600;">← All subscriptions</a></p>
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <div class="db-grid">
  
      <div>
        <section class="db-panel">
          <div class="db-panel-head">
            <h3>
              <span class="db-p-icon">💳</span> {{ $s->user?->name ?? 'Deleted account' }}
              <span class="adm-badge {{ $badge[$s->status] ?? 'adm-badge-draft' }}">{{ ucfirst(str_replace('_', ' ', $s->status)) }}</span>
            </h3>
          </div>
  
          <div class="adm-kv">
            <span>Account</span>
            <b>
              @if ($s->user)
                <a href="{{ route('admin.users.show', $s->user) }}" style="color:var(--indigo);">{{ $s->user->name }}</a> · {{ $s->user->email }}
              @else
                Deleted
              @endif
            </b>
          </div>
          <div class="adm-kv"><span>Plan</span><b>{{ ucfirst($s->plan) }}</b></div>
          <div class="adm-kv"><span>Source</span><b>{{ ucfirst($s->provider) }}</b></div>
          <div class="adm-kv"><span>Started</span><b>{{ $s->created_at->format('M j, Y') }}</b></div>
          <div class="adm-kv"><span>Paid until</span><b>{{ $s->current_period_end ? $s->current_period_end->format('M j, Y · H:i') : 'No end date' }}</b></div>
          <div class="adm-kv"><span>Has access now</span><b>{{ $s->grantsAccess() ? 'Yes' : 'No' }}@if ($until && $s->grantsAccess()) (until {{ $until->format('M j, Y') }})@endif</b></div>
          @if ($s->cancel_at_period_end)
            <div class="adm-kv"><span>Cancellation</span><b>Will not renew</b></div>
          @endif
          @if ($s->provider_subscription_id)
            <div class="adm-kv"><span>Provider reference</span><b>{{ $s->provider_subscription_id }}</b></div>
          @endif
          @if ($s->note)
            <div class="adm-kv"><span>Note</span><b>{{ $s->note }}</b></div>
          @endif
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🧾</span> History</h3></div>
          @forelse ($logs as $log)
            <div class="db-row">
              <div class="db-info">
                <h4>{{ $log->description }}</h4>
                <p><span>{{ $log->created_at?->format('M j, Y · H:i') }}</span><span>{{ $log->user?->name ?? 'System' }}</span></p>
              </div>
            </div>
          @empty
            <p class="db-empty">Nothing has been recorded for this subscription yet.</p>
          @endforelse
        </section>
      </div>
  
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">⚙️</span> Actions</h3></div>
  
          <div class="adm-actions">
  
            <form method="POST" action="{{ route('admin.subscriptions.extend', $s) }}">
              @csrf
              <label class="adm-label" for="x_days">Extend by (days)</label>
              <input type="number" id="x_days" name="days" class="adm-input" min="1" max="3650" value="30" required>
              <button type="submit" class="adm-btn adm-btn-primary">Extend</button>
            </form>
  
            @if ($s->status !== 'expired')
              <div class="adm-danger-box" style="border-color:#fde68a;">
                <h4 style="color:#b45309;">End Pro access now</h4>
                <p style="margin-bottom:10px;">Access stops immediately.</p>
                <form method="POST" action="{{ route('admin.subscriptions.expire', $s) }}"
                      onsubmit="return confirm('End Pro access for this account now?')">
                  @csrf
                  <button type="submit" class="adm-btn adm-btn-warn">⏹ End access</button>
                </form>
              </div>
            @endif
  
            @if ($s->provider !== 'manual')
              <p><b>Note:</b> this subscription is billed by {{ ucfirst($s->provider) }}. Ending or extending it here changes access only. It doesn't stop the card being charged, and the provider's next renewal will overwrite the dates. Cancelling at the provider will be added with checkout.</p>
            @endif
  
          </div>
        </section>
      </aside>
  
    </div>
  </x-admin-layout>