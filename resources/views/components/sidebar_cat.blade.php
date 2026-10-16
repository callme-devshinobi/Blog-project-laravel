 <!-- =================  
 @props(['current' => null])

<div class="widget">
  <h4>Categories</h4>
  <ul class="cat-list">
    <li class="{{ $current === 'laravel' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Laravel <span class="count">5</span></a></li>

    <li class="{{ $current === 'web-dev' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Web Development <span class="count">4</span></a></li>

    <li class="{{ $current === 'js' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      JavaScript <span class="count">3</span></a></li>

    <li class="{{ $current === 'tools' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Tools &amp; Resources <span class="count">2</span></a></li>

    <li class="{{ $current === 'productivity' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Productivity <span class="count">2</span></a></li>

    <li class="{{ $current === 'news' ? 'current' : '' }}"><a href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Tech News <span class="count">1</span></a></li>
  </ul>
</div>
================= -->
@props(['current' => null, 'categories', 'posts'])

<div class="widget">
  <h4>Categories</h4>
  <ul class="cat-list">
    @foreach ($categories as $cat)
      <li class="{{ $current === $cat['slug'] ? 'current' : '' }}">
        <a href="/show/{{ $cat['name'] }}">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          {{ $cat['name'] }}
          <span class="count">{{ collect($posts)->where('category', $cat['name'])->count() }}</span>
        </a>
      </li>
    @endforeach
  </ul>
</div>

