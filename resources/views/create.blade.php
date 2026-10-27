<x-layout>
  <div class="create-post">
  
    {{-- Hero --}}
    <section class="post-hero">
      <div class="post-hero-inner">
        <div class="post-crumb">
          <a href="/">Home</a><span>/</span><a href="/posts">Posts</a><span>/</span><span class="current">New Post</span>
        </div>
        <h1>Write a new post</h1>
        <p>Draft your next article. It won't be visible on the blog until you publish it.</p>
      </div>
    </section>
  
    {{-- The form wraps BOTH columns so the sidebar's Save button works --}}
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="post-layout">
      @csrf
  
      {{-- Main column --}}
      <div>
        @if (session('success'))
          <div class="alert-success">{{ session('success') }}</div>
        @endif
  
        <div class="card">
          <h2>Content</h2>
  
          <div class="field">
            <label for="title">Title <i>*</i></label>
            <input type="text" id="title" name="title" class="title-input"
                   value="{{ old('title') }}"
                   placeholder="e.g. Getting Started with Laravel 12: A Beginner's Guide" required>
            @error('title') <p class="error">{{ $message }}</p> @enderror
          </div>
  
          <div class="field">
            <label for="excerpt">Excerpt</label>
            <input type="text" id="excerpt" name="excerpt" maxlength="300"
                   value="{{ old('excerpt') }}"
                   placeholder="A short summary shown on the homepage and in search results…">
            <p class="hint">Max 300 characters.</p>
            @error('excerpt') <p class="error">{{ $message }}</p> @enderror
          </div>
  
          <div class="field">
            <label for="body">Body <i>*</i></label>
            <textarea id="body" name="body" placeholder="Start writing… Press Enter twice to start a new paragraph." required>{{ old('body') }}</textarea>
            @error('body') <p class="error">{{ $message }}</p> @enderror
          </div>
        </div>
  
        <div class="card">
          <h2>Tags</h2>
          <div class="field">
            <label for="tags">Add tags</label>
            <div class="tags-input" id="tagsBox">
              @foreach (array_filter(explode(',', old('tags', ''))) as $tag)
                <span class="tag-pill" data-tag="{{ $tag }}">{{ $tag }} <button type="button" aria-label="Remove tag">✕</button></span>
              @endforeach
              <input type="text" id="tags" placeholder="Type and press Enter…">
            </div>
            {{-- This hidden input is what actually gets sent to the controller --}}
            <input type="hidden" name="tags" id="tagsValue" value="{{ old('tags') }}">
            <p class="hint">Up to 6 tags. They help readers discover related posts.</p>
            @error('tags') <p class="error">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>
  
      {{-- Sidebar column --}}
      <aside class="post-side">
  
        {{-- Publish --}}
        <div class="card">
          <h2>Publish</h2>
          <div class="status-options">
            <label class="status-opt">
              <input type="radio" name="status" value="draft" @checked(old('status', 'draft') === 'draft')>
              <span><b>📝 Draft</b><small>Save it and come back later.</small></span>
            </label>
            <label class="status-opt">
              <input type="radio" name="status" value="published" @checked(old('status') === 'published')>
              <span><b>🚀 Publish now</b><small>Visible on the blog immediately.</small></span>
            </label>
          </div>
          @error('status') <p class="error">{{ $message }}</p> @enderror
  
          <div class="actions">
            <button type="submit" class="btn btn-primary" id="submitBtn">Save draft</button>
            <a href="{{ route('posts.published') }}" class="btn btn-outline">View published</a>
            <a href="{{ route('posts.drafts') }}" class="btn btn-outline">View drafts</a>
            <a href="/posts" class="btn btn-danger">Discard</a>
          </div>
        </div>
  
        {{-- Category --}}
        <div class="card">
          <h2>Category</h2>
          <div class="field">
            <label for="category_id">Choose a category <i>*</i></label>
            <select id="category_id" name="category_id" required>
              <option value="">Choose a category</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            @error('category_id') <p class="error">{{ $message }}</p> @enderror
          </div>
        </div>
  
        {{-- Cover image --}}
        <div class="card">
          <h2>Cover image</h2>
          <label class="dropzone" for="cover">
            <span class="dz-icon">🖼</span>
            <b>Click to choose an image</b>
            <small>JPG, PNG or WebP · max 2MB · 1600×900 recommended</small>
            <input type="file" id="cover" name="featured_image" accept="image/*">
          </label>
          <div class="preview-thumb" id="thumb">⬡</div>
          <p class="hint" id="thumbHint" style="text-align:center;">No cover selected, so a placeholder image will be used.</p>
          @error('featured_image') <p class="error">{{ $message }}</p> @enderror
  
          <div class="field" style="margin-top:18px;">
            <label for="image_alt">Image description <span class="hint">(for accessibility)</span></label>
            <input type="text" id="image_alt" name="image_alt" value="{{ old('image_alt') }}"
                   placeholder="e.g. Laptop showing Laravel code">
            @error('image_alt') <p class="error">{{ $message }}</p> @enderror
          </div>
        </div>
  
        {{-- Reading time --}}
        <div class="card">
          <h2>Reading</h2>
          <p style="font-size:14px;color:var(--text-muted);">Reading time is calculated automatically from your word count.</p>
          <p class="word-count">Current draft: <b id="wc">0 words</b> · ≈ <b id="rt">1 min read</b></p>
        </div>
  
      </aside>
    </form>
  
    <script>
      // ---------- Tags ----------
      const tagsBox   = document.getElementById('tagsBox');
      const tagsInput = document.getElementById('tags');
      const tagsValue = document.getElementById('tagsValue');
  
      function currentTags() {
        return [...tagsBox.querySelectorAll('.tag-pill')].map(p => p.dataset.tag);
      }
      function syncTags() {
        tagsValue.value = currentTags().join(',');
      }
      function addTag(text) {
        text = text.trim().replace(/^#/, '').toLowerCase();
        if (!text || currentTags().includes(text) || currentTags().length >= 6) return;
        const pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.dataset.tag = text;
        pill.append(text + ' ');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Remove tag');
        btn.textContent = '✕';
        pill.append(btn);
        tagsBox.insertBefore(pill, tagsInput);
        syncTags();
      }
      tagsInput.addEventListener('keydown', e => {
        if ((e.key === 'Enter' || e.key === ',') && tagsInput.value.trim()) {
          e.preventDefault();
          addTag(tagsInput.value);
          tagsInput.value = '';
        } else if (e.key === 'Enter') {
          e.preventDefault(); // stops Enter from submitting the whole form
        } else if (e.key === 'Backspace' && !tagsInput.value) {
          const pills = tagsBox.querySelectorAll('.tag-pill');
          if (pills.length) { pills[pills.length - 1].remove(); syncTags(); }
        }
      });
      tagsBox.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON') { e.target.parentElement.remove(); syncTags(); }
        else tagsInput.focus();
      });
      // If someone typed a tag but didn't press Enter, still keep it on submit
      document.querySelector('.post-layout').addEventListener('submit', () => {
        if (tagsInput.value.trim()) { addTag(tagsInput.value); tagsInput.value = ''; }
      });
  
      // ---------- Cover image preview ----------
      const cover = document.getElementById('cover');
      const thumb = document.getElementById('thumb');
      const thumbHint = document.getElementById('thumbHint');
      cover.addEventListener('change', () => {
        const file = cover.files[0];
        if (!file) return;
        thumb.style.backgroundImage = 'url(' + URL.createObjectURL(file) + ')';
        thumb.textContent = '';
        thumbHint.textContent = file.name;
      });
  
      // ---------- Live word count + reading time ----------
      const body = document.getElementById('body');
      function updateCount() {
        const words = body.value.trim() ? body.value.trim().split(/\s+/).length : 0;
        document.getElementById('wc').textContent = words + (words === 1 ? ' word' : ' words');
        document.getElementById('rt').textContent = Math.max(1, Math.ceil(words / 200)) + ' min read';
      }
      body.addEventListener('input', updateCount);
      updateCount();
  
      // ---------- Button label follows the chosen status ----------
      const submitBtn = document.getElementById('submitBtn');
      function updateButton() {
        const status = document.querySelector('input[name="status"]:checked').value;
        submitBtn.textContent = status === 'published' ? 'Publish post' : 'Save draft';
      }
      document.querySelectorAll('input[name="status"]').forEach(r => r.addEventListener('change', updateButton));
      updateButton();
    </script>
  
  </div>
  </x-layout>