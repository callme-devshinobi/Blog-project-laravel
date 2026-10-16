
<x-layout>
  
  <!-- ================= CATEGORY HERO ================= -->
  <section class="category-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="#">Home</a><span class="sep">/</span>
        <a href="#">Categories</a><span class="sep">/</span>
        <span class="current">{{$category['name']}}</span>
      </nav>
      <h1><span>#</span>{{$category['name']}}</h1>
      <p class="category-desc">
        {{$category['description']}}
      </p>
      <div class="category-stats">
        <span class="stat-chip accent">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
         <!-- ================= Counts the number of articles ================= -->
          {{ $post_lists->count() }} Articles
        </span>
        <span class="stat-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
          Updated Sep 16, 2025
        </span>
      </div>
    </div>
  </section>
  
  <!-- ================= MAIN CONTENT ================= -->
  <main class="container content-grid">
  
    <!-- ---------- POST LIST ---------- -->
    <section class="posts">
      <h2 class="post-count">All Posts <span>— {{$category['name']}}</span></h2>
  
      <div class="post-list">
        @foreach ( $post_lists as $post)
        <article class="post-row">
          <div class="row-media">
            <!-- Photo: PHP / Laravel code on screen — Pixabay -->
            <img src="{{$post['image']}}" alt="{{$post['image_alt']}}">
            <span class="badge">{{$post['category']}}</span>
          </div>
          <div class="row-body">
            <h3><a href="/post/{{ $post['id']  }}">{{$post['title']}}</a></h3>
            <p class="excerpt">{{$post['excerpt']}}</p>
            <div class="card-meta">
              <span class="author"><span class="avatar">{{$post['author_initials']}}</span> {{$post['author']}}</span>
              <span class="meta-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> {{$post['published_at']}}</span>
              <span class="meta-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg> {{$post['read_time']}} min read</span>
            </div>
          </div>
        </article>
        @endforeach
        <!-- Post 1 -->
        
  
       
      </div>
  
      <!-- Pagination -->
      {{ $post_lists->links('vendor.pagination.devnotes') }}
    </section>
  
    <!-- ---------- SIDEBAR ---------- -->
    <aside class="sidebar">
  
      <!-- Search -->
      <x-sidebar_search></x-sidebar_search>
  
      <!-- Categories -->
      <x-sidebar_cat :current="$category['slug']" :categories="$categories" :posts="$posts" />
  
      <!-- Popular Posts -->
      <x-sidebar_popular :posts="collect($posts)" />
  
    </aside>
  </main>
  
</x-layout>