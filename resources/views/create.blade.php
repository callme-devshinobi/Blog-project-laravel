<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>New Post – DevNotes</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-dark: #0b0d12;
    --surface: #ffffff;
    --surface-soft: #f7f8fa;
    --text: #111827;
    --text-muted: #6b7280;
    --accent: #6c5ce7;
    --accent-soft: #efeafe;
    --border: #e5e7eb;
    --radius: 12px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', system-ui, sans-serif; color: var(--text); background: var(--surface-soft); line-height: 1.6; }

  /* ---------- Header ---------- */
  .header {
    position: sticky; top: 0; z-index: 50;
    background: rgba(11, 13, 18, 0.9);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
  .header-inner {
    max-width: 1200px; margin: 0 auto; padding: 0 24px;
    height: 64px; display: flex; align-items: center; justify-content: space-between;
  }
  .logo { display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 700; font-size: 19px; text-decoration: none; }
  .logo-mark {
    width: 30px; height: 30px; border-radius: 8px;
    background: linear-gradient(135deg, #6c5ce7, #4834d4);
    display: grid; place-items: center; font-size: 14px; color: #fff; font-weight: 800;
  }
  .logo span em { color: var(--accent); font-style: normal; }
  .nav { display: flex; align-items: center; gap: 34px; }
  .nav a { color: #cbd2dd; text-decoration: none; font-size: 15px; font-weight: 500; transition: color .2s; }
  .nav a:hover { color: #fff; }
  .nav a.active { color: #fff; position: relative; padding-bottom: 6px; }
  .nav a.active::after {
    content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 2px;
    background: var(--accent); border-radius: 2px;
  }
  .header-actions { display: flex; align-items: center; gap: 14px; }
  .icon-btn {
    background: none; border: none; color: #cbd2dd; cursor: pointer; font-size: 17px;
    width: 36px; height: 36px; border-radius: 8px; display: grid; place-items: center;
    transition: background .2s, color .2s;
  }
  .icon-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }
  .btn-subscribe {
    background: var(--accent); color: #fff; border: none; cursor: pointer;
    padding: 9px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;
    font-family: inherit; transition: background .2s, transform .2s;
  }
  .btn-subscribe:hover { background: #5a4bd1; transform: translateY(-1px); }

  /* ---------- Page hero ---------- */
  .page-hero {
    background:
      radial-gradient(900px 400px at 15% 0%, rgba(108,92,231,0.18), transparent 60%),
      radial-gradient(700px 350px at 90% 20%, rgba(72,52,212,0.12), transparent 60%),
      var(--bg-dark);
    color: #fff; padding: 56px 24px 52px;
  }
  .page-hero-inner { max-width: 1080px; margin: 0 auto; }
  .breadcrumb { font-size: 13.5px; color: #8b93a1; margin-bottom: 18px; }
  .breadcrumb a { color: #aeb6c2; text-decoration: none; }
  .breadcrumb a:hover { color: #fff; }
  .breadcrumb span { color: #4b5563; margin: 0 6px; }
  .breadcrumb .current { color: var(--accent); font-weight: 600; }
  .page-hero h1 { font-size: clamp(26px, 4vw, 36px); font-weight: 800; letter-spacing: -0.02em; }
  .page-hero p { color: #aeb6c2; margin-top: 8px; font-size: 15.5px; }

  /* ---------- Layout ---------- */
  .layout {
    max-width: 1080px; margin: 0 auto; padding: 40px 24px 72px;
    display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start;
  }
  @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } }

  .card {
    background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 26px;
  }
  .card + .card { margin-top: 24px; }
  .card h2 {
    font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--text-muted); margin-bottom: 20px;
  }

  /* ---------- Fields ---------- */
  .field { margin-bottom: 18px; }
  .field:last-child { margin-bottom: 0; }
  .field label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 7px; }
  .field label i { color: #dc2626; font-style: normal; }
  .hint { font-weight: 400; color: var(--text-muted); font-size: 12.5px; }
  .field input, .field select, .field textarea {
    width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 10px;
    font-family: inherit; font-size: 14.5px; color: var(--text); background: var(--surface-soft);
    transition: border-color .2s, box-shadow .2s, background .2s;
  }
  .field input:focus, .field select:focus, .field textarea:focus {
    outline: none; border-color: var(--accent); background: #fff;
    box-shadow: 0 0 0 4px rgba(108,92,231,0.14);
  }
  .field textarea { min-height: 340px; resize: vertical; line-height: 1.7; font-family: 'JetBrains Mono', monospace; font-size: 13.5px; }
  .field input.slug { font-family: 'JetBrains Mono', monospace; font-size: 13.5px; color: var(--text-muted); }
  .title-input {
    font-size: 21px !important; font-weight: 700; letter-spacing: -0.01em; padding: 15px 16px !important;
  }

  /* Slug row */
  .slug-row { display: flex; align-items: stretch; }
  .slug-prefix {
    display: flex; align-items: center; padding: 0 14px; background: var(--surface-soft);
    border: 1px solid var(--border); border-right: none; border-radius: 10px 0 0 10px;
    font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--text-muted); white-space: nowrap;
  }
  .slug-row input { border-radius: 0 10px 10px 0 !important; }

  /* Editor toolbar */
  .editor-toolbar {
    display: flex; align-items: center; gap: 4px; flex-wrap: wrap;
    background: var(--surface-soft); border: 1px solid var(--border); border-bottom: none;
    border-radius: 10px 10px 0 0; padding: 8px 10px;
  }
  .tb-btn {
    background: none; border: none; cursor: pointer; font-family: inherit;
    min-width: 32px; height: 32px; border-radius: 7px; display: grid; place-items: center;
    font-size: 13.5px; font-weight: 700; color: var(--text-muted);
    transition: background .15s, color .15s;
  }
  .tb-btn:hover { background: var(--accent-soft); color: var(--accent); }
  .tb-sep { width: 1px; height: 20px; background: var(--border); margin: 0 6px; }
  .editor-wrap textarea { border-radius: 0 0 10px 10px !important; }

  /* Tags input */
  .tags-input {
    display: flex; flex-wrap: wrap; gap: 8px; padding: 10px 12px; min-height: 50px;
    border: 1px solid var(--border); border-radius: 10px; background: var(--surface-soft);
    cursor: text; transition: border-color .2s, box-shadow .2s, background .2s;
  }
  .tags-input:focus-within { border-color: var(--accent); background: #fff; box-shadow: 0 0 0 4px rgba(108,92,231,0.14); }
  .tags-input input { flex: 1; min-width: 120px; border: none !important; background: none !important; box-shadow: none !important; padding: 6px 4px !important; font-size: 14px; }
  .tags-input input:focus { outline: none; }
  .tag-pill {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--accent-soft); color: #4b3bbf; font-size: 13px; font-weight: 600;
    padding: 6px 8px 6px 12px; border-radius: 999px;
  }
  .tag-pill button {
    background: rgba(108,92,231,0.18); border: none; color: #4b3bbf; cursor: pointer;
    width: 18px; height: 18px; border-radius: 50%; font-size: 11px; line-height: 1;
    display: grid; place-items: center; transition: background .15s;
  }
  .tag-pill button:hover { background: rgba(108,92,231,0.35); }

  /* Cover upload */
  .dropzone {
    border: 2px dashed var(--border); border-radius: 12px; padding: 34px 20px;
    text-align: center; cursor: pointer; transition: border-color .2s, background .2s;
    background: var(--surface-soft);
  }
  .dropzone:hover, .dropzone.drag { border-color: var(--accent); background: var(--accent-soft); }
  .dropzone .dz-icon {
    width: 46px; height: 46px; margin: 0 auto 12px; border-radius: 12px;
    background: var(--accent-soft); color: var(--accent);
    display: grid; place-items: center; font-size: 20px;
  }
  .dropzone b { font-size: 14.5px; display: block; margin-bottom: 3px; }
  .dropzone span { font-size: 12.5px; color: var(--text-muted); }
  .dropzone input { display: none; }

  /* Status radios */
  .status-options { display: flex; flex-direction: column; gap: 10px; }
  .status-opt {
    display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px;
    border: 1px solid var(--border); border-radius: 10px; cursor: pointer;
    transition: border-color .2s, background .2s;
  }
  .status-opt:hover { border-color: rgba(108,92,231,0.5); }
  .status-opt input { margin-top: 3px; accent-color: var(--accent); }
  .status-opt b { font-size: 14px; display: block; }
  .status-opt span { font-size: 12.5px; color: var(--text-muted); }

  /* Sidebar */
  .sidebar { display: flex; flex-direction: column; gap: 24px; position: sticky; top: 88px; }
  @media (max-width: 900px) { .sidebar { position: static; } }
  .preview-thumb {
    height: 120px; border-radius: 10px; margin-bottom: 14px;
    background: radial-gradient(160px 160px at 75% 25%, rgba(108,92,231,0.4), transparent 70%), linear-gradient(135deg, #161b26, #0b0d12);
    display: grid; place-items: center; color: var(--accent); font-size: 26px;
  }
  .word-count { font-size: 13px; color: var(--text-muted); margin-top: 10px; }
  .word-count b { color: var(--text); }

  /* Actions */
  .actions { display: flex; flex-direction: column; gap: 10px; }
  .btn {
    width: 100%; padding: 13px; border-radius: 10px; font-family: inherit; font-weight: 700;
    font-size: 14.5px; cursor: pointer; border: none; transition: background .2s, transform .2s;
  }
  .btn-primary { background: var(--accent); color: #fff; }
  .btn-primary:hover { background: #5a4bd1; transform: translateY(-1px); }
  .btn-outline { background: transparent; color: var(--text); border: 1px solid var(--border); }
  .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
  .btn-danger { background: transparent; color: #dc2626; border: 1px solid #fecaca; }
  .btn-danger:hover { background: #fef2f2; }

  /* ---------- Footer ---------- */
  .footer { background: var(--bg-dark); color: #8b93a1; }
  .footer-inner {
    max-width: 1200px; margin: 0 auto; padding: 26px 24px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;
  }
  .footer-inner .logo { font-size: 17px; }
  .footer-inner small { color: #6b7280; font-size: 13.5px; }
  .footer-inner small b { color: #9aa3b0; }
  .socials { display: flex; gap: 16px; }
  .socials a { color: #9aa3b0; text-decoration: none; font-size: 15px; font-weight: 600; transition: color .2s; }
  .socials a:hover { color: #fff; }
</style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="header-inner">
      <a href="#" class="logo"><span class="logo-mark">&lt;/&gt;</span><span>Dev<em>Notes</em></span></a>
      <nav class="nav">
        <a href="#">Home</a>
        <a href="#" class="active">Blog</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
      </nav>
      <div class="header-actions">
        <button class="icon-btn" aria-label="Search">⌕</button>
        <button class="icon-btn" aria-label="Toggle theme">◐</button>
        <button class="btn-subscribe">Subscribe</button>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="page-hero">
    <div class="page-hero-inner">
      <div class="breadcrumb">
        <a href="#">Dashboard</a><span>/</span><a href="#">Posts</a><span>/</span><span class="current">New Post</span>
      </div>
      <h1>Write a new post</h1>
      <p>Draft your next article. It won't be visible on the blog until you publish it.</p>
    </div>
  </section>

  <!-- Layout -->
  <div class="layout">

    <!-- Main column -->
    <div>
      <form action="#" method="POST">

        <!-- Content card -->
        <div class="card">
          <h2>Content</h2>

          <div class="field">
            <label for="title">Title <i>*</i></label>
            <input type="text" id="title" class="title-input" placeholder="e.g. Getting Started with Laravel 12: A Beginner's Guide" required />
          </div>

          <div class="field">
            <label for="slug">Slug</label>
            <div class="slug-row">
              <span class="slug-prefix">devnotes.dev/blog/</span>
              <input type="text" id="slug" class="slug" placeholder="auto-generated-from-title" />
            </div>
            <p class="hint" style="margin-top:6px;">Leave blank to auto-generate from the title.</p>
          </div>

          <div class="field">
            <label for="excerpt">Excerpt</label>
            <input type="text" id="excerpt" maxlength="160" placeholder="A short summary shown on the homepage and in search results…" />
            <p class="hint" style="margin-top:6px;">Max 160 characters.</p>
          </div>

          <div class="field editor-wrap">
            <label for="body">Body <i>*</i> <span class="hint">— Markdown supported</span></label>
            <div class="editor-toolbar" aria-hidden="true">
              <button type="button" class="tb-btn" title="Bold"><b>B</b></button>
              <button type="button" class="tb-btn" title="Italic"><i>I</i></button>
              <button type="button" class="tb-btn" title="Strikethrough"><s>S</s></button>
              <span class="tb-sep"></span>
              <button type="button" class="tb-btn" title="Heading">H2</button>
              <button type="button" class="tb-btn" title="Quote">❝</button>
              <button type="button" class="tb-btn" title="Code">&lt;/&gt;</button>
              <span class="tb-sep"></span>
              <button type="button" class="tb-btn" title="Link">🔗</button>
              <button type="button" class="tb-btn" title="Image">🖼</button>
              <button type="button" class="tb-btn" title="List">•≡</button>
              <button type="button" class="tb-btn" title="Numbered list">1≡</button>
            </div>
            <textarea id="body" placeholder="Start writing… Use ## for headings, ``` for code blocks, and write like you talk." required></textarea>
          </div>
        </div>

        <!-- Tags card -->
        <div class="card">
          <h2>Tags</h2>
          <div class="field">
            <label for="tags">Add tags</label>
            <div class="tags-input" id="tagsBox">
              <span class="tag-pill">laravel <button type="button" aria-label="Remove tag">✕</button></span>
              <span class="tag-pill">php <button type="button" aria-label="Remove tag">✕</button></span>
              <input type="text" id="tags" placeholder="Type and press Enter…" />
            </div>
            <p class="hint" style="margin-top:6px;">Up to 6 tags. They help readers discover related posts.</p>
          </div>
        </div>

      </form>
    </div>

    <!-- Sidebar column -->
    <aside class="sidebar">

      <!-- Publish -->
      <div class="card">
        <h2>Publish</h2>
        <div class="status-options" style="margin-bottom:18px;">
          <label class="status-opt">
            <input type="radio" name="status" value="draft" checked />
            <span><b>📝 Draft</b><span>Save it and come back later.</span></span>
          </label>
          <label class="status-opt">
            <input type="radio" name="status" value="published" />
            <span><b>🚀 Publish now</b><span>Visible on the blog immediately.</span></span>
          </label>
          <label class="status-opt">
            <input type="radio" name="status" value="scheduled" />
            <span><b>🗓 Scheduled</b><span>Publish automatically at a future date.</span></span>
          </label>
        </div>
        <div class="field" id="scheduleField" style="display:none;">
          <label for="publish_at">Publish date</label>
          <input type="datetime-local" id="publish_at" />
        </div>
        <div class="actions">
          <button type="submit" class="btn btn-primary">Save draft</button>
          <button type="button" class="btn btn-outline">Preview →</button>
          <button type="button" class="btn btn-danger">Discard</button>
        </div>
      </div>

      <!-- Category -->
      <div class="card">
        <h2>Category</h2>
        <div class="field">
          <label for="category">Choose a category <i>*</i></label>
          <select id="category" required>
            <option value="">Select category…</option>
            <option selected>Laravel</option>
            <option>Web Development</option>
            <option>JavaScript</option>
            <option>Tools &amp; Resources</option>
            <option>Productivity</option>
            <option>Tech News</option>
          </select>
        </div>
      </div>

      <!-- Cover image -->
      <div class="card">
        <h2>Cover image</h2>
        <label class="dropzone" for="cover">
          <span class="dz-icon">🖼</span>
          <b>Click or drop an image</b>
          <span>JPG, PNG or WebP · max 2MB · 1600×900 recommended</span>
          <input type="file" id="cover" accept="image/*" />
        </label>
        <div class="preview-thumb" style="margin-top:14px;margin-bottom:0;">⬡</div>
        <p class="hint" style="text-align:center;margin-top:8px;">No cover selected — a fallback gradient will be used.</p>
      </div>

      <!-- Meta -->
      <div class="card">
        <h2>Reading</h2>
        <p style="font-size:14px;color:var(--text-muted);">Reading time is calculated automatically from your word count.</p>
        <p class="word-count">Current draft: <b>0 words</b> · ≈ <b>0 min read</b></p>
      </div>

    </aside>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-inner">
      <a href="#" class="logo"><span class="logo-mark">&lt;/&gt;</span><span>Dev<em>Notes</em></span></a>
      <small>© 2025 DevNotes. Built with Laravel. <b>♥</b> by Dennis Onwuegbu</small>
      <div class="socials">
        <a href="#">GitHub</a><a href="#">LinkedIn</a><a href="#">X</a><a href="#">YouTube</a>
      </div>
    </div>
  </footer>

  <script>
    // Show/hide schedule date field
    document.querySelectorAll('input[name="status"]').forEach(radio => {
      radio.addEventListener('change', () => {
        document.getElementById('scheduleField').style.display =
          document.querySelector('input[name="status"]:checked').value === 'scheduled' ? 'block' : 'none';
      });
    });

    // Simple tag input behaviour
    const tagsBox = document.getElementById('tagsBox');
    const tagsInput = document.getElementById('tags');
    function addTag(text) {
      text = text.trim().replace(/^#/, '');
      if (!text) return;
      const pill = document.createElement('span');
      pill.className = 'tag-pill';
      pill.innerHTML = text + ' <button type="button" aria-label="Remove tag">✕</button>';
      tagsBox.insertBefore(pill, tagsInput);
    }
    tagsInput.addEventListener('keydown', e => {
      if ((e.key === 'Enter' || e.key === ',') && tagsInput.value.trim()) {
        e.preventDefault();
        addTag(tagsInput.value);
        tagsInput.value = '';
      } else if (e.key === 'Backspace' && !tagsInput.value) {
        const pills = tagsBox.querySelectorAll('.tag-pill');
        if (pills.length) pills[pills.length - 1].remove();
      }
    });
    tagsBox.addEventListener('click', e => {
      if (e.target.tagName === 'BUTTON') e.target.parentElement.remove();
      else tagsInput.focus();
    });
  </script>

</body>
</html>
