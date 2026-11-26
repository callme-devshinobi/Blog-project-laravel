<x-admin-layout title="About page">

    @include('admin.pages._tabs')
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <form method="POST" action="{{ route('admin.pages.about.save') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
  
      <section class="db-panel">
        <div class="db-panel-head"><h3><span class="db-p-icon">🏷️</span> Top of the page</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="eyebrow">Small line above the title</label>
            <input type="text" id="eyebrow" name="eyebrow" class="adm-input" maxlength="60" value="{{ old('eyebrow', $c['eyebrow']) }}">
          </div>
          <div>
            <label class="adm-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="adm-input" maxlength="140" required value="{{ old('title', $c['title']) }}">
          </div>
          <div>
            <label class="adm-label" for="intro">Introduction</label>
            <textarea id="intro" name="intro" class="adm-input" maxlength="500">{{ old('intro', $c['intro']) }}</textarea>
          </div>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">👤</span> About the author</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="author_heading">Heading</label>
            <input type="text" id="author_heading" name="author_heading" class="adm-input" maxlength="100" value="{{ old('author_heading', $c['author_heading']) }}">
          </div>
          <div>
            <label class="adm-label" for="lead">Opening line</label>
            <textarea id="lead" name="lead" class="adm-input" maxlength="400">{{ old('lead', $c['lead']) }}</textarea>
          </div>
          <div>
            <label class="adm-label" for="body">Story (leave a blank line between paragraphs)</label>
            <textarea id="body" name="body" class="adm-input" rows="7" maxlength="3000">{{ old('body', $c['body']) }}</textarea>
          </div>
  
          <div>
            <label class="adm-label">Skills or topics (shown as chips)</label>
            @include('admin.pages._list', [
                'name'     => 'chips',
                'rows'     => array_map(fn ($v) => ['v' => $v], $c['chips'] ?? []),
                'fields'   => [['key' => 'v', 'label' => 'Name', 'max' => 30]],
                'addLabel' => 'Add a chip',
                'max'      => 12,
            ])
          </div>
  
          <div>
            <label class="adm-label" for="portrait_name">Name under the photo</label>
            <input type="text" id="portrait_name" name="portrait_name" class="adm-input" maxlength="60" value="{{ old('portrait_name', $c['portrait_name']) }}">
          </div>
          <div>
            <label class="adm-label" for="portrait_role">Role under the name</label>
            <input type="text" id="portrait_role" name="portrait_role" class="adm-input" maxlength="60" value="{{ old('portrait_role', $c['portrait_role']) }}">
          </div>
          <div>
            <label class="adm-label" for="portrait">Photo</label>
            @if ($portraitUrl)
              <img src="{{ $portraitUrl }}" alt="Current photo" style="width:160px;border-radius:10px;margin-bottom:10px;display:block;">
            @endif
            <input type="file" id="portrait" name="portrait" class="adm-input" accept="image/png,image/jpeg,image/webp">
            <p>PNG, JPG or WebP, up to 2 MB, roughly 4:3 in shape. Without a photo, the page shows the original dark panel.</p>
            @if ($portraitUrl)
              <label style="display:flex;gap:8px;align-items:center;font-size:14px;">
                <input type="checkbox" name="remove_portrait" value="1"> Remove the photo
              </label>
            @endif
          </div>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">📊</span> Numbers</h3></div>
        <div class="adm-actions">
          @include('admin.pages._list', [
              'name'     => 'stats',
              'rows'     => $c['stats'] ?? [],
              'fields'   => [
                  ['key' => 'value', 'label' => 'Number or text', 'max' => 20],
                  ['key' => 'label', 'label' => 'Label', 'max' => 40],
              ],
              'addLabel' => 'Add a number',
              'max'      => 4,
          ])
          <p>Type any text, or use <b>{posts}</b>, <b>{categories}</b>, <b>{blogs}</b> or <b>{writers}</b> to show a live count that updates by itself.</p>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">🧭</span> What readers will find</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="find_heading">Heading</label>
            <input type="text" id="find_heading" name="find_heading" class="adm-input" maxlength="80" value="{{ old('find_heading', $c['find_heading']) }}">
          </div>
          @include('admin.pages._list', [
              'name'     => 'find',
              'rows'     => $c['find'] ?? [],
              'fields'   => [
                  ['key' => 'tag',   'label' => 'Small label (like TUTORIALS)', 'max' => 24],
                  ['key' => 'title', 'label' => 'Title', 'max' => 80],
                  ['key' => 'text',  'label' => 'Text', 'type' => 'textarea', 'max' => 300],
              ],
              'addLabel' => 'Add an item',
              'max'      => 6,
          ])
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">💡</span> Why you write</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="why_heading">Heading</label>
            <input type="text" id="why_heading" name="why_heading" class="adm-input" maxlength="80" value="{{ old('why_heading', $c['why_heading']) }}">
          </div>
          <div>
            <label class="adm-label" for="why_body">Text (blank line between paragraphs)</label>
            <textarea id="why_body" name="why_body" class="adm-input" rows="6" maxlength="2000">{{ old('why_body', $c['why_body']) }}</textarea>
          </div>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">📣</span> Call to action (the dark box at the bottom)</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="cta_title">Title</label>
            <input type="text" id="cta_title" name="cta_title" class="adm-input" maxlength="100" value="{{ old('cta_title', $c['cta_title']) }}">
          </div>
          <div>
            <label class="adm-label" for="cta_text">Text</label>
            <textarea id="cta_text" name="cta_text" class="adm-input" maxlength="300">{{ old('cta_text', $c['cta_text']) }}</textarea>
          </div>
          <div>
            <label class="adm-label" for="cta_primary_label">First button: label</label>
            <input type="text" id="cta_primary_label" name="cta_primary_label" class="adm-input" maxlength="40" value="{{ old('cta_primary_label', $c['cta_primary_label']) }}">
          </div>
          <div>
            <label class="adm-label" for="cta_primary_url">First button: link</label>
            <input type="text" id="cta_primary_url" name="cta_primary_url" class="adm-input" maxlength="200" placeholder="https://… or /contact" value="{{ old('cta_primary_url', $c['cta_primary_url']) }}">
            <p>A button with no link isn't shown.</p>
          </div>
          <div>
            <label class="adm-label" for="cta_secondary_label">Second button: label</label>
            <input type="text" id="cta_secondary_label" name="cta_secondary_label" class="adm-input" maxlength="40" value="{{ old('cta_secondary_label', $c['cta_secondary_label']) }}">
          </div>
          <div>
            <label class="adm-label" for="cta_secondary_url">Second button: link</label>
            <input type="text" id="cta_secondary_url" name="cta_secondary_url" class="adm-input" maxlength="200" placeholder="https://… or /contact" value="{{ old('cta_secondary_url', $c['cta_secondary_url']) }}">
          </div>
        </div>
      </section>
  
      <div style="margin-top:20px;">
        <button type="submit" class="adm-btn adm-btn-primary">Save About page</button>
      </div>
    </form>
  
    <form method="POST" action="{{ route('admin.pages.reset', 'about') }}" style="margin-top:14px;"
          onsubmit="return confirm('Put the original About text back? Your changes to the text will be lost.')">
      @csrf
      @method('DELETE')
      <button type="submit" class="adm-btn">Put the original text back</button>
    </form>
  
  </x-admin-layout>