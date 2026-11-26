<x-admin-layout title="General settings">

    @php $colors = config('devnotes.site.colors'); @endphp
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <div class="db-grid">
  
      <div>
  
        {{-- Name, logo, footer --}}
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">🏷️</span> Name, logo and footer</h3></div>
  
          <form method="POST" action="{{ route('admin.general.branding') }}" enctype="multipart/form-data" class="adm-actions">
            @csrf
            @method('PUT')
  
            <div>
              <label class="adm-label" for="site_name">Site name</label>
              <input type="text" id="site_name" name="site_name" class="adm-input" maxlength="40" required
                     value="{{ old('site_name', config('devnotes.site.name')) }}">
              <p>Shown in the header, the footer and the browser tab.</p>
            </div>
  
            <div>
              <label class="adm-label" for="site_footer">Footer text</label>
              <input type="text" id="site_footer" name="site_footer" class="adm-input" maxlength="120"
                     value="{{ old('site_footer', config('devnotes.site.footer')) }}">
              <p>Shown after "© year, site name". Leave it empty to use the default text.</p>
            </div>
  
            <div>
              <label class="adm-label" for="logo">Logo</label>
              @if ($logoUrl)
                <div style="margin-bottom:10px;padding:12px;background:var(--bg-dark);border-radius:10px;display:inline-block;">
                  <img src="{{ $logoUrl }}" alt="Current logo" style="height:30px;width:auto;display:block;">
                </div>
              @endif
              <input type="file" id="logo" name="logo" class="adm-input" accept="image/png,image/jpeg,image/webp">
              <p>PNG, JPG or WebP, up to 1 MB. About 30 pixels tall works best. The logo sits on the dark header. SVG isn't allowed, for safety.</p>
              @if ($logoUrl)
                <label style="display:flex;gap:8px;align-items:center;font-size:14px;margin-top:6px;">
                  <input type="checkbox" name="remove_logo" value="1"> Remove the logo and use the default icon
                </label>
              @endif
            </div>
  
            <button type="submit" class="adm-btn adm-btn-primary">Save</button>
          </form>
        </section>
  
        {{-- Colours --}}
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🎨</span> Colors</h3></div>
  
          <form method="POST" action="{{ route('admin.general.colors') }}" class="adm-actions">
            @csrf
            @method('PUT')
  
            <div>
              <label class="adm-label" for="color_brand">Accent color</label>
              <input type="color" id="color_brand" name="color_brand" value="{{ old('color_brand', $colors['brand']) }}" style="width:70px;height:40px;border:none;background:none;">
              <p>Buttons, links, badges and highlights.</p>
            </div>
  
            <div>
              <label class="adm-label" for="color_header">Header and footer color</label>
              <input type="color" id="color_header" name="color_header" value="{{ old('color_header', $colors['header']) }}" style="width:70px;height:40px;border:none;background:none;">
              <p>Must be dark, because the text on it is white.</p>
            </div>
  
            <div>
              <label class="adm-label" for="color_page">Page background</label>
              <input type="color" id="color_page" name="color_page" value="{{ old('color_page', $colors['page']) }}" style="width:70px;height:40px;border:none;background:none;">
              <p>Must be light, because the text on it is dark. Dark mode keeps its own background.</p>
            </div>
  
            <button type="submit" class="adm-btn adm-btn-primary">Save colors</button>
          </form>
  
          <form method="POST" action="{{ route('admin.general.colors.reset') }}" class="adm-actions" style="padding-top:0;"
                onsubmit="return confirm('Put the original colors back?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="adm-btn">Reset to the original colors</button>
          </form>
        </section>
  
        {{-- Hero --}}
        <section class="db-panel" style="margin-top:24px;">
          <div class="db-panel-head"><h3><span class="db-p-icon">🖼️</span> Home page hero</h3></div>
  
          <form method="POST" action="{{ route('admin.general.hero') }}" enctype="multipart/form-data" class="adm-actions">
            @csrf
            @method('PUT')
  
            <div>
              <label class="adm-label" for="hero_eyebrow">Small line above the title</label>
              <input type="text" id="hero_eyebrow" name="hero_eyebrow" class="adm-input" maxlength="60"
                     value="{{ old('hero_eyebrow', config('devnotes.site.hero_eyebrow')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="hero_title">Title</label>
              <input type="text" id="hero_title" name="hero_title" class="adm-input" maxlength="120" required
                     value="{{ old('hero_title', config('devnotes.site.hero_title')) }}">
            </div>
  
            <div>
              <label class="adm-label" for="hero_desc">Description</label>
              <textarea id="hero_desc" name="hero_desc" class="adm-input" maxlength="300">{{ old('hero_desc', config('devnotes.site.hero_desc')) }}</textarea>
            </div>
  
            <div>
              <label class="adm-label" for="hero_image">Image</label>
              <img src="{{ $heroUrl }}" alt="Current hero image" style="width:100%;max-width:360px;border-radius:10px;margin-bottom:10px;display:block;">
              <input type="file" id="hero_image" name="hero_image" class="adm-input" accept="image/png,image/jpeg,image/webp">
              <p>PNG, JPG or WebP, up to 3 MB. A wide image, about 1600 by 1000 pixels, works best.</p>
              @if ($hasCustomHero)
                <label style="display:flex;gap:8px;align-items:center;font-size:14px;margin-top:6px;">
                  <input type="checkbox" name="reset_hero_image" value="1"> Go back to the default image
                </label>
              @endif
            </div>
  
            <p>This is the main site's home page. Each blog has its own hero text, but it uses this image.</p>
            <button type="submit" class="adm-btn adm-btn-primary">Save hero</button>
          </form>
        </section>
  
      </div>
  
      {{-- RIGHT: live colour preview --}}
      <aside class="db-right">
        <section class="db-panel">
          <div class="db-panel-head"><h3><span class="db-p-icon">👁</span> Color preview</h3></div>
          <div style="padding:20px 24px;">
            <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;">
              <div id="pvHeader" style="background:{{ $colors['header'] }};padding:14px 18px;display:flex;justify-content:space-between;align-items:center;">
                <b style="color:#fff;">{{ config('devnotes.site.name') }}</b>
                <span id="pvButton" style="background:{{ $colors['brand'] }};color:#fff;padding:6px 14px;border-radius:8px;font-size:13px;font-weight:700;">Sign up</span>
              </div>
              <div id="pvPage" style="background:{{ $colors['page'] }};padding:18px;color:#191a2e;font-size:14px;">
                <p style="margin-bottom:8px;">This is how page text looks on the background.</p>
                <a id="pvLink" style="color:{{ $colors['brand'] }};font-weight:700;">A link or highlight</a>
              </div>
            </div>
            <p style="color:var(--text-muted);font-size:13px;margin-top:12px;">Updates as you pick colors. Nothing is saved until you click Save colors.</p>
          </div>
        </section>
      </aside>
  
    </div>
  
    <script>
      (function () {
        const brand  = document.getElementById('color_brand');
        const header = document.getElementById('color_header');
        const page   = document.getElementById('color_page');
  
        function paint() {
          document.getElementById('pvHeader').style.background = header.value;
          document.getElementById('pvButton').style.background = brand.value;
          document.getElementById('pvPage').style.background   = page.value;
          document.getElementById('pvLink').style.color        = brand.value;
        }
  
        [brand, header, page].forEach(el => el.addEventListener('input', paint));
      })();
    </script>
  
  </x-admin-layout>