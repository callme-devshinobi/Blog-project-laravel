<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevNotes — Thoughts, tutorials and real-world tech insights</title>
  <!--
    DevNotes blog homepage — pure HTML + CSS (no JavaScript)
    Images: royalty-free photos from Pixabay (cdn.pixabay.com)
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="main.css">
  @vite('resources/css/app.css')
</head>
  <body>
  
  <!-- ================= HEADER ================= -->
  <header class="site-header">
    <div class="container header-inner">
  
      <a href="/" class="logo">
        <span class="logo-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="8 6 3 12 8 18"/><polyline points="16 6 21 12 16 18"/>
          </svg>
        </span>
        DevNotes
      </a>
  
      <input type="checkbox" id="nav-toggle" class="nav-toggle">
  
      <nav class="main-nav">
        <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="/categories" class="{{ request()->is('categories*') ? 'active' : '' }}">Categories</a>
        <a href="/about" class="{{ request()->is('about') ? 'active' : '' }}">About</a>
        <a href="/contact" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a>
      </nav>
  
      <div class="header-actions">
        <button class="icon-btn" aria-label="Search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/>
          </svg>
        </button>
        <button class="icon-btn" aria-label="Toggle dark mode">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>
          </svg>
        </button>
        <a href="#" class="btn-subscribe">Subscribe</a>
        <label for="nav-toggle" class="icon-btn hamburger" aria-label="Open menu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </label>
      </div>
  
    </div>
  </header>
{{$slot}}
  <!-- ================= FOOTER ================= -->
  <footer class="site-footer">
    <div class="container footer-inner">
  
      <a href="/" class="logo">
        <span class="logo-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="8 6 3 12 8 18"/><polyline points="16 6 21 12 16 18"/>
          </svg>
        </span>
        DevNotes
      </a>
  
      <p class="footer-copy">&copy; 2025 DevNotes. Built with Laravel. <span class="heart">&hearts;</span> by Dennis Onwuegbu</p>
  
      <div class="socials">
        <a href="#" aria-label="GitHub">
          <svg viewBox="0 0 24 24"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.55v-2.15c-3.2.7-3.87-1.36-3.87-1.36-.52-1.33-1.28-1.68-1.28-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.75 2.69 1.25 3.35.95.1-.74.4-1.25.72-1.53-2.55-.29-5.24-1.28-5.24-5.68 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.77 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.7 5.38-5.27 5.66.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.55A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"/></svg>
        </a>
        <a href="#" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.24 8.16h4.52V23H.24V8.16zM8.34 8.16h4.33v2.03h.06c.6-1.14 2.08-2.34 4.28-2.34 4.57 0 5.41 3.01 5.41 6.92V23h-4.51v-7.23c0-1.72-.03-3.94-2.4-3.94-2.4 0-2.77 1.87-2.77 3.81V23H8.34V8.16z"/></svg>
        </a>
        <a href="#" aria-label="X (Twitter)">
          <svg viewBox="0 0 24 24"><path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.47l8.6-9.83L0 1.15h7.59l5.24 6.93 6.07-6.93zm-1.29 19.5h2.04L6.49 3.24H4.3l13.31 17.41z"/></svg>
        </a>
        <a href="#" aria-label="YouTube">
          <svg viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/></svg>
        </a>
      </div>
  
    </div>
  </footer>
  
  </body>
  </html>
  