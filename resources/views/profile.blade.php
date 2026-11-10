<x-dashboard-layout title="Profile">
    <div class="profile-page">
    
    <style>
      .profile-page {
        --primary:#4f46e5; --primary-dark:#4338ca; --primary-soft:#eef2ff; --bg:#f5f6fa;
        --surface:#fff; --text:#111827; --muted:#6b7280; --border:#e9eaf2; --radius:14px;
      }
      .pf-intro { color: var(--muted); font-size: 14.5px; margin-bottom: 22px; }
    
      .pf-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; }
      .pf-alert-ok  { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
      .pf-alert-err { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
      .pf-alert ul  { margin: 6px 0 0 18px; }
    
      .pf-grid { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
      @media (max-width: 1000px) { .pf-grid { grid-template-columns: 1fr; } }
    
      .pf-panel { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
      .pf-panel + .pf-panel { margin-top: 24px; }
      .pf-title { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
                  color: var(--muted); margin-bottom: 22px; display: flex; align-items: center; gap: 9px; }
    
      /* Photo */
      .pf-avatar-row { display: flex; align-items: center; gap: 22px; margin-bottom: 26px; flex-wrap: wrap; }
      .pf-avatar { width: 92px; height: 92px; border-radius: 50%; flex-shrink: 0;
                   background: linear-gradient(135deg, #6366f1, #4338ca) center/cover no-repeat;
                   display: grid; place-items: center; color: #fff; font-weight: 800; font-size: 32px;
                   border: 4px solid var(--primary-soft); box-shadow: 0 6px 16px rgba(79,70,229,.25); }
      .pf-actions b { font-size: 15px; display: block; margin-bottom: 4px; }
      .pf-actions p { color: var(--muted); font-size: 13px; margin-bottom: 12px; }
      .pf-btns { display: flex; gap: 10px; flex-wrap: wrap; }
      .pf-btn-sm { padding: 9px 18px; border-radius: 9px; font-family: inherit; font-size: 13.5px;
                   font-weight: 600; cursor: pointer; border: none; transition: background .2s; }
      .pf-upload { background: var(--primary); color: #fff; }
      .pf-upload:hover { background: var(--primary-dark); }
      .pf-remove { background: #fff; color: #dc2626; border: 1px solid #fecaca; }
      .pf-remove:hover { background: #fef2f2; }
      #avatarInput { display: none; }
    
      /* Fields */
      .pf-field { margin-bottom: 18px; }
      .pf-field:last-child { margin-bottom: 0; }
      .pf-field label { display: block; font-size: 13.5px; font-weight: 600; margin-bottom: 7px; }
      .pf-field label i { color: #dc2626; font-style: normal; }
      .pf-hint { font-weight: 400; color: var(--muted); font-size: 12.5px; margin-top: 6px; }
      .pf-field input[type=text], .pf-field input[type=email], .pf-field input[type=tel], .pf-field textarea {
        width: 100%; padding: 12px 14px 12px 42px; border: 1px solid var(--border); border-radius: 10px;
        font-family: inherit; font-size: 14.5px; color: var(--text); background: var(--bg);
        transition: border-color .2s, box-shadow .2s, background .2s; }
      .pf-field textarea { padding-left: 14px; min-height: 110px; resize: vertical; line-height: 1.65; }
      .pf-field input:focus, .pf-field textarea:focus { outline: none; border-color: var(--primary);
        background: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
      .pf-input { position: relative; }
      .pf-lead { position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
                 color: var(--muted); font-size: 14px; pointer-events: none; width: 16px; text-align: center; }
      .pf-count { text-align: right; font-size: 12px; color: var(--muted); margin-top: 5px; }
    
      /* Tags */
      .pf-tags { display: flex; flex-wrap: wrap; gap: 8px; padding: 10px 12px; min-height: 50px;
                 border: 1px solid var(--border); border-radius: 10px; background: var(--bg); cursor: text;
                 transition: border-color .2s, box-shadow .2s, background .2s; }
      .pf-tags:focus-within { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
      .pf-tags input[type=text] { flex: 1; min-width: 120px; border: none !important; background: none !important;
                 box-shadow: none !important; padding: 6px 4px !important; font-size: 14px; }
      .pf-pill { display: inline-flex; align-items: center; gap: 7px; background: var(--primary-soft);
                 color: var(--primary-dark); font-size: 13px; font-weight: 600; padding: 6px 8px 6px 12px; border-radius: 999px; }
      .pf-pill button { background: rgba(79,70,229,.15); border: none; color: var(--primary-dark); cursor: pointer;
                 width: 18px; height: 18px; border-radius: 50%; font-size: 11px; line-height: 1; display: grid; place-items: center; }
      .pf-pill button:hover { background: rgba(79,70,229,.3); }
    
      .pf-contact { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
      @media (max-width: 640px) { .pf-contact { grid-template-columns: 1fr; } }
    
      /* Account box */
      .pf-info-row { display: flex; align-items: center; justify-content: space-between; gap: 12px;
                     padding: 13px 4px; border-bottom: 1px solid var(--border); font-size: 14px; }
      .pf-info-row:last-of-type { border-bottom: none; }
      .pf-info-row span:first-child { color: var(--muted); }
      .pf-info-row b { font-weight: 600; text-align: right; word-break: break-all; }
      .pf-badge { width: 34px; height: 34px; border-radius: 10px; background: var(--primary-soft); color: var(--primary);
                  display: inline-grid; place-items: center; font-weight: 800; font-size: 13px; }
    
      /* Save bar */
      .pf-save { margin-top: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
      .pf-save small { color: var(--muted); font-size: 13px; }
      .pf-save .pf-save-btns { display: flex; gap: 12px; }
      .pf-primary { background: var(--primary); color: #fff; border: none; padding: 13px 30px; border-radius: 10px;
                    font-family: inherit; font-weight: 700; font-size: 15px; cursor: pointer; transition: background .2s, transform .2s; }
      .pf-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
      .pf-ghost { background: #fff; color: var(--text); border: 1px solid var(--border); padding: 13px 24px; border-radius: 10px;
                  font-family: inherit; font-weight: 600; font-size: 14.5px; cursor: pointer; text-decoration: none; display: inline-block; }
      .pf-ghost:hover { border-color: var(--primary); color: var(--primary); }
    </style>
    
    <p class="pf-intro">This is how you appear to readers on your blog.</p>
    
    @if (session('success'))
      <div class="pf-alert pf-alert-ok">{{ session('success') }}</div>
    @endif
    
    @if ($errors->any())
      <div class="pf-alert pf-alert-err" role="alert">
        <b>Please fix the following:</b>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
      @csrf
      @method('PUT')
      <input type="hidden" name="remove_avatar" id="removeAvatar" value="0">
    
      <div class="pf-grid">
    
        <!-- LEFT: editable form -->
        <div>
    
          <!-- Picture + name -->
          <section class="pf-panel">
            <div class="pf-title">👤 Profile Picture &amp; Name</div>
    
            <div class="pf-avatar-row">
              <div class="pf-avatar" id="avatarPreview"
                   @if ($user->avatar_url) style="background-image:url('{{ $user->avatar_url }}')" @endif>
                @unless ($user->avatar_url){{ $user->initials }}@endunless
              </div>
              <div class="pf-actions">
                <b>Your photo</b>
                <p>JPG, PNG or WebP · max 2MB · square works best</p>
                <div class="pf-btns">
                  <label class="pf-btn-sm pf-upload" for="avatarInput" style="cursor:pointer;">Upload photo</label>
                  <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp">
                  <button type="button" class="pf-btn-sm pf-remove" id="removePhoto">Remove</button>
                </div>
              </div>
            </div>
    
            <div class="pf-field">
              <label for="name">Full name <i>*</i></label>
              <div class="pf-input">
                <span class="pf-lead">👤</span>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
              </div>
              <p class="pf-hint">First and last name together. Your initials update automatically.</p>
            </div>
            <div class="pf-field">
              <label for="blog_title">Blog name</label>
              <div class="pf-input">
                <span class="pf-lead">📝</span>
                <input type="text" id="blog_title" name="blog_title" maxlength="60"
                       value="{{ old('blog_title', $blog->title) }}"
                       placeholder="{{ $user->name }}'s Blog">
              </div>
              <p class="pf-hint">Shown in your blog's header, footer and browser tab. Leave it empty to use "<span id="defaultTitle">{{ $user->name }}</span>'s Blog".</p>
            </div>
          </section>
    
          <!-- About -->
          <section class="pf-panel">
            <div class="pf-title">✎ About</div>
    
            <div class="pf-field">
              <label for="bio">Bio</label>
              <textarea id="bio" name="bio" maxlength="300" placeholder="A short intro that appears on your blog…">{{ old('bio', $blog->bio) }}</textarea>
              <p class="pf-count"><span id="bioCount">{{ mb_strlen(old('bio', $blog->bio ?? '')) }}</span>/300</p>
            </div>
    
            <div class="pf-field">
              <label for="stackInput">Tech stack</label>
              <div class="pf-tags" id="stackBox">
                @foreach (old('tech_stack', $blog->tech_stack ?? []) as $tag)
                  <span class="pf-pill">{{ $tag }}<input type="hidden" name="tech_stack[]" value="{{ $tag }}"><button type="button" aria-label="Remove">✕</button></span>
                @endforeach
                <input type="text" id="stackInput" placeholder="Type and press Enter…" autocomplete="off">
              </div>
              <p class="pf-hint">Up to 15. Shown as chips on your blog.</p>
            </div>
    
            <div class="pf-field">
              <label for="write_about">What you write about</label>
              <textarea id="write_about" name="write_about" placeholder="e.g. Laravel tutorials, honest career lessons, and tools that make development more enjoyable…">{{ old('write_about', $blog->write_about) }}</textarea>
            </div>
    
            <div class="pf-field">
              <label for="why_write">Why you write</label>
              <textarea id="why_write" name="why_write" placeholder="e.g. Teaching is the best form of learning…">{{ old('why_write', $blog->why_write) }}</textarea>
            </div>
          </section>
    
          <!-- Contact -->
          <section class="pf-panel">
            <div class="pf-title">✉ Contact</div>
            <p class="pf-hint" style="margin:-10px 0 18px;">Everything here is shown publicly on your blog's contact page. Leave a field empty to hide it.</p>
            <div class="pf-contact">
              <div class="pf-field">
                <label for="contact_email">Email</label>
                <div class="pf-input">
                  <span class="pf-lead">✉</span>
                  <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $blog->contact_email) }}">
                </div>
              </div>
              <div class="pf-field">
                <label for="phone">Phone number</label>
                <div class="pf-input">
                  <span class="pf-lead">📞</span>
                  <input type="tel" id="phone" name="phone" value="{{ old('phone', $blog->phone) }}" placeholder="+234 800 000 0000">
                </div>
              </div>
              <div class="pf-field">
                <label for="github">GitHub</label>
                <div class="pf-input">
                  <span class="pf-lead">⌥</span>
                  <input type="text" id="github" name="github" value="{{ old('github', $blog->github) }}" placeholder="github.com/username">
                </div>
              </div>
              <div class="pf-field">
                <label for="linkedin">LinkedIn</label>
                <div class="pf-input">
                  <span class="pf-lead">in</span>
                  <input type="text" id="linkedin" name="linkedin" value="{{ old('linkedin', $blog->linkedin) }}" placeholder="linkedin.com/in/username">
                </div>
              </div>
              <div class="pf-field" style="grid-column: 1 / -1;">
                <label for="twitter">Twitter / X</label>
                <div class="pf-input">
                  <span class="pf-lead">𝕏</span>
                  <input type="text" id="twitter" name="twitter" value="{{ old('twitter', $blog->twitter) }}" placeholder="@@username">
                </div>
              </div>
            </div>
          </section>
    
          <div class="pf-save">
            <small>Your changes are only saved when you click Save.</small>
            <div class="pf-save-btns">
              <a href="{{ route('dashboard') }}" class="pf-ghost">Cancel</a>
              <button type="submit" class="pf-primary">Save profile →</button>
            </div>
          </div>
    
        </div>
    
        <!-- RIGHT: read-only account info -->
        <aside>
          <section class="pf-panel" style="padding: 24px;">
            <div class="pf-title" style="margin-bottom: 8px;">⚙ Account</div>
            <div class="pf-info-row">
              <span>Initials</span>
              <span class="pf-badge" id="initialsBadge">{{ $user->initials }}</span>
            </div>
            <div class="pf-info-row">
              <span>✉ Email</span>
              <b>{{ $user->email }}</b>
            </div>
            <div class="pf-info-row">
              <span>🗓 Date registered</span>
              <b>{{ $user->created_at->format('M j, Y') }}</b>
            </div>
            <p class="pf-hint" style="margin-top:14px;">Initials come from your full name. Your sign-in email and registration date can't be changed here. The contact email above is separate and public.</p>
          </section>
        </aside>
    
      </div>
    </form>
    
    <script>
      // Bio counter
      const bio = document.getElementById('bio');
      const bioCount = document.getElementById('bioCount');
      bio.addEventListener('input', () => bioCount.textContent = bio.value.length);
    
      // Initials preview from the name
      const nameInput = document.getElementById('name');
      const initialsBadge = document.getElementById('initialsBadge');
      const avatarPreview = document.getElementById('avatarPreview');
      const removeAvatar = document.getElementById('removeAvatar');
      let hasPhoto = {{ $user->avatar_url ? 'true' : 'false' }};
    
      function currentInitials() {
        const parts = nameInput.value.trim().split(/\s+/).filter(Boolean);
        return parts.slice(0, 2).map(p => p[0].toUpperCase()).join('') || '–';
      }
      function updateInitials() {
        initialsBadge.textContent = currentInitials();
        if (!hasPhoto) avatarPreview.textContent = currentInitials();

        // keep the blog name hint and placeholder in step with the full name
    const shown = nameInput.value.trim() || 'Your name';
    document.getElementById('blog_title').placeholder = shown + "'s Blog";
    document.getElementById('defaultTitle').textContent = shown;
      }
      nameInput.addEventListener('input', updateInitials);
    
      // Photo preview / remove
      const avatarInput = document.getElementById('avatarInput');
      avatarInput.addEventListener('change', () => {
        const file = avatarInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
          avatarPreview.style.backgroundImage = 'url(' + e.target.result + ')';
          avatarPreview.textContent = '';
          hasPhoto = true;
          removeAvatar.value = '0';
        };
        reader.readAsDataURL(file);
      });
      document.getElementById('removePhoto').addEventListener('click', () => {
        avatarInput.value = '';
        avatarPreview.style.backgroundImage = '';
        hasPhoto = false;
        removeAvatar.value = '1';
        updateInitials();
      });
    
      // Tech stack chips (built with DOM calls, so typed text can never become HTML)
      const stackBox = document.getElementById('stackBox');
      const stackInput = document.getElementById('stackInput');
    
      function addStackTag(text) {
        text = text.trim().slice(0, 30);
        if (!text) return;
        const existing = [...stackBox.querySelectorAll('input[name="tech_stack[]"]')];
        if (existing.length >= 15) return;
        if (existing.some(i => i.value.toLowerCase() === text.toLowerCase())) return;
    
        const pill = document.createElement('span');
        pill.className = 'pf-pill';
        pill.append(document.createTextNode(text));
    
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tech_stack[]';
        hidden.value = text;
        pill.append(hidden);
    
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Remove');
        btn.textContent = '✕';
        pill.append(btn);
    
        stackBox.insertBefore(pill, stackInput);
      }
    
      stackInput.addEventListener('keydown', e => {
        if ((e.key === 'Enter' || e.key === ',') && stackInput.value.trim()) {
          e.preventDefault();
          addStackTag(stackInput.value);
          stackInput.value = '';
        } else if (e.key === 'Enter') {
          e.preventDefault();               // never submit the form from this box
        } else if (e.key === 'Backspace' && !stackInput.value) {
          const pills = stackBox.querySelectorAll('.pf-pill');
          if (pills.length) pills[pills.length - 1].remove();
        }
      });
      stackBox.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON') e.target.parentElement.remove();
        else stackInput.focus();
      });
    </script>
    
    </div>
    </x-dashboard-layout>