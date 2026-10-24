<x-layout>
  <div class="all-posts">
  @php
    // A lookup list: category name => its icon (used on the cards and popular list)
    $icons = collect($categories)->pluck('icon', 'name');
  @endphp

  <!-- Hero -->
  <section class="page-hero">
    <p class="eyebrow">The archive</p>
    <h1>All Posts</h1>
    <p>Every tutorial, guide and insight published on DevNotes — newest first.</p>
  </section>

  <!-- Floating search -->
  <div class="search-bar">
    <span class="search-icon">⌕</span>
    <input type="search" placeholder="Search articles…" aria-label="Search articles" />
  </div>

  <!-- Layout -->
  <div class="layout">

    <!-- Main column -->
    <div>
      <!-- Category filters -->
      <div class="filters" id="filters">
        <a href="/posts" class="filter-chip active">All</a>

        @foreach ($categories as $cat)
          <a href="/show/{{ rawurlencode($cat['slug']) }}" class="filter-chip">{{ $cat['name'] }}</a>
        @endforeach

        <span class="results-count">
          Showing <b>{{ $pagedPosts->firstItem() ?? 0 }}–{{ $pagedPosts->lastItem() ?? 0 }}</b>
          of <b>{{ $pagedPosts->total() }}</b> posts
        </span>
      </div>

      <!-- Post grid -->
      <div class="grid">

        @forelse ($pagedPosts as $post)
          <a href="/post/{{ $post['id'] }}" class="post-card">
            <div class="thumb">
              <img src="{{ $post->image_url }}" alt="{{ $post['image_alt'] ?? $post['title'] }}" loading="lazy">
              <span class="cat-badge">{{ $post->category->name }}</span>
            </div>
            <div class="post-body">
              <h3>{{ $post['title'] }}</h3>
              <p>{{ $post['excerpt'] }}</p>
              <div class="post-meta">
                <span class="avatar-sm">{{ $post['author_initials'] }}</span><b>{{ $post['author'] }}</b>
                <span class="dot">·</span> {{ $post->published_at?->format('M j, Y') }}
                <span class="dot">·</span> {{ $post['read_time'] }} min read
              </div>
            </div>
          </a>
        @empty
          <p>No posts yet. Check back soon!</p>
        @endforelse

      </div>

      <!-- Pagination -->
      {{ $pagedPosts->links('vendor.pagination.devnotes') }}
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Search -->
          <x-sidebar_search></x-sidebar_search>
        
      
 <!-- Categories -->
 <x-sidebar_cat :categories="$categories" />
      
<!-- Popular Posts -->
<x-sidebar_popular :posts="$popularPosts" />
    </aside>

  </div>
</div>
</x-layout>