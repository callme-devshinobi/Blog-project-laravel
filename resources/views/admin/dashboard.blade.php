<x-admin-layout title="Admin overview">

    <section class="db-stats">
      <div class="db-stat">
        <span class="db-stat-icon db-si-purple">👥</span>
        <span><b>{{ $stats['users'] }}</b><span>Users ({{ $stats['new_users'] }} new this week)</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-green">📚</span>
        <span><b>{{ $stats['blogs'] }}</b><span>Blogs</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-amber">🌐</span>
        <span><b>{{ $stats['domains'] }}</b><span>Custom domains</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-green">🚀</span>
        <span><b>{{ $stats['published'] }}</b><span>Published posts</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-amber">📋</span>
        <span><b>{{ $stats['drafts'] }}</b><span>Drafts</span></span>
      </div>
      <div class="db-stat">
        <span class="db-stat-icon db-si-purple">📄</span>
        <span><b>{{ $stats['published'] + $stats['drafts'] }}</b><span>Total posts</span></span>
      </div>
    </section>
  
    <div class="db-grid">
  
      <section class="db-panel">
        <div class="db-panel-head">
          <h3><span class="db-p-icon">🧾</span> Recent activity</h3>
          <a href="{{ route('admin.activity') }}" class="db-viewall">View all →</a>
        </div>
  
        @forelse ($latestLogs as $log)
          <div class="db-row">
            <div class="db-info">
              <h4>{{ $log->description }}</h4>
              <p><span>{{ $log->user?->name ?? 'System' }}</span><span>{{ $log->created_at?->diffForHumans() }}</span></p>
            </div>
          </div>
        @empty
          <p class="db-empty">Nothing has been recorded yet.</p>
        @endforelse
      </section>
  
      <aside class="db-right">
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">👥</span> Newest users</h3></div>
          @forelse ($latestUsers as $u)
            <div class="db-mini">
              <span><b>{{ $u->name }}</b><span>Joined {{ $u->created_at->format('M j, Y') }}{{ $u->is_admin ? ' · admin' : '' }}</span></span>
            </div>
          @empty
            <p class="db-empty">No users yet.</p>
          @endforelse
        </section>
  
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">📄</span> Newest posts</h3></div>
          @forelse ($latestPosts as $p)
            <div class="db-mini">
              <span><b>{{ $p->title }}</b><span>{{ $p->blog?->title }} · {{ $p->status }}</span></span>
            </div>
          @empty
            <p class="db-empty">No posts yet.</p>
          @endforelse
        </section>
  
      </aside>
  
    </div>
  </x-admin-layout>