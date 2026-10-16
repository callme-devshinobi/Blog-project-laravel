@props(['posts'])

<div class="widget">
  <h4>Popular Posts</h4>
  <ul class="popular-list">
    @foreach ($posts->take(4) as $index => $p_posts)
      <li>
        <a href="/post/{{ $p_posts['id'] }}">
          <span class="rank">{{ $index + 1 }}</span>
          <img src="{{ $p_posts['image'] }}" alt="{{ $p_posts['image_alt'] }}">
          <span>
            <span class="p-title">{{ $p_posts['title'] }}</span>
            <span class="p-meta">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
              {{ $p_posts['read_time'] }} min read
            </span>
          </span>
        </a>
      </li>
    @endforeach
  </ul>
</div>