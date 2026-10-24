<x-layout>

  <!-- Post hero -->
  <section class="post-hero">
    <div class="post-hero-inner">
      <div class="breadcrumb">
        <a href="/">Home</a><span class="sep">/</span>
        <a href="/show/{{ $post->category->slug }}">{{ $post->category->name }}</a><span class="sep">/</span>
        <span class="current">{{ $post['title'] }}</span>
      </div>
      <span class="post-badge">{{ $post->category->name }}</span>
      <h1>{{ $post['title'] }}</h1>
      <div class="post-meta">
        <div class="author">
          <div class="avatar">{{ $post->user->initials }}</div>
          <div><b>{{ $post->user->author_name }}</b><span>Writer & Developer</span></div>
        </div>
        <span class="sep">·</span>
        <span class="meta-item"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          {{ $post->published_at?->format('M d, Y') }}</span>
        <span class="sep">·</span>
        <span class="meta-item">◷ {{ $post['read_time'] }} min read</span>
      </div>
    </div>
  </section>

  <!-- Layout -->
  <div class="layout">

    <!-- Article -->
    <article>
      <div class="article-card">
        <div class="cover">
          <img src="{{ $post->image_url }}"
               alt="{{ $post['image_alt'] ?? $post['title'] }}"
               style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
          <span class="cover-badge">{{ $post->category->name }}</span>
        </div>

        <div class="article-body">
          {!! $post['body'] !!}
        </div>

        <!-- Tags -->
        <div class="post-foot">
          <div class="tags">
            @foreach ($post['tags'] ?? [] as $tag)
              <a href="#" class="tag">#{{ $tag }}</a>
            @endforeach
          </div>
        </div>

        <!-- Author box -->
        <div class="avatar">{{ $post->user->initials }}</div>
<div>
  <small>WRITTEN BY</small>
  <h4>{{ $post->user->author_name }}</h4>
</div>

        <!-- Prev / Next -->
        <div class="post-nav">
          @if ($previousPost)
            <a href="/post/{{ $previousPost['id'] }}" class="nav-card">
              <small>← Older post</small>
              <h4>{{ $previousPost['title'] }}</h4>
            </a>
          @endif

          @if ($nextPost)
            <a href="/post/{{ $nextPost['id'] }}" class="nav-card next">
              <small>Newer post →</small>
              <h4>{{ $nextPost['title'] }}</h4>
            </a>
          @endif
        </div>
      </div>
    </article>

    <!-- Sidebar -->
    <aside class="sidebar">
      <x-sidebar_search></x-sidebar_search>

      <x-sidebar_cat :current="$current" :categories="$categories" />

      <x-sidebar_popular :posts="$popularPosts" />
    </aside>

  </div>

</x-layout>