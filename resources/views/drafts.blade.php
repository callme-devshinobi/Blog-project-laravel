<x-layout>
    <div class="drafts-page">
    
      <div class="dr-head">
        <h1>Your drafts</h1>
        <p>Preview a draft, then publish it when you're ready.</p>
      </div>
    
      <div class="dr-layout">
    
        {{-- Main column: draft cards --}}
        <main>
         @if (session('success'))
          <div class="dr-alert">{{ session('success') }}</div>
        @endif
          @forelse ($drafts as $draft)
            <article class="dr-card">
              <div>
                <span class="dr-badge">{{ $draft->category->name }}</span>
                <h2>{{ $draft->title }}</h2>
                @if ($draft->excerpt)
                  <p class="dr-excerpt">{{ $draft->excerpt }}</p>
                @endif
                <p class="dr-meta">Saved {{ $draft->created_at->diffForHumans() }} · {{ $draft->read_time }} min read</p>
              </div>
              <div class="dr-actions">
                <button type="button" class="dr-btn" data-preview="{{ $draft->id }}">Preview</button>
              
                <form action="{{ route('posts.destroy', $draft->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this post? This cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dr-btn dr-btn-danger">Delete</button>
                </form>
              </div>
            </article>
          @empty
            <div class="dr-card">
              <p>No drafts yet. <a href="{{ route('posts.create') }}">Write one</a>.</p>
            </div>
          @endforelse
        </main>
    
        {{-- Aside: list of all drafts --}}
        <aside class="dr-aside">
          <div class="dr-box">
            <h3>All drafts ({{ $drafts->count() }})</h3>
            <ul>
              @forelse ($drafts as $draft)
                <li><button type="button" data-preview="{{ $draft->id }}">{{ $draft->title }}</button></li>
              @empty
                <li class="dr-none">Nothing here yet.</li>
              @endforelse
            </ul>
            <a href="{{ route('posts.create') }}" class="dr-btn dr-btn-primary">+ New post</a>
          </div>
        </aside>
    
      </div>
    
      {{-- Each draft's content is stored here (hidden) and copied into the overlay when you click Preview --}}
      @foreach ($drafts as $draft)
        <div hidden id="draft-{{ $draft->id }}"
             data-title="{{ $draft->title }}"
             data-meta="{{ $draft->category->name }} · {{ $draft->read_time }} min read"
             data-image="{{ $draft->image_url }}">{!! $draft->body !!}</div>
      @endforeach
    
      {{-- The overlay --}}
      <div class="dr-modal" id="drModal">
        <div class="dr-modal-box" role="dialog" aria-modal="true">
          <button type="button" class="dr-close" id="drClose" aria-label="Close">✕</button>
          <img id="drImg" src="" alt="">
          <p class="dr-meta" id="drMeta"></p>
          <h2 id="drTitle"></h2>
          <div class="dr-body" id="drBody"></div>
    
          <div class="dr-modal-actions">
            <button type="button" class="dr-btn" id="drCancel">Close</button>
            <form id="drForm" method="POST" action="">
              @csrf
              <button type="submit" class="dr-btn dr-btn-primary">Publish now</button>
            </form>
          </div>
        </div>
      </div>
    
      <script>
        const modal  = document.getElementById('drModal');
        const form   = document.getElementById('drForm');
        // Laravel builds the URL; we swap __ID__ for the real draft id
        const publishUrl = "{{ route('posts.publish', '__ID__') }}";
    
        function openPreview(id) {
          const src = document.getElementById('draft-' + id);
          document.getElementById('drTitle').textContent = src.dataset.title;
          document.getElementById('drMeta').textContent  = src.dataset.meta;
          document.getElementById('drImg').src           = src.dataset.image;
          document.getElementById('drBody').innerHTML    = src.innerHTML;
          form.action = publishUrl.replace('__ID__', id);
          modal.classList.add('open');
          document.body.style.overflow = 'hidden';   // stop the page scrolling behind
        }
        function closePreview() {
          modal.classList.remove('open');
          document.body.style.overflow = '';
        }
    
        document.querySelectorAll('[data-preview]').forEach(btn => {
          btn.addEventListener('click', () => openPreview(btn.dataset.preview));
        });
        document.getElementById('drClose').addEventListener('click', closePreview);
        document.getElementById('drCancel').addEventListener('click', closePreview);
        modal.addEventListener('click', e => { if (e.target === modal) closePreview(); });  // click the dark area
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreview(); });
      </script>
    
    </div>
    </x-layout>