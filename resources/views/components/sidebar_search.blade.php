<div class="widget">
  <h4>Search</h4>
  <form action="{{ $base }}/search" method="GET" role="search">
    <div class="search-box">
      <input type="search" name="q" value="{{ request('q') }}" maxlength="80"
             placeholder="Search for articles..." aria-label="Search articles">
      <button type="submit" aria-label="Submit search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/>
        </svg>
      </button>
    </div>
  </form>
</div>