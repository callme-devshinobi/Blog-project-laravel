<x-admin-layout title="Categories">

    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <div class="db-grid">
  
      <section class="db-panel">
        <div class="db-panel-head">
          <h3><span class="db-p-icon">🗂️</span> All categories ({{ $categories->count() }})</h3>
        </div>
  
        @forelse ($categories as $c)
          <div style="padding:18px 24px;border-bottom:1px solid var(--border);">
  
            <form method="POST" action="{{ route('admin.categories.update', $c) }}" class="adm-filter" style="margin-bottom:12px;">
              @csrf
              @method('PUT')
              <input type="text" name="name" value="{{ $c->name }}" maxlength="40" required aria-label="Name" style="flex:1;min-width:140px;">
              <input type="text" name="description" value="{{ $c->description ?? '' }}" maxlength="200" placeholder="Short description" aria-label="Description" style="flex:2;min-width:180px;">
              <button type="submit">Save</button>
            </form>
  
            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
              <span class="adm-badge adm-badge-admin" style="margin:0;">{{ $c->posts_count }} {{ Str::plural('post', $c->posts_count) }}</span>
              <span style="color:var(--text-muted);font-size:13px;">/show/{{ $c->slug }}</span>
  
              @if ($categories->count() > 1)
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="adm-filter" style="margin-left:auto;"
                      onsubmit="return confirm(@js('Delete ' . $c->name . '?' . ($c->posts_count ? ' Its posts will be moved to the category you chose.' : '')))">
                  @csrf
                  @method('DELETE')
  
                  @if ($c->posts_count > 0)
                    <select name="move_to" required aria-label="Move its posts to">
                      <option value="">Move its posts to…</option>
                      @foreach ($categories as $other)
                        @if ($other->id !== $c->id)
                          <option value="{{ $other->id }}">{{ $other->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  @endif
  
                  <button type="submit" style="background:#dc2626;">Delete</button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <p class="db-empty">No categories yet. Add the first one on the right.</p>
        @endforelse
      </section>
  
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">➕</span> Add a category</h3></div>
  
          <form method="POST" action="{{ route('admin.categories.store') }}" class="adm-actions">
            @csrf
            <div>
              <label class="adm-label" for="new_name">Name</label>
              <input type="text" id="new_name" name="name" class="adm-input" maxlength="40" required value="{{ old('name') }}">
            </div>
            <div>
              <label class="adm-label" for="new_description">Short description (optional)</label>
              <input type="text" id="new_description" name="description" class="adm-input" maxlength="200" value="{{ old('description') }}">
            </div>
            <p>Writers can pick it as soon as it's added. Renaming keeps the category's web address, so existing links don't break. Deleting a category with posts needs you to choose where the posts go.</p>
            <button type="submit" class="adm-btn adm-btn-primary">Add category</button>
          </form>
        </section>
      </aside>
  
    </div>
  </x-admin-layout>