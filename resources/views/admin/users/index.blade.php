<x-admin-layout title="Users">

    <style>
      .adm-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; }
      .adm-ok  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
      .adm-err { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
      .adm-alert ul { margin: 6px 0 0 18px; }
      .adm-badge { display: inline-block; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin-left: 8px; vertical-align: middle; }
      .adm-badge-admin { background: var(--indigo-soft); color: #4b3bbf; }
      .adm-badge-susp  { background: #fee2e2; color: #b91c1c; }
      .adm-filter { display: flex; gap: 8px; flex-wrap: wrap; }
      .adm-filter input, .adm-filter select {
        padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-light); color: var(--text); font-family: inherit; font-size: 14px;
      }
      .adm-filter button { background: var(--indigo); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-family: inherit; }
      [data-theme="dark"] .adm-ok  { background: rgba(74,222,128,.12); color: #86efac; border-color: rgba(74,222,128,.35); }
      [data-theme="dark"] .adm-err { background: rgba(220,38,38,.12); color: #fca5a5; border-color: rgba(220,38,38,.4); }
      [data-theme="dark"] .adm-badge-admin { color: #c4c0ff; }
      [data-theme="dark"] .adm-badge-susp  { background: rgba(220,38,38,.18); color: #fca5a5; }
    </style>
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <section class="db-panel">
      <div class="db-panel-head">
        <h3><span class="db-p-icon">👥</span> All users ({{ $users->total() }})</h3>
  
        <form method="GET" action="{{ route('admin.users') }}" class="adm-filter">
          <input type="search" name="q" value="{{ $q }}" placeholder="Search name or email…" aria-label="Search users">
          <select name="filter" onchange="this.form.submit()" aria-label="Filter users">
            <option value="">Everyone</option>
            <option value="admins"    @selected($filter === 'admins')>Admins</option>
            <option value="suspended" @selected($filter === 'suspended')>Suspended</option>
          </select>
          <button type="submit">Search</button>
        </form>
      </div>
  
      @forelse ($users as $u)
        <div class="db-row">
          <span class="db-avatar" @if ($u->avatar_url) style="background:center/cover no-repeat url('{{ $u->avatar_url }}')" @endif>{{ $u->avatar_url ? '' : $u->initials }}</span>
  
          <div class="db-info">
            <h4>
              {{ $u->name }}
              @if ($u->is_admin)<span class="adm-badge adm-badge-admin">Admin</span>@endif
              @if ($u->suspended_at)<span class="adm-badge adm-badge-susp">Suspended</span>@endif
            </h4>
            <p>
              <span>{{ $u->email }}</span>
              <span>Joined {{ $u->created_at->format('M j, Y') }}</span>
              <span>{{ $u->posts_count }} {{ Str::plural('post', $u->posts_count) }}</span>
              @if ($u->blog)<span>{{ '@' . $u->blog->subdomain }}</span>@endif
            </p>
          </div>
  
          <a href="{{ route('admin.users.show', $u) }}" class="db-rowlink">Manage</a>
        </div>
      @empty
        <p class="db-empty">No users match that search.</p>
      @endforelse
    </section>
  
    {{ $users->links('vendor.pagination.devnotes') }}
  
  </x-admin-layout>