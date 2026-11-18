<x-admin-layout title="Posts">

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
        <h3><span class="db-p-icon">📄</span> All posts ({{ $posts->total() }})</h3>
  
        <form method="GET" action="{{ route('admin.posts') }}" class="adm-filter">
          <input type="search" name="q" value="{{ $q }}" placeholder="Search title, blog or author…" aria-label="Search posts">
          <select name="status" onchange="this.form.submit()" aria-label="Filter by status">
            <option value="">Any status</option>
            <option value="published" @selected($status === 'published')>Published</option>
            <option value="draft"     @selected($status === 'draft')>Drafts</option>
            <option value="moderated" @selected($status === 'moderated')>Removed by an admin</option>
          </select>
          <select name="category" onchange="this.form.submit()" aria-label="Filter by category">
            <option value="">All categories</option>
            @foreach ($categories as $c)
              <option value="{{ $c->id }}" @selected($categoryId == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
          <button type="submit">Search</button>
        </form>
      </div>
  
      @forelse ($posts as $p)
        <div class="db-row">
          <div class="db-info">
            <h4>
              {{ $p->title }}
              @if ($p->moderated_at)
                <span class="adm-badge adm-badge-susp">Removed by admin</span>
              @elseif ($p->status === 'published')
                <span class="adm-badge adm-badge-live">Published</span>
              @else
                <span class="adm-badge adm-badge-draft">Draft</span>
              @endif
              @if ($p->blog?->suspended_at)<span class="adm-badge adm-badge-susp">Blog suspended</span>@endif
            </h4>
            <p>
              <span>{{ $p->blog?->title ?? 'No blog' }}</span>
              <span>by {{ $p->user?->name ?? 'unknown' }}</span>
              <span>{{ $p->category?->name }}</span>
              <span>{{ $p->created_at->format('M j, Y') }}</span>
            </p>
          </div>
  
          <a href="{{ route('admin.posts.show', $p) }}" class="db-rowlink">Manage</a>
        </div>
      @empty
        <p class="db-empty">No posts match that search.</p>
      @endforelse
    </section>
  
    {{ $posts->links('vendor.pagination.devnotes') }}
  
  </x-admin-layout>