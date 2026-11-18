<x-admin-layout title="Manage user">

    <style>
      .adm-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; }
      .adm-ok  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
      .adm-err { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
      .adm-alert ul { margin: 6px 0 0 18px; }
      .adm-badge { display: inline-block; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin-left: 8px; vertical-align: middle; }
      .adm-badge-admin { background: var(--indigo-soft); color: #4b3bbf; }
      .adm-badge-susp  { background: #fee2e2; color: #b91c1c; }
      .adm-badge-live  { background: #dcfce7; color: #166534; }
      .adm-badge-draft { background: #fef3c7; color: #b45309; }
      .adm-kv { display: flex; justify-content: space-between; gap: 16px; padding: 12px 24px; border-bottom: 1px solid var(--border); font-size: 14px; }
      .adm-kv:last-child { border-bottom: none; }
      .adm-kv span:first-child { color: var(--text-muted); }
      .adm-kv b { font-weight: 600; text-align: right; word-break: break-word; }
      .adm-actions { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; }
      .adm-actions p { color: var(--text-muted); font-size: 13.5px; }
      .adm-btn { width: 100%; padding: 11px; border-radius: 10px; font-family: inherit; font-weight: 700; font-size: 14px; cursor: pointer; border: 1px solid var(--border); background: transparent; color: var(--text); }
      .adm-btn:hover { border-color: var(--indigo); color: var(--indigo); }
      .adm-btn-warn   { color: #b45309; border-color: #fde68a; }
      .adm-btn-warn:hover { background: #fffbeb; color: #b45309; border-color: #b45309; }
      .adm-btn-danger { color: #dc2626; border-color: #fecaca; }
      .adm-btn-danger:hover { background: #fef2f2; color: #dc2626; border-color: #dc2626; }
      .adm-btn-solid  { background: #dc2626; border-color: #dc2626; color: #fff; }
      .adm-btn-solid:hover { background: #b91c1c; border-color: #b91c1c; color: #fff; }
      .adm-input { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--bg-light); color: var(--text); font-family: inherit; font-size: 14px; margin-bottom: 8px; }
      .adm-danger-box { border: 1px solid #fecaca; border-radius: 12px; padding: 14px; }
      .adm-danger-box h4 { font-size: 14px; font-weight: 800; margin-bottom: 6px; color: #dc2626; }
      [data-theme="dark"] .adm-ok  { background: rgba(74,222,128,.12); color: #86efac; border-color: rgba(74,222,128,.35); }
      [data-theme="dark"] .adm-err { background: rgba(220,38,38,.12); color: #fca5a5; border-color: rgba(220,38,38,.4); }
      [data-theme="dark"] .adm-badge-admin { color: #c4c0ff; }
      [data-theme="dark"] .adm-badge-susp  { background: rgba(220,38,38,.18); color: #fca5a5; }
      [data-theme="dark"] .adm-badge-live  { background: rgba(74,222,128,.15); color: #86efac; }
      [data-theme="dark"] .adm-badge-draft { background: rgba(251,191,36,.15); color: #fcd34d; }
      [data-theme="dark"] .adm-btn-warn:hover   { background: rgba(251,191,36,.1); }
      [data-theme="dark"] .adm-btn-danger:hover { background: rgba(220,38,38,.12); }
      [data-theme="dark"] .adm-danger-box { border-color: rgba(220,38,38,.4); }
    </style>
  
    @php
      $isSelf  = auth()->id() === $user->id;
      $joined  = $user->created_at->format('M j, Y');
      $suspendedOn = $user->suspended_at ? \Illuminate\Support\Carbon::parse($user->suspended_at)->format('M j, Y') : null;
    @endphp
  
    <p style="margin-bottom:16px;"><a href="{{ route('admin.users') }}" style="color:var(--indigo);font-weight:600;">← All users</a></p>
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <div class="db-grid">
  
      {{-- LEFT: details --}}
      <div>
  
        <section class="db-panel">
          <div class="db-panel-head">
            <h3>
              <span class="db-p-icon">👤</span> {{ $user->name }}
              @if ($user->is_admin)<span class="adm-badge adm-badge-admin">Admin</span>@endif
              @if ($user->suspended_at)<span class="adm-badge adm-badge-susp">Suspended</span>@endif
              @if ($isSelf)<span class="adm-badge adm-badge-admin">You</span>@endif
            </h3>
          </div>
  
          <div class="adm-kv"><span>Email</span><b>{{ $user->email }}</b></div>
          <div class="adm-kv"><span>Joined</span><b>{{ $joined }}</b></div>
          <div class="adm-kv"><span>Posts</span><b>{{ $postCount }}</b></div>
          <div class="adm-kv">
            <span>Blog</span>
            <b>
              @if ($user->blog)
                <a href="{{ route('blog.show', $user->blog) }}" target="_blank" rel="noopener" style="color:var(--indigo);">{{ $user->blog->title }} ({{ '@' . $user->blog->subdomain }})</a>
              @else
                None
              @endif
            </b>
          </div>
          @if ($user->blog?->custom_domain)
            <div class="adm-kv"><span>Custom domain</span><b>{{ $user->blog->custom_domain }}{{ $user->blog->custom_domain_verified_at ? '' : ' (not verified)' }}</b></div>
          @endif
          @if ($suspendedOn)
            <div class="adm-kv"><span>Suspended on</span><b>{{ $suspendedOn }}</b></div>
          @endif
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">📄</span> Latest posts</h3></div>
          @forelse ($posts as $p)
            <div class="db-row">
              <div class="db-info">
                <h4>{{ $p->title }}</h4>
                <p><span>{{ $p->created_at->format('M j, Y') }}</span></p>
              </div>
              <span class="adm-badge {{ $p->status === 'published' ? 'adm-badge-live' : 'adm-badge-draft' }}">{{ ucfirst($p->status) }}</span>
              @if ($p->status === 'published' && $p->blog)
                <a href="{{ $p->url }}" target="_blank" rel="noopener" class="db-rowlink">View</a>
              @endif
            </div>
          @empty
            <p class="db-empty">No posts yet.</p>
          @endforelse
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🧾</span> Recent activity</h3></div>
          @forelse ($logs as $log)
            <div class="db-row">
              <div class="db-info">
                <h4>{{ $log->description }}</h4>
                <p><span>{{ $log->created_at?->format('M j, Y · H:i') }}</span>@if ($log->ip_address)<span>{{ $log->ip_address }}</span>@endif</p>
              </div>
            </div>
          @empty
            <p class="db-empty">No activity recorded for this person yet.</p>
          @endforelse
        </section>
  
      </div>
  
      {{-- RIGHT: actions --}}
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">⚙️</span> Actions</h3></div>
  
          <div class="adm-actions">
  
            @if ($isSelf)
              <p>This is your own account, so most actions are turned off. To remove your own admin rights, use the terminal: <code>php artisan admin:make {{ $user->email }} --revoke</code></p>
  
            @else
  
              {{-- suspend / unsuspend --}}
              @if ($user->suspended_at)
                <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                  @csrf
                  <button type="submit" class="adm-btn">✅ Reinstate this account</button>
                </form>
              @elseif (! $user->is_admin)
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}"
                      onsubmit="return confirm(@js('Suspend ' . $user->name . '? They will be signed out and unable to log in until you reinstate them.'))">
                  @csrf
                  <button type="submit" class="adm-btn adm-btn-warn">⏸ Suspend account</button>
                </form>
                <p>Suspended people can't sign in. Their blog stays public until blog moderation is added.</p>
              @endif
  
              {{-- remove admin --}}
              @if ($user->is_admin)
                <form method="POST" action="{{ route('admin.users.revoke', $user) }}"
                      onsubmit="return confirm(@js('Remove admin rights from ' . $user->name . '?'))">
                  @csrf
                  <button type="submit" class="adm-btn adm-btn-warn">🛡️ Remove admin rights</button>
                </form>
                <p>Admins can't be suspended or deleted until their admin rights are removed. Admin rights can only be given from the terminal.</p>
              @endif
  
              {{-- delete --}}
              @unless ($user->is_admin)
                <div class="adm-danger-box">
                  <h4>Delete account</h4>
                  <p style="margin-bottom:10px;">This permanently deletes the account, their blog and all {{ $postCount }} of their {{ Str::plural('post', $postCount) }}. It cannot be undone.</p>
  
                  <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm(@js('Permanently delete ' . $user->name . ' and everything they wrote?'))">
                    @csrf
                    @method('DELETE')
                    <input type="password" name="password" class="adm-input" placeholder="Your admin password" required autocomplete="current-password">
                    <input type="text" name="confirm" class="adm-input" placeholder="Type their email: {{ $user->email }}" required autocomplete="off">
                    <button type="submit" class="adm-btn adm-btn-solid">🗑 Delete permanently</button>
                  </form>
                </div>
              @endunless
  
            @endif
  
          </div>
        </section>
      </aside>
  
    </div>
  </x-admin-layout>