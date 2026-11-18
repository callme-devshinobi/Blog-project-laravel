<x-admin-layout title="Manage post">

    @php
      // A safe plain-text preview: tags are stripped and the text is escaped again,
      // so nothing in a post can ever run in an admin's browser.
      $text = preg_replace(['#</p>\s*<p>#i', '#<br\s*/?>#i'], ["\n\n", "\n"], (string) $post->body);
      $text = html_entity_decode(strip_tags($text), ENT_QUOTES);
  
      $tagList = is_array($post->tags) ? $post->tags : (json_decode($post->tags ?? '[]', true) ?: []);
      $moderatedOn = $post->moderated_at ? \Illuminate\Support\Carbon::parse($post->moderated_at)->format('M j, Y · H:i') : null;
    @endphp
  
    <p style="margin-bottom:16px;"><a href="{{ route('admin.posts') }}" style="color:var(--indigo);font-weight:600;">← All posts</a></p>
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    @if ($post->moderated_at)
      <div class="adm-banner" role="status">
        <b>Removed by an admin on {{ $moderatedOn }}.</b>
        @if ($post->moderation_reason) Reason: {{ $post->moderation_reason }}. @endif
        The owner can't publish it again unless you restore it or clear the flag.
      </div>
    @endif
  
    <div class="db-grid">
  
      {{-- LEFT --}}
      <div>
  
        <section class="db-panel">
          <div class="db-panel-head">
            <h3>
              <span class="db-p-icon">📄</span> {{ $post->title }}
              @if ($post->moderated_at)
                <span class="adm-badge adm-badge-susp">Removed by admin</span>
              @elseif ($post->status === 'published')
                <span class="adm-badge adm-badge-live">Published</span>
              @else
                <span class="adm-badge adm-badge-draft">Draft</span>
              @endif
            </h3>
          </div>
  
          <div class="adm-kv">
            <span>Blog</span>
            <b>
              @if ($post->blog)
                <a href="{{ route('admin.blogs.show', $post->blog) }}" style="color:var(--indigo);">{{ $post->blog->title }}</a>
                @if ($post->blog->suspended_at)<span class="adm-badge adm-badge-susp">Suspended</span>@endif
              @else
                None
              @endif
            </b>
          </div>
          <div class="adm-kv">
            <span>Author</span>
            <b>
              @if ($post->user)
                <a href="{{ route('admin.users.show', $post->user) }}" style="color:var(--indigo);">{{ $post->user->name }}</a>
              @else
                Unknown
              @endif
            </b>
          </div>
          <div class="adm-kv"><span>Category</span><b>{{ $post->category?->name ?? '—' }}</b></div>
          <div class="adm-kv"><span>Created</span><b>{{ $post->created_at->format('M j, Y') }}</b></div>
          @if ($post->status === 'published' && $post->published_at)
            <div class="adm-kv"><span>Published</span><b>{{ $post->published_at->format('M j, Y') }}</b></div>
          @endif
          <div class="adm-kv"><span>Reading time</span><b>{{ $post->read_time }} min</b></div>
          @if ($post->status === 'published' && $post->blog)
            <div class="adm-kv"><span>Public page</span><b><a href="{{ $post->url }}" target="_blank" rel="noopener" style="color:var(--indigo);">Open →</a></b></div>
          @endif
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">👁</span> Content (plain-text preview)</h3></div>
          <div style="padding:20px 24px;max-height:420px;overflow-y:auto;line-height:1.75;">
            @if ($post->excerpt)
              <p style="color:var(--text-muted);margin-bottom:14px;"><b>Excerpt:</b> {{ $post->excerpt }}</p>
            @endif
            {!! nl2br(e($text)) !!}
          </div>
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">✎</span> Details</h3></div>
          <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="adm-actions">
            @csrf
            @method('PUT')
            <div>
              <label class="adm-label" for="title">Title</label>
              <input type="text" id="title" name="title" class="adm-input" maxlength="255" value="{{ old('title', $post->title) }}" required>
            </div>
            <div>
              <label class="adm-label" for="excerpt">Excerpt</label>
              <textarea id="excerpt" name="excerpt" class="adm-input" maxlength="300">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
              <label class="adm-label" for="category_id">Category</label>
              <select id="category_id" name="category_id" class="adm-input" required>
                @foreach ($categories as $c)
                  <option value="{{ $c->id }}" @selected(old('category_id', $post->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="adm-label" for="tags">Tags (comma separated, up to 6)</label>
              <input type="text" id="tags" name="tags" class="adm-input" maxlength="300" value="{{ old('tags', implode(', ', $tagList)) }}">
            </div>
            <p>The body and the post's address are left as the writer wrote them. For serious problems, unpublish or delete the post instead.</p>
            <button type="submit" class="adm-btn adm-btn-primary">Save changes</button>
          </form>
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🧾</span> History</h3></div>
          @forelse ($logs as $log)
            <div class="db-row">
              <div class="db-info">
                <h4>{{ $log->description }}</h4>
                <p><span>{{ $log->created_at?->format('M j, Y · H:i') }}</span><span>{{ $log->user?->name ?? 'System' }}</span></p>
              </div>
            </div>
          @empty
            <p class="db-empty">Nothing has been recorded for this post yet.</p>
          @endforelse
        </section>
  
      </div>
  
      {{-- RIGHT: actions --}}
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">⚙️</span> Actions</h3></div>
  
          <div class="adm-actions">
  
            @if ($post->moderated_at)
              <form method="POST" action="{{ route('admin.posts.restore', $post) }}">
                @csrf
                <button type="submit" class="adm-btn adm-btn-primary">↩ Restore and publish</button>
              </form>
              <form method="POST" action="{{ route('admin.posts.clear', $post) }}">
                @csrf
                <button type="submit" class="adm-btn">Let the owner publish it again</button>
              </form>
              <p>"Restore" makes it public right away. The second option clears the flag and leaves it as a draft for the owner to publish.</p>
  
            @elseif ($post->status === 'published')
              <div class="adm-danger-box" style="border-color:#fde68a;">
                <h4 style="color:#b45309;">Unpublish this post</h4>
                <p style="margin-bottom:10px;">Hides it from the whole site. The owner keeps it as a draft, sees your reason, and can't publish it again until you restore it.</p>
                <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}"
                      onsubmit="return confirm(@js('Unpublish ' . $post->title . '?'))">
                  @csrf
                  <input type="text" name="reason" class="adm-input" maxlength="200" placeholder="Reason (shown to the owner)" required>
                  <button type="submit" class="adm-btn adm-btn-warn">⏸ Unpublish</button>
                </form>
              </div>
  
            @else
              <p>This post is a draft, so readers can't see it.</p>
            @endif
  
            <div class="adm-danger-box">
              <h4>Delete post</h4>
              <p style="margin-bottom:10px;">Permanently deletes the post and its cover image. This cannot be undone.</p>
              <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                    onsubmit="return confirm(@js('Permanently delete ' . $post->title . '?'))">
                @csrf
                @method('DELETE')
                <button type="submit" class="adm-btn adm-btn-solid">🗑 Delete permanently</button>
              </form>
            </div>
  
          </div>
        </section>
      </aside>
  
    </div>
  </x-admin-layout>