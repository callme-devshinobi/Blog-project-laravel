<x-dashboard-layout title="Settings">
<div class="settings-page">

<style>
  .settings-page {
    --primary:#4f46e5; --primary-dark:#4338ca; --bg:#f5f6fa; --surface:#fff; --text:#111827;
    --muted:#6b7280; --border:#e9eaf2; --danger:#dc2626; --danger-dark:#b91c1c;
    --danger-soft:#fef2f2; --danger-border:#fecaca; --green:#16a34a; --radius:14px;
  }
  .st-wrap { max-width: 720px; }
  .st-intro { color: var(--muted); font-size: 14.5px; margin-bottom: 22px; }

  .st-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; }
  .st-alert-ok  { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
  .st-alert-err { background:var(--danger-soft); color:var(--danger-dark); border:1px solid var(--danger-border); }
  .st-alert ul  { margin: 6px 0 0 18px; }

  .st-panel { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
  .st-panel + .st-panel { margin-top: 24px; }
  .st-title { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
              color: var(--muted); margin-bottom: 8px; display: flex; align-items: center; gap: 9px; }
  .st-desc { color: var(--muted); font-size: 14px; margin-bottom: 22px; }

  .st-field { margin-bottom: 18px; }
  .st-field:last-child { margin-bottom: 0; }
  .st-field label { display: block; font-size: 13.5px; font-weight: 600; margin-bottom: 7px; }
  .st-field label i { color: #dc2626; font-style: normal; }
  .st-hint { font-weight: 400; color: var(--muted); font-size: 12.5px; margin-top: 6px; }

  .st-input { position: relative; }
  .st-lead { position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
             color: var(--muted); font-size: 14px; pointer-events: none; width: 16px; text-align: center; }
  .st-input input { width: 100%; padding: 12px 44px 12px 42px; border: 1px solid var(--border);
             border-radius: 10px; font-family: inherit; font-size: 14.5px; color: var(--text);
             background: var(--bg); transition: border-color .2s, box-shadow .2s, background .2s; }
  .st-input input:focus { outline: none; border-color: var(--primary); background: #fff;
             box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
  .st-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
             background: none; border: none; cursor: pointer; color: var(--muted); font-size: 14px;
             padding: 4px; border-radius: 6px; }
  .st-toggle:hover { color: var(--primary); }

  .st-match { font-size: 12.5px; margin-top: 6px; display: none; }
  .st-match.ok  { display: block; color: var(--green); }
  .st-match.err { display: block; color: var(--danger); }

  .st-btn { background: var(--primary); color: #fff; border: none; padding: 12px 26px; border-radius: 10px;
            font-family: inherit; font-weight: 700; font-size: 14.5px; cursor: pointer;
            transition: background .2s, transform .2s; white-space: nowrap; }
  .st-btn:hover { background: var(--primary-dark); transform: translateY(-1px); }

  .st-address { display: flex; gap: 12px; }
  .st-address .st-input { flex: 1; }
  .st-address .st-input input { padding-right: 14px; font-family: monospace; font-size: 13.5px; }
  @media (max-width: 560px) { .st-address { flex-direction: column; } }

  .st-dz { border: 1px solid var(--danger-border); border-radius: var(--radius); overflow: hidden; margin-top: 24px; background: var(--surface); }
  .st-dz-head { background: var(--danger-soft); border-bottom: 1px solid var(--danger-border);
                padding: 14px 22px; display: flex; align-items: center; gap: 10px; }
  .st-dz-head h3 { font-size: 15px; font-weight: 700; }
  .st-dz-row { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 20px 22px; }
  .st-dz-row h4 { font-size: 14.5px; font-weight: 700; margin-bottom: 3px; }
  .st-dz-row p { color: var(--muted); font-size: 13.5px; max-width: 440px; }
  .st-btn-outline { background: #fff; color: var(--danger); border: 1px solid var(--danger-border);
                    padding: 9px 20px; border-radius: 8px; font-family: inherit; font-weight: 600;
                    font-size: 13.5px; cursor: pointer; flex-shrink: 0; transition: all .2s; }
  .st-btn-outline:hover { background: var(--danger); border-color: var(--danger); color: #fff; }
  .st-btn-solid { background: var(--danger); color: #fff; border: 1px solid var(--danger);
                  padding: 11px 22px; border-radius: 8px; font-family: inherit; font-weight: 700;
                  font-size: 14px; cursor: pointer; }
  .st-btn-solid:hover { background: var(--danger-dark); border-color: var(--danger-dark); }
  .st-btn-solid:disabled { opacity: .5; cursor: not-allowed; }

  .st-overlay { position: fixed; inset: 0; background: rgba(11,13,18,.6); backdrop-filter: blur(3px);
                display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
  .st-overlay.show { display: flex; }
  .st-modal { background: var(--surface); border-radius: 16px; max-width: 460px; width: 100%; padding: 30px;
              box-shadow: 0 32px 64px -24px rgba(0,0,0,.5); }
  .st-modal h2 { font-size: 19px; font-weight: 800; margin-bottom: 10px; }
  .st-modal p { color: var(--muted); font-size: 14px; margin-bottom: 18px; }
  .st-modal .st-input input { padding-left: 14px; }
  .st-modal-actions { display: flex; gap: 12px; margin-top: 22px; }
  .st-modal-actions > * { flex: 1; }
  .st-btn-ghost { background: var(--bg); border: 1px solid var(--border); color: var(--text);
                  padding: 11px; border-radius: 9px; font-family: inherit; font-weight: 600;
                  font-size: 14px; cursor: pointer; }
  .st-btn-ghost:hover { background: var(--border); }

  @media (max-width: 860px) { .st-dz-row { flex-direction: column; align-items: flex-start; } }
  .st-status { font-size: 13.5px; font-weight: 600; margin-bottom: 16px; }
.st-status-ok { color: var(--green); }
.st-status-wait { color: #b45309; }
.st-dns { background: var(--bg); border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-top: 18px; font-size: 13px; color: var(--muted); }
.st-dns b { color: var(--text); }
.st-dns table { width: 100%; border-collapse: collapse; margin: 10px 0; }
.st-dns th, .st-dns td { text-align: left; padding: 6px 12px 6px 0; vertical-align: top; word-break: break-all; font-family: monospace; font-size: 12.5px; }
.st-dns small { display: block; font-family: inherit; color: var(--muted); }
</style>

<div class="st-wrap">

  <p class="st-intro">Manage your account security and blog address.</p>

  @if (session('success'))
    <div class="st-alert st-alert-ok">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="st-alert st-alert-err" role="alert">
      <b>Please fix the following:</b>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Change password -->
  <section class="st-panel">
    <div class="st-title">🔒 Change password</div>
    <p class="st-desc">Use a strong password you don't reuse elsewhere. You'll stay logged in on this device.</p>

    <form action="{{ route('settings.password') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="st-field">
        <label for="current_password">Current password <i>*</i></label>
        <div class="st-input">
          <span class="st-lead">🔒</span>
          <input type="password" id="current_password" name="current_password" placeholder="••••••••" required>
          <button type="button" class="st-toggle" data-target="current_password" aria-label="Show password">👁</button>
        </div>
      </div>

      <div class="st-field">
        <label for="new_password">New password <i>*</i></label>
        <div class="st-input">
          <span class="st-lead">🔒</span>
          <input type="password" id="new_password" name="password" placeholder="••••••••" required minlength="8">
          <button type="button" class="st-toggle" data-target="new_password" aria-label="Show password">👁</button>
        </div>
        <p class="st-hint">At least 8 characters.</p>
      </div>

      <div class="st-field">
        <label for="confirm_password">Confirm new password <i>*</i></label>
        <div class="st-input">
          <span class="st-lead">🔒</span>
          <input type="password" id="confirm_password" name="password_confirmation" placeholder="••••••••" required>
          <button type="button" class="st-toggle" data-target="confirm_password" aria-label="Show password">👁</button>
        </div>
        <p class="st-match" id="matchHint"></p>
      </div>

      <button type="submit" class="st-btn">Update password →</button>
    </form>
  </section>

  <!-- Blog address -->
   <!-- Blog address + custom domain -->
   @if ($blog)
   <section class="st-panel">
     <div class="st-title">🌐 Your blog address</div>
     <p class="st-desc">This is where readers find your blog. Share it anywhere.</p>

     <div class="st-address">
       <div class="st-input">
         <span class="st-lead">🌐</span>
         <input type="text" id="blogUrl" value="{{ url('/@' . $blog->subdomain) }}" readonly>
       </div>
       <button type="button" class="st-btn" id="copyUrl">Copy link</button>
     </div>
   </section>

   <section class="st-panel">
     <div class="st-title">🔗 Custom domain</div>
     <p class="st-desc">Use a domain you own, like <b>myblog.com</b>, so readers see your blog there.</p>

     @if ($blog->custom_domain && $blog->custom_domain_verified_at)
       <div class="st-status st-status-ok">✓ Active: <a href="https://{{ $blog->custom_domain }}" target="_blank" rel="noopener">{{ $blog->custom_domain }}</a></div>
     @elseif ($blog->custom_domain)
       <div class="st-status st-status-wait">⏳ Waiting for DNS verification: <b>{{ $blog->custom_domain }}</b></div>
     @endif

     <form action="{{ route('settings.domain') }}" method="POST">
       @csrf
       @method('PUT')
       <div class="st-address">
         <div class="st-input">
           <span class="st-lead">🔗</span>
           <input type="text" name="domain" value="{{ old('domain', $blog->custom_domain) }}" placeholder="myblog.com" autocomplete="off">
         </div>
         <button type="submit" class="st-btn">Save domain</button>
       </div>
       <p class="st-hint">Changing the domain means verifying it again.</p>
     </form>

     @if ($blog->custom_domain && ! $blog->custom_domain_verified_at)
       <div class="st-dns">
         <b>Add these records where you bought your domain:</b>
         <table>
           <tr><th>Type</th><th>Name</th><th>Value</th></tr>
           <tr>
             <td>TXT</td>
             <td>_devnotes-verify.{{ $blog->custom_domain }}</td>
             <td>{{ $blog->domain_token }}</td>
           </tr>
           <tr>
             <td>CNAME</td>
             <td>{{ $blog->custom_domain }} <small>(use for www.example.com or blog.example.com)</small></td>
             <td>{{ config('devnotes.domain_target') }}</td>
           </tr>
           <tr>
             <td>A</td>
             <td>{{ $blog->custom_domain }} <small>(use instead of CNAME for a root domain like example.com)</small></td>
             <td>{{ config('devnotes.server_ip') ?: 'your server IP address' }}</td>
           </tr>
         </table>
         <small>The TXT record proves you own the domain. The CNAME or A record sends visitors to DevNotes. Some providers want only the part before your root domain in the Name column. DNS changes can take from a few minutes to 48 hours.</small>
       </div>

       <form action="{{ route('settings.domain.verify') }}" method="POST" style="margin-top:16px;">
         @csrf
         <button type="submit" class="st-btn">Verify domain</button>
       </form>
     @endif

     @if ($blog->custom_domain)
       <form action="{{ route('settings.domain.destroy') }}" method="POST" style="margin-top:16px;"
             onsubmit="return confirm('Remove this custom domain? Your blog stays available at its DevNotes address.')">
         @csrf
         @method('DELETE')
         <button type="submit" class="st-btn-outline">Remove domain</button>
       </form>
     @endif
   </section>
 @endif

  <!-- Danger zone -->
  <section class="st-dz">
    <div class="st-dz-head">
      <span>⚠️</span>
      <h3>Danger zone</h3>
    </div>
    <div class="st-dz-row">
      <div>
        <h4>Delete this account</h4>
        <p>Once you delete your account, there is no going back. All your posts, drafts and your blog will be permanently destroyed. Please be certain.</p>
      </div>
      <button type="button" class="st-btn-outline" id="openDelete">Delete account</button>
    </div>
  </section>

</div>

<!-- Delete confirmation modal -->
<div class="st-overlay" id="deleteModal">
  <div class="st-modal" role="dialog" aria-modal="true" aria-labelledby="deleteTitle">
    <h2 id="deleteTitle">⚠️ Delete account?</h2>
    <p>This action <b>cannot be undone</b>. This will permanently delete the <b>{{ auth()->user()->name }}</b> account,
       along with {{ $publishedCount }} published {{ Str::plural('post', $publishedCount) }}
       and {{ $draftCount }} {{ Str::plural('draft', $draftCount) }}.</p>

    <form action="{{ route('settings.destroy') }}" method="POST">
      @csrf
      @method('DELETE')

      <div class="st-field">
        <label for="del_password">Your password</label>
        <div class="st-input">
          <input type="password" id="del_password" name="password" placeholder="••••••••" required>
        </div>
      </div>

      <div class="st-field">
        <label for="confirmPhrase">Type <b>delete my account</b> to confirm:</label>
        <div class="st-input">
          <input type="text" id="confirmPhrase" name="confirm" placeholder="delete my account" autocomplete="off">
        </div>
      </div>

      <div class="st-modal-actions">
        <button type="button" class="st-btn-ghost" id="cancelDelete">Cancel</button>
        <button type="submit" class="st-btn-solid" id="confirmDelete" disabled>Delete this account</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Show / hide password
  document.querySelectorAll('.st-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.textContent = input.type === 'password' ? '👁' : '🙈';
    });
  });

  // Password match feedback
  const newPass = document.getElementById('new_password');
  const confirmPass = document.getElementById('confirm_password');
  const matchHint = document.getElementById('matchHint');
  function checkMatch() {
    if (!confirmPass.value) { matchHint.className = 'st-match'; return; }
    const ok = confirmPass.value === newPass.value;
    matchHint.className = 'st-match ' + (ok ? 'ok' : 'err');
    matchHint.textContent = ok ? '✓ Passwords match' : '✕ Passwords do not match';
  }
  newPass.addEventListener('input', checkMatch);
  confirmPass.addEventListener('input', checkMatch);

  // Copy blog link
  const copyBtn = document.getElementById('copyUrl');
  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      navigator.clipboard.writeText(document.getElementById('blogUrl').value).then(() => {
        copyBtn.textContent = 'Copied ✓';
        setTimeout(() => copyBtn.textContent = 'Copy link', 1800);
      });
    });
  }

  // Delete modal
  const modal = document.getElementById('deleteModal');
  const phrase = document.getElementById('confirmPhrase');
  const confirmBtn = document.getElementById('confirmDelete');
  document.getElementById('openDelete').addEventListener('click', () => {
    modal.classList.add('show');
    phrase.value = '';
    confirmBtn.disabled = true;
    phrase.focus();
  });
  document.getElementById('cancelDelete').addEventListener('click', () => modal.classList.remove('show'));
  modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('show'); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('show'); });
  phrase.addEventListener('input', () => {
    confirmBtn.disabled = phrase.value !== 'delete my account';
  });
</script>

</div>
</x-dashboard-layout>