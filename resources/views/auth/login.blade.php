<x-layout>
    <div class="auth-page">
      <div class="auth-card login-card">
        <h1>Welcome back</h1>
        <p class="subtitle">Log in to write, comment and save articles.</p>
    
        {{-- Error sent back by Laravel --}}
        @if ($errors->any())
          <div class="alert" role="alert">
            <b>Could not log you in</b>
            {{ $errors->first() }}
          </div>
        @endif
    
        <form action="{{ route('login') }}" method="POST" id="loginForm">
          @csrf
    
          <div class="field">
            <label for="email">Email address</label>
            <div class="input-wrap">
              <span class="lead-icon">✉</span>
              <input type="email" id="email" name="email" placeholder="you@example.com"
                     value="{{ old('email') }}" class="@error('email') invalid @enderror" required autofocus>
            </div>
          </div>
    
          <div class="field">
            <label for="password">Password</label>
            <div class="input-wrap">
              <span class="lead-icon">🔒</span>
              <input type="password" id="password" name="password" placeholder="••••••••" required>
              <button type="button" class="toggle-pass" id="togglePass" aria-label="Show password">👁</button>
            </div>
          </div>
    
          <div class="form-row">
            <label class="remember">
              <input type="checkbox" name="remember" id="remember"> Remember me
            </label>
            <a href="#">Forgot password?</a>
          </div>
    
          <button type="submit" class="btn btn-primary">Log in →</button>
        </form>
    
        <p class="switch-auth">New to DevNotes? <a href="{{ route('register') }}">Create an account</a></p>
        <a href="/" class="back-home">← Back to the blog</a>
      </div>
    
      <script>
        // Show / hide password
        const pass = document.getElementById('password');
        document.getElementById('togglePass').addEventListener('click', function () {
          pass.type = pass.type === 'password' ? 'text' : 'password';
          this.textContent = pass.type === 'password' ? '👁' : '🙈';
        });
      </script>
    </div>
    </x-layout>