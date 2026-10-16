

  <x-layout>
  <!-- ================= HERO ================= -->
  <section class="hero">
    <div class="container hero-grid">
      <div class="hero-text">
        <p class="hero-eyebrow">Welcome to DevNotes</p>
        <h1>Thoughts, tutorials and real-world tech insights.</h1>
        <p class="hero-desc">A blog for developers, creators and curious minds. Learn, build and grow — one post at a time.</p>
      </div>
      <figure class="hero-media">
        <!-- Photo: Developer workspace with code — Pixabay -->
        <img src="https://kimi-web-img.kimi.ai/img/cdn.pixabay.com/4f59813a0eb8024b15572745b58916dc3957b4a1.jpg"
             alt="Developer workspace with a monitor and laptop showing code">
      </figure>
    </div>
  </section>
  
  <!-- ================= MAIN CONTENT ================= -->
  <main class="container content-grid">
  
    <!-- ---------- LATEST POSTS ---------- -->
    <section class="posts">
      <div class="section-head">
        <h2>Latest Posts</h2>
        <a href="#" class="view-all">
          View All Posts
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="12" x2="20" y2="12"/><polyline points="13 5 20 12 13 19"/>
          </svg>
        </a>
      </div>
  
      <div class="post-grid">
  
        <!-- Card  -->
        @foreach ($pagedPosts as $post)
        <article class="card">
          
          <div class="card-media">
            <!-- Photo: PHP / Laravel code on screen — Pixabay -->
            <img src="{{ $post['image']  }}"
                 alt="{{ $post['image_alt']  }}">
            <span class="badge">{{ $post['category']  }}</span>
          </div>
          <div class="card-body">
            <h3><a href="/post/{{ $post['id']  }}">{{ $post['title']  }}</a></h3>
            <p class="excerpt">{{ $post['excerpt']  }}</p>
            <div class="card-meta">
              <span class="author"><span class="avatar">{{ $post['author_initials']  }}</span> {{ $post['author']  }}</span>
              <span class="meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $post['published_at']  }}
              </span>
              <span class="meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
                {{ $post['read_time']  }} min read
              </span>
            </div>
          </div>    
        </article>
        @endforeach
  
        
        {{ $pagedPosts->links('vendor.pagination.devnotes') }}
      </div>
    </section>
  
    <!-- ---------- SIDEBAR ---------- -->
    <aside class="sidebar">
  
      <!-- Search -->
      <x-sidebar_search></x-sidebar_search>
  
      <!-- Categories -->
      <x-sidebar_cat  :categories="$categories" :posts="$posts" />
  
      <!-- Popular Posts -->
      <x-sidebar_popular :posts="collect($posts)" />
  
    </aside>
  </main>
</x-layout>
  