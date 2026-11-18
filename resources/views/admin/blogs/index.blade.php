<x-admin-layout title="Blogs">

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
        <h3><span class="db-p-icon">📚</span> All blogs ({{ $blogs->total() }})</h3>
  
        <form method="GET" action="{{ route('admin.blogs') }}" class="adm-filter">
          <input type="search" name="q" value="{{ $q }}" placeholder="Search title, address, domain, owner…" aria-label="Search blogs">
          <select name="filter" onchange="this.form.submit()" aria-label="Filter blogs">
            <option value="">All blogs</option>
            <option value="suspended" @selected($filter === 'suspended')>Suspended</option>
            <option value="domains"   @selected($filter === 'domains')>With a custom domain</option>
          </select>
          <button type="submit">Search</button>
        </form>
      </div>
  
      @forelse ($blogs as $b)
        <div class="db-row">
          <div class="db-info">
            <h4>
              {{ $b->title }}
              @if ($b->suspended_at)<span class="adm-badge adm-badge-susp">Suspended</span>@endif
              @if ($b->custom_domain)<span class="adm-badge adm-badge-admin">{{ $b->custom_domain_verified_at ? 'Domain active' : 'Domain pending' }}</span>@endif
            </h4>
            <p>
              <span>{{ '@' . $b->subdomain }}</span>
              <span>by {{ $b->user?->name ?? 'unknown' }}</span>
              <span>{{ $b->published_count }} published / {{ $b->posts_count }} total</span>
              <span>Created {{ $b->created_at->format('M j, Y') }}</span>
            </p>
          </div>
  
          <a href="{{ route('admin.blogs.show', $b) }}" class="db-rowlink">Manage</a>
        </div>
      @empty
        <p class="db-empty">No blogs match that search.</p>
      @endforelse
    </section>
  
    {{ $blogs->links('vendor.pagination.devnotes') }}
  
  </x-admin-layout>