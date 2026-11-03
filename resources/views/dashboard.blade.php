<x-layout>
    <div class="dashboard-page">
      <div class="db-shell">
    
        {{-- Sidebar --}}
        <aside class="db-side" id="dbSide">
          <button type="button" class="db-close" id="dbClose" aria-label="Close menu">✕</button>
          <nav class="db-nav">
            <a href="{{ route('dashboard') }}" class="db-link active"><span class="db-link-icon">🏠</span> Dashboard</a>
            <a href="{{ route('posts.published') }}" class="db-link"><span class="db-link-icon">📄</span> My Posts</a>
            <a href="{{ route('posts.drafts') }}" class="db-link"><span class="db-link-icon">📝</span> Drafts</a>
            <a href="{{ route('posts.create') }}" class="db-link"><span class="db-link-icon">✏️</span> New Post</a>
          </nav>
    
          <div class="db-promo">
            <span class="db-laptop">💻</span>
            <p>Keep writing,<br>keep sharing!</p>
          </div>
        </aside>
        <div class="db-backdrop" id="dbBackdrop"></div>
    
        {{-- Main --}}
        <main class="db-main">
    
          <div class="db-topbar">
            <div class="db-topbar-left">
              <button type="button" class="db-burger" id="dbBurger" aria-label="Open menu" aria-expanded="false" aria-controls="dbSide">☰</button>
              <h1>Dashboard</h1>
            </div>
            <div class="db-user">
              <span class="db-avatar">{{ auth()->user()->initials }}</span>
              <b>{{ auth()->user()->name }}</b>
            </div>
          </div>
    
          <section class="db-welcome">
            <div>
              <h2>👋 Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
              <p>Here's what's happening with your blog.</p>
            </div>
            <div class="db-art">
              <div class="db-bubble">Keep creating<br>great content!</div>
              <span class="db-person">👨🏾‍💻</span>
            </div>
          </section>
    
          {{-- Stats --}}
          <section class="db-stats">
            <div class="db-stat">
              <span class="db-stat-icon db-si-purple">📄</span>
              <span><b>{{ $publishedCount + $draftCount }}</b><span>Total Posts</span></span>
            </div>
            <div class="db-stat">
              <span class="db-stat-icon db-si-green">🚀</span>
              <span><b>{{ $publishedCount }}</b><span>Published</span></span>
            </div>
            <div class="db-stat">
              <span class="db-stat-icon db-si-amber">📋</span>
              <span><b>{{ $draftCount }}</b><span>Drafts</span></span>
            </div>
          </section>
    
          <div class="db-grid">
    
            {{-- Recent posts --}}
            <section class="db-panel">
              <div class="db-panel-head">
                <h3><span class="db-p-icon">📄</span> Your Recent Posts</h3>
                <a href="{{ route('posts.published') }}" class="db-viewall">View All →</a>
              </div>
    
              @forelse ($recentPosts as $post)
                <div class="db-row">
                  <span class="db-thumb"><img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?? $post->title }}"></span>
                  <div class="db-info">
                    <h4>{{ $post->title }}</h4>
                    <p><span>🗓 {{ $post->created_at->format('M j, Y') }}</span><span>{{ $post->category->name }}</span></p>
                  </div>
    
                  @if ($post->status === 'published')
                    <span class="db-badge live">Published</span>
                    <a href="/post/{{ $post->id }}" class="db-rowlink">View</a>
                  @else
                    <span class="db-badge draft">Draft</span>
                    <a href="{{ route('posts.drafts') }}" class="db-rowlink">Open</a>
                  @endif
                </div>
              @empty
                <p class="db-empty">You haven't written anything yet. <a href="{{ route('posts.create') }}">Write your first post</a>.</p>
              @endforelse
            </section>
    
            {{-- Right column --}}
            <aside class="db-right">
              <a href="{{ route('posts.create') }}" class="db-create">＋ Create New Post</a>
    
              <section class="db-panel">
                <div class="db-panel-head"><h3><span class="db-p-icon">⚡</span> Quick Actions</h3></div>
                <div style="padding: 10px 12px 14px;">
                  <a href="{{ route('posts.create') }}" class="db-qa">
                    <span class="db-qa-icon">✏️</span>
                    <span><b>Write a New Post</b><span>Start creating amazing content</span></span>
                  </a>
                  <a href="{{ route('posts.drafts') }}" class="db-qa">
                    <span class="db-qa-icon">📁</span>
                    <span><b>View Drafts</b><span>Continue where you left off</span></span>
                  </a>
                  <a href="{{ route('posts.published') }}" class="db-qa">
                    <span class="db-qa-icon">🚀</span>
                    <span><b>View Published</b><span>See what's live on your blog</span></span>
                  </a>
                </div>
              </section>
    
              <section class="db-panel">
                <div class="db-panel-head">
                  <h3><span class="db-p-icon">📄</span> Your Drafts</h3>
                  <a href="{{ route('posts.drafts') }}" class="db-viewall">View All →</a>
                </div>
    
                @forelse ($recentDrafts as $draft)
                  <div class="db-mini">
                    <span class="db-thumb"><img src="{{ $draft->image_url }}" alt=""></span>
                    <span><b>{{ $draft->title }}</b><span>Last saved: {{ $draft->updated_at->format('M j, Y') }}</span></span>
                    <a href="{{ route('posts.drafts') }}" class="db-edit" title="Open drafts">✏️</a>
                  </div>
                @empty
                  <p class="db-empty">No drafts right now.</p>
                @endforelse
              </section>
            </aside>
    
          </div>
        </main>
      </div>
      <script>
        const dbSide     = document.getElementById('dbSide');
        const dbBackdrop = document.getElementById('dbBackdrop');
        const dbBurger   = document.getElementById('dbBurger');
      
        function setMenu(open) {
          dbSide.classList.toggle('open', open);
          dbBackdrop.classList.toggle('open', open);
          dbBurger.setAttribute('aria-expanded', open);
          document.body.style.overflow = open ? 'hidden' : '';   // stop the page scrolling behind
        }
      
        dbBurger.addEventListener('click', () => setMenu(true));
        document.getElementById('dbClose').addEventListener('click', () => setMenu(false));
        dbBackdrop.addEventListener('click', () => setMenu(false));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
        dbSide.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
        window.addEventListener('resize', () => { if (window.innerWidth > 860) setMenu(false); });
      </script>
    </div>
    </x-layout>