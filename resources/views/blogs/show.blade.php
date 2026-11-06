<x-layout>
  <div class="all-posts">

  <!-- Hero -->
  <section class="page-hero">
    <p class="eyebrow">Category</p>
    <h1>{{ $name }}</h1>
    @if ($currentBlog)
      <p>Posts in {{ $name }} on {{ $currentBlog->title }} — newest first.</p>
    @else
      <p>Every {{ $name }} post published on DevNotes — newest first.</p>
    @endif
  </section>

  <!-- Layout -->
  <div class="layout">

    <!-- Main column -->
    <div>
      <!-- Category filters -->
      <div class="filters" id="filters">
        <a href="{{ $base }}/posts" class="filter-chip">All</a>

        @foreach ($categories as $cat)
          <a href="{{ $base }}/show/{{ rawurlencode($cat['slug']) }}"
             class="filter-chip {{ $category->slug === $cat['slug'] ? 'active' : '' }}">{{ $cat['name'] }}</a>
        @endforeach

        <span class="results-count">
          Showing <b>{{ $post_lists->firstItem() ?? 0 }}–{{ $post_lists->lastItem() ?? 0 }}</b>
          of <b>{{ $post_lists->total() }}</b> posts
        </span>
      </div>

      <!-- Post grid -->
      <div class="grid">
        @forelse ($post_lists as $post)
          <a href="{{ $post->url }}" class="post-card">
            <div class="thumb">
              <img src="{{ $post->image_url }}" alt="{{ $post['image_alt'] ?? $post['title'] }}" loading="lazy">
              <span class="cat-badge">{{ $name }}</span>
            </div>
            <div class="post-body">
              <h3>{{ $post['title'] }}</h3>
              <p>{{ $post['excerpt'] }}</p>
              <div class="post-meta">
                <span class="avatar-sm">{{ $post->user->initials }}</span><b>{{ $post->user->author_name }}</b>
                <span class="dot">·</span> {{ $post->published_at?->format('M j, Y') }}
                <span class="dot">·</span> {{ $post['read_time'] }} min read
              </div>
            </div>
          </a>
        @empty
          <p>No {{ $name }} posts here yet. <a href="{{ $base }}/categories">Browse other categories</a>.</p>
        @endforelse
      </div>

      <!-- Pagination -->
      {{ $post_lists->links('vendor.pagination.devnotes') }}
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
      <x-sidebar_search></x-sidebar_search>
      <x-sidebar_cat :categories="$categories" :current="$category->slug" />
      <x-sidebar_popular :posts="$popularPosts" />
    </aside>

  </div>
  </div>
</x-layout>