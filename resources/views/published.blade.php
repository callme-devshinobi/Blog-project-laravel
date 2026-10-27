<x-layout>
    <div class="drafts-page">
    
      <div class="dr-head">
        <h1>Published posts</h1>
        <p>Everything that is live on your blog right now.</p>
      </div>
    
      <div class="dr-layout">
    
        <main>
            @if (session('success'))
  <div class="dr-alert">{{ session('success') }}</div>
@endif
          @forelse ($posts as $post)
            <article class="dr-card">
              <div>
                <span class="dr-badge">{{ $post->category->name }}</span>
                <h2>{{ $post->title }}</h2>
                @if ($post->excerpt)
                  <p class="dr-excerpt">{{ $post->excerpt }}</p>
                @endif
                <p class="dr-meta">Published {{ $post->published_at->format('M j, Y') }} · {{ $post->read_time }} min read</p>
              </div>
              <div class="dr-actions">
                <a href="/post/{{ $post->id }}" class="dr-btn">View</a>
              
                <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this post? This cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dr-btn dr-btn-danger">Delete</button>
                </form>
              </div>
            </article>
          @empty
            <div class="dr-card">
              <p>Nothing published yet. <a href="{{ route('posts.drafts') }}">Check your drafts</a>.</p>
            </div>
          @endforelse
    
          {{ $posts->links('vendor.pagination.devnotes') }}
        </main>
    
        <aside class="dr-aside">
          <div class="dr-box">
            <h3>Shortcuts</h3>
            <a href="{{ route('posts.create') }}" class="dr-btn dr-btn-primary">+ New post</a>
            <a href="{{ route('posts.drafts') }}" class="dr-btn" style="margin-top:10px;">View drafts</a>
          </div>
        </aside>
    
      </div>
    </div>
    </x-layout>