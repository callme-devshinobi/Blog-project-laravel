<x-layout>
    <style>
        .search-empty a {
          color: #4f46e5;
          font-weight: 600;
          text-decoration: underline;
          text-underline-offset: 3px;
        }
        .search-empty a:hover { color: #4338ca; }
      </style>
    <div class="all-posts">
  
    <!-- Hero -->
    <section class="page-hero">
      <p class="eyebrow">Search</p>
      <h1>{{ $q !== '' ? 'Results for “' . $q . '”' : 'Search' }}</h1>
      <p>{{ $currentBlog ? 'Searching ' . $currentBlog->title : 'Searching all of DevNotes' }}</p>
    </section>
  
    <!-- Search box -->
    <form class="search-bar" action="{{ $base }}/search" method="GET" role="search">
      <span class="search-icon">⌕</span>
      <input type="search" name="q" value="{{ $q }}" placeholder="Search articles…"
             aria-label="Search articles" maxlength="80" @if ($q === '') autofocus @endif>
    </form>
  
    <!-- Layout -->
    <div class="layout">
  
      <!-- Main column -->
      <div>
  
        @if ($results)
          <div class="filters">
            <span class="results-count">
              <b>{{ $results->total() }}</b> {{ Str::plural('result', $results->total()) }}
              @if ($results->total() > 0)
                · showing <b>{{ $results->firstItem() }}–{{ $results->lastItem() }}</b>
              @endif
            </span>
          </div>
  
          <div class="grid">
            @forelse ($results as $post)
              <a href="{{ $post->url }}" class="post-card">
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
              <div class="search-empty">
                <p>Nothing matched “{{ $q }}”. Try different or fewer words, or
                  <b> <a href="{{ $base }}/posts">browse all posts</a>. </b></p>
              </div>
            @endforelse
          </div>
  
          {{ $results->links('vendor.pagination.devnotes') }}
  
        @elseif ($q !== '')
          <p>Please type at least 2 characters to search.</p>
        @else
          <p>Type a word or two above to search the posts
             {{ $currentBlog ? 'on this blog' : 'across DevNotes' }}.</p>
        @endif
  
      </div>
  
      <!-- Sidebar -->
      <aside class="sidebar">
        <x-sidebar_search></x-sidebar_search>
        <x-sidebar_cat :categories="$categories" />
        <x-sidebar_popular :posts="$popularPosts" />
      </aside>
  
    </div>
    </div>
  </x-layout>