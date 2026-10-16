<x-layout>
  
    <!-- Hero -->
    <section class="page-hero">
      <p class="eyebrow">Explore topics</p>
      <h1>Browse by category.</h1>
      <p>Every article on DevNotes lives in one of these categories. Pick a topic and start reading — from Laravel deep-dives to developer habits.</p>
    </section>
  
    <!-- Floating search -->
    <div class="search-bar">
      <span class="search-icon">⌕</span>
      <input type="search" placeholder="Search categories or topics…" aria-label="Search categories" />
    </div>
  
    <!-- Category grid -->
    <section class="section">
      <div class="grid">
  @foreach ( $categories as $category)
  <a href="show/{{ $category['name']  }}" class="cat-card">
    <div class="cat-head">
      <div class="cat-icon" style="background:#fee2e2;color:#dc2626;"> {{ $category['icon']  }}</div>
      <span class="count"> {{ $category['post_count']  }} post(s)</span>
    </div>
    <h3>{{$category['name']}}</h3>
    <p>{{$category['description']}}</p>
    <div class="cat-foot">
      <span class="tag-line">Latest: <b>Laravel 12 guide</b></span>
      <span class="arrow">→</span>
    </div>
  </a>
  @endforeach
      </div>
  
      <!-- Featured strip -->
      <div class="featured">
        <div>
          <h2>Not sure where to start?</h2>
          <p>Start with the most popular series — the step-by-step Laravel 12 guide, or the full blog build from scratch. Both are beginner-friendly.</p>
        </div>
        <div class="actions">
          @if ($recommendedPost)
            <a href="/post/{{ $recommendedPost['id'] }}" class="btn-subscribe">
              Start with: {{ Str::limit($recommendedPost['title'], 28) }}
            </a>
          @endif
        
          <a href="/" class="btn-ghost">View all posts →</a>
        </div>
      </div>
    </section>
  
  </x-layout>