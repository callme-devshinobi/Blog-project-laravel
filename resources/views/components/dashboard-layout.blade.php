@props(['title' => 'Dashboard'])

<x-layout>
    <div class="dashboard-page">
      <div class="db-shell">

        {{-- Sidebar --}}
        <aside class="db-side" id="dbSide">
          <button type="button" class="db-close" id="dbClose" aria-label="Close menu">✕</button>
          <nav class="db-nav">
            <a href="{{ route('dashboard') }}" class="db-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="db-link-icon">🏠</span> Dashboard</a>
            <a href="{{ route('posts.published') }}" class="db-link {{ request()->routeIs('posts.published') ? 'active' : '' }}"><span class="db-link-icon">📄</span> My Posts</a>
            <a href="{{ route('posts.drafts') }}" class="db-link {{ request()->routeIs('posts.drafts') ? 'active' : '' }}"><span class="db-link-icon">📝</span> Drafts</a>
            <a href="{{ route('posts.create') }}" class="db-link {{ request()->routeIs('posts.create') ? 'active' : '' }}"><span class="db-link-icon">✏️</span> New Post</a>
          </nav>

          <div class="db-promo">
            <span class="db-laptop">💻</span>
            <p>Keep writing,<br>keep sharing!</p>
          </div>
        </aside>
        <div class="db-backdrop" id="dbBackdrop"></div>

        {{-- Main --}}
        <main class="db-main">

          <div class="db-topbar">
            <div class="db-topbar-left">
              <button type="button" class="db-burger" id="dbBurger" aria-label="Open menu" aria-expanded="false" aria-controls="dbSide">☰</button>
              <h1>{{ $title }}</h1>
            </div>
            <div class="db-user">
              <span class="db-avatar">{{ auth()->user()->initials }}</span>
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