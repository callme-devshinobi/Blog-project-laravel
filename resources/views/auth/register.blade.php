<x-layout>
    <div class="auth-page">
      <div class="auth-card">
        <h1>Create your account</h1>
        <p class="subtitle">Join DevNotes to comment, save articles and follow new posts.</p>
    
        <div class="perks">
          <span class="perk"><b>✓</b> Free forever</span>
          <span class="perk"><b>✓</b> No spam, ever</span>
          <span class="perk"><b>✓</b> Unsubscribe anytime</span>
        </div>
    
        {{-- Errors sent back by Laravel --}}
        @if ($errors->any())
          <div class="alert" role="alert">
            <b>Please fix the following:</b>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
    
        <form action="{{ route('register') }}" method="POST" id="signupForm" novalidate>
          @csrf
    
          <div class="field">
            <label for="name">Full name <i>*</i></label>
            <div class="input-wrap">
              <span class="lead-icon">👤</span>
              <input type="text" id="name" name="name" placeholder="Ada Lovelace"
                     value="{{ old('name') }}" class="@error('name') invalid @enderror" required autofocus>
            </div>
            <p class="field-error" id="nameError">Please enter your name.</p>
          </div>
    
          <div class="field">
            <label for="email">Email address <i>*</i></label>
            <div class="input-wrap">
              <span class="lead-icon">✉</span>
              <input type="email" id="email" name="email" placeholder="you@example.com"
                     value="{{ old('email') }}" class="@error('email') invalid @enderror" required>
            </div>
            <p class="field-error" id="emailError">Please enter a valid email address.</p>
          </div>
    
          <div class="field">
            <label for="password">Password <i>*</i></label>
            <div class="input-wrap">
              <span class="lead-icon">🔒</span>
              <input type="password" id="password" name="password" placeholder="••••••••"
                     class="@error('password') invalid @enderror" required>
              <button type="button" class="toggle-pass" data-target="password" aria-label="Show password">👁</button>
            </div>
            <div class="strength" id="strength">
              <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
              <p class="strength-label">Password strength: <b id="strengthText">—</b></p>
            </div>
            <ul class="reqs" id="reqs">
              <li data-req="length">At least 8 characters</li>
              <li data-req="upper">One uppercase letter</li>
              <li data-req="number">One number</li>
              <li data-req="symbol">One symbol</li>
            </ul>
          </div>
    
          <div class="field">
            <label for="password_confirmation">Confirm password <i>*</i></label>
            <div class="input-wrap">
              <span class="lead-icon">🔒</span>
              <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
              <button type="button" class="toggle-pass" data-target="password_confirmation" aria-label="Show password">👁</button>
            </div>
            <p class="field-error" id="confirmError">Passwords do not match.</p>
          </div>
    
          <label class="terms">
            <input type="checkbox" name="terms" id="terms" required>
            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</span>
          </label>
          <p class="field-error" id="termsError" style="margin:-14px 0 18px;">Please accept the terms to continue.</p>
    
          <button type="submit" class="btn btn-primary">Create account →</button>
        </form>
    
        <p class="switch-auth">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
        <a href="/" class="back-home">← Back to the blog</a>
      </div>
    
      <script>
        // Show / hide password toggles
        document.querySelectorAll('.toggle-pass').forEach(btn => {
          btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁' : '🙈';
          });
        });
    
        // Password strength + requirements
        const pass = document.getElementById('password');
        const strength = document.getElementById('strength');
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        const reqs = {
          length: v => v.length >= 8,
          upper:  v => /[A-Z]/.test(v),
          number: v => /[0-9]/.test(v),
          symbol: v => /[^A-Za-z0-9]/.test(v),
        };
        function score(v) { return Object.values(reqs).map(f => f(v)).filter(Boolean).length; }
        pass.addEventListener('input', () => {
          const v = pass.value;
          strength.classList.toggle('show', v.length > 0);
          document.querySelectorAll('#reqs li').forEach(li => {
            li.classList.toggle('met', reqs[li.dataset.req](v));
          });
          const s = score(v);
          const levels = ['Weak', 'Fair', 'Good', 'Strong'];
          const colors = ['#dc2626', '#f59e0b', '#84cc16', '#16a34a'];
          fill.style.width = (s / 4 * 100) + '%';
          fill.style.background = colors[s - 1] || 'transparent';
          text.textContent = s ? levels[s - 1] : '—';
          text.style.color = s ? colors[s - 1] : 'inherit';
        });
    
        // Check the form before sending. It only blocks sending if something is wrong.
        document.getElementById('signupForm').addEventListener('submit', function (e) {
          const name = document.getElementById('name');
          const email = document.getElementById('email');
          const confirmBox = document.getElementById('password_confirmation');
          const terms = document.getElementById('terms');
          let ok = true;
          function check(input, errId, valid) {
            document.getElementById(errId).style.display = valid ? 'none' : 'block';
            input.classList.toggle('invalid', !valid);
            if (!valid) ok = false;
          }
          check(name, 'nameError', name.value.trim().length >= 2);
          check(email, 'emailError', /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value));
          check(confirmBox, 'confirmError', confirmBox.value.length > 0 && confirmBox.value === pass.value);
          check(terms, 'termsError', terms.checked);
          if (!ok) e.preventDefault();   // stop only when something is wrong
        });
      </script>
    </div>
    </x-layout>