@props(['title' => 'Admin'])

<x-layout>
    <div class="dashboard-page">
      <div class="db-shell">

        <aside class="db-side" id="dbSide">
          <button type="button" class="db-close" id="dbClose" aria-label="Close menu">✕</button>
          <nav class="db-nav">
            <a href="{{ route('admin.dashboard') }}" class="db-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="db-link-icon">🛡️</span> Overview</a>
            <a href="{{ route('dashboard') }}" class="db-link"><span class="db-link-icon">🏠</span> My dashboard</a>
           
            <a href="{{ route('admin.activity') }}" class="db-link {{ request()->routeIs('admin.activity') ? 'active' : '' }}"><span class="db-link-icon">🧾</span> Activity</a>
            <a href="{{ route('admin.users') }}" class="db-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><span class="db-link-icon">👥</span> Users</a>  
            <a href="{{ route('admin.blogs') }}" class="db-link {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}"><span class="db-link-icon">📚</span> Blogs</a>
            <a href="{{ route('admin.posts') }}" class="db-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}"><span class="db-link-icon">📄</span> Posts</a>
            <a href="{{ route('admin.subscriptions') }}" class="db-link {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}"><span class="db-link-icon">💳</span> Subscriptions</a>
            <a href="{{ url('/') }}" class="db-link"><span class="db-link-icon">↩️</span> Back to site</a>
            </nav>

          <div class="db-promo">
            <span class="db-laptop">🛡️</span>
            <p>Admin area.<br>Changes here affect everyone.</p>
          </div>
        </aside>
        <div class="db-backdrop" id="dbBackdrop"></div>

        <main class="db-main">

          <div class="db-topbar">
            <div class="db-topbar-left">
              <button type="button" class="db-burger" id="dbBurger" aria-label="Open menu" aria-expanded="false" aria-controls="dbSide">☰</button>
              <h1>{{ $title }}</h1>
            </div>
            <div class="db-user">
              <span class="db-avatar" @if (auth()->user()->avatar_url) style="background:center/cover no-repeat url('{{ auth()->user()->avatar_url }}')" @endif>{{ auth()->user()->avatar_url ? '' : auth()->user()->initials }}</span>
              <b>{{ auth()->user()->name }}</b>
            </div>
          </div>

          {{ $slot }}

        </main>
      </div>

      <script>
        const dbSide     = document.getElementById('dbSide');
        const dbBackdrop = document.getElementById('dbBackdrop');
        const dbBurger   = document.getElementById('dbBurger');

        function setMenu(open) {
          dbSide.classList.toggle('open', open);
          dbBackdrop.classList.toggle('open', open);
          dbBurger.setAttribute('aria-expanded', open);
          document.body.style.overflow = open ? 'hidden' : '';
        }

        dbBurger.addEventListener('click', () => setMenu(true));
        document.getElementById('dbClose').addEventListener('click', () => setMenu(false));
        dbBackdrop.addEventListener('click', () => setMenu(false));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
        dbSide.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
        window.addEventListener('resize', () => { if (window.innerWidth > 860) setMenu(false); });
      </script>
    </div>
</x-layout>