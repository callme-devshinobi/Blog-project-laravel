<x-admin-layout title="Activity log">

    <section class="db-panel">
      <div class="db-panel-head">
        <h3><span class="db-p-icon">🧾</span> Everything that happens in the app</h3>
        <form method="GET" action="{{ route('admin.activity') }}">
          <select name="type" onchange="this.form.submit()" aria-label="Filter by type"
                  style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:var(--bg-light);color:var(--text);font-family:inherit;">
            <option value="">All activity</option>
            <option value="auth"  @selected($type === 'auth')>Sign-ins</option>
            <option value="post"  @selected($type === 'post')>Posts</option>
            <option value="blog"  @selected($type === 'blog')>Blogs</option>
            <option value="user"  @selected($type === 'user')>Accounts</option>
            <option value="admin" @selected($type === 'admin')>Admin actions</option>
            <option value="subscription" @selected($type === 'subscription')>Subscriptions</option>
            <option value="ai"           @selected($type === 'ai')>AI keys</option>
          </select>
        </form>
      </div>
  
      @forelse ($logs as $log)
        <div class="db-row">
          <div class="db-info">
            <h4>{{ $log->description }}</h4>
            <p>
              <span>{{ $log->user?->name ?? 'System' }}</span>
              <span>{{ $log->created_at?->format('M j, Y · H:i') }}</span>
              <span>{{ $log->action }}</span>
              @if ($log->ip_address)<span>{{ $log->ip_address }}</span>@endif
            </p>
          </div>
        </div>
      @empty
        <p class="db-empty">No activity matches that filter yet.</p>
      @endforelse
    </section>
  
    {{ $logs->links('vendor.pagination.devnotes') }}
  
  </x-admin-layout>