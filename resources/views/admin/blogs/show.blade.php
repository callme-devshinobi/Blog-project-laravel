<x-admin-layout title="Manage blog">

    @php
      $suspendedOn = $blog->suspended_at ? \Illuminate\Support\Carbon::parse($blog->suspended_at)->format('M j, Y · H:i') : null;
      $ownerIsAdmin = (bool) $blog->user?->is_admin;
    @endphp
  
    <p style="margin-bottom:16px;"><a href="{{ route('admin.blogs') }}" style="color:var(--indigo);font-weight:600;">← All blogs</a></p>
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    @if ($blog->suspended_at)
      <div class="adm-banner" role="status">
        <b>This blog is suspended.</b> Readers get a 404, and its custom domain is switched off. Only admins can preview it.
        @if ($blog->suspended_reason) Reason: {{ $blog->suspended_reason }}. @endif
      </div>
    @endif
  
    <div class="db-grid">
  
      {{-- LEFT: details --}}
      <div>
  
        <section class="db-panel">
          <div class="db-panel-head">
            <h3>
              <span class="db-p-icon">📚</span> {{ $blog->title }}
              @if ($blog->suspended_at)<span class="adm-badge adm-badge-susp">Suspended</span>@endif
            </h3>
          </div>
  
          <div class="adm-kv"><span>Address</span><b><a href="{{ route('blog.show', $blog) }}" target="_blank" rel="noopener" style="color:var(--indigo);">{{ '@' . $blog->subdomain }}</a></b></div>
          <div class="adm-kv">
            <span>Owner</span>
            <b>
              @if ($blog->user)
                <a href="{{ route('admin.users.show', $blog->user) }}" style="color:var(--indigo);">{{ $blog->user->name }}</a>
                @if ($ownerIsAdmin)<span class="adm-badge adm-badge-admin">Admin</span>@endif
              @else
                Unknown
              @endif
            </b>
          </div>
          <div class="adm-kv"><span>Created</span><b>{{ $blog->created_at->format('M j, Y') }}</b></div>
          <div class="adm-kv"><span>Posts</span><b>{{ $publishedCount }} published · {{ $draftCount }} {{ Str::plural('draft', $draftCount) }}</b></div>
          <div class="adm-kv">
            <span>Custom domain</span>
            <b>
              @if ($blog->custom_domain)
                {{ $blog->custom_domain }} ({{ $blog->custom_domain_verified_at ? 'verified' : 'not verified' }})
              @else
                None
              @endif
            </b>
          </div>
          @if ($suspendedOn)
            <div class="adm-kv"><span>Suspended on</span><b>{{ $suspendedOn }}</b></div>
            <div class="adm-kv"><span>Reason</span><b>{{ $blog->suspended_reason ?: '—' }}</b></div>
          @endif
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">✎</span> Title and bio</h3></div>
          <form method="POST" action="{{ route('admin.blogs.update', $blog) }}" class="adm-actions">
            @csrf
            @method('PUT')
            <div>
              <label class="adm-label" for="title">Blog name</label>
              <input type="text" id="title" name="title" class="adm-input" maxlength="60" value="{{ old('title', $blog->title) }}" required>
            </div>
            <div>
              <label class="adm-label" for="bio">Bio</label>
              <textarea id="bio" name="bio" class="adm-input" maxlength="300">{{ old('bio', $blog->bio) }}</textarea>
            </div>
            <p>Use this to fix an inappropriate title or bio without suspending the whole blog.</p>
            <button type="submit" class="adm-btn adm-btn-primary">Save changes</button>
          </form>
        </section>
  
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">📄</span> Latest posts</h3></div>
          @forelse ($posts as $p)
            <div class="db-row">
              <div class="db-info">
                <h4>{{ $p->title }}</h4>
                <p><span>{{ $p->created_at->format('M j, Y') }}</span></p>
              </div>
              <span class="adm-badge {{ $p->status === 'published' ? 'adm-badge-live' : 'adm-badge-draft' }}">{{ ucfirst($p->status) }}</span>
              @if ($p->status === 'published')
                <a href="{{ $p->url }}" target="_blank" rel="noopener" class="db-rowlink">View</a>
              @endif
            </div>
          @empty
            <p class="db-empty">No posts yet.</p>
          @endforelse
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
            <p class="db-empty">Nothing has been recorded for this blog yet.</p>
          @endforelse
        </section>
  
      </div>
  
      {{-- RIGHT: actions --}}
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">⚙️</span> Actions</h3></div>
  
          <div class="adm-actions">
  
            @if ($blog->suspended_at)
              <form method="POST" action="{{ route('admin.blogs.unsuspend', $blog) }}">
                @csrf
                <button type="submit" class="adm-btn">✅ Reinstate this blog</button>
              </form>
              <p>Reinstating makes the blog, its posts and its custom domain (if any) work again.</p>
  
            @elseif ($ownerIsAdmin)
              <p>This blog belongs to an admin, so it can't be suspended. Remove their admin rights first.</p>
  
            @else
              <div class="adm-danger-box" style="border-color:#fde68a;">
                <h4 style="color:#b45309;">Suspend this blog</h4>
                <p style="margin-bottom:10px;">Hides the blog and all its posts from the whole site (home, search, categories, directory) and switches off its custom domain. The owner can still sign in and will see your reason.</p>
  
                <form method="POST" action="{{ route('admin.blogs.suspend', $blog) }}"
                      onsubmit="return confirm(@js('Suspend ' . $blog->title . '? It and all its posts will be hidden from readers.'))">
                  @csrf
                  <input type="text" name="reason" class="adm-input" maxlength="200" placeholder="Reason (shown to the owner)" required>
                  <button type="submit" class="adm-btn adm-btn-warn">⏸ Suspend blog</button>
                </form>
              </div>
            @endif
  
            @if ($blog->custom_domain)
              <form method="POST" action="{{ route('admin.blogs.domain.destroy', $blog) }}"
                    onsubmit="return confirm(@js('Remove the custom domain ' . $blog->custom_domain . '?'))">
                @csrf
                @method('DELETE')
                <button type="submit" class="adm-btn adm-btn-danger">🔗 Remove custom domain</button>
              </form>
            @endif
  
            <p>To delete a blog together with its posts, delete the owner's account from the Users page.</p>
  
          </div>
        </section>
      </aside>
  
    </div>
  </x-admin-layout>