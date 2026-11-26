@props(['post', 'compact' => false])

@php
    $url   = $post->share_url;
    $title = $post->title;
    $enc   = fn ($v) => rawurlencode($v);

    $links = [
        'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $enc($url),
        'X'        => 'https://x.com/intent/tweet?url=' . $enc($url) . '&text=' . $enc($title),
        'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $enc($url),
        'WhatsApp' => 'https://wa.me/?text=' . $enc($title . ' ' . $url),
        'Telegram' => 'https://t.me/share/url?url=' . $enc($url) . '&text=' . $enc($title),
        'Email'    => 'mailto:?subject=' . $enc($title) . '&body=' . $enc($url),
    ];

    if ($compact) {
        $links = array_intersect_key($links, array_flip(['LinkedIn', 'X', 'WhatsApp']));
    }
@endphp

@if ($compact)
  <details class="share-menu" data-share data-url="{{ $url }}" data-title="{{ $title }}">
    <summary class="dr-btn">Share</summary>
    <div class="share-pop">
      @foreach ($links as $name => $href)
        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer">{{ $name }}</a>
      @endforeach
      <button type="button" data-copy>Copy link</button>
    </div>
  </details>
@else
  <div class="share" data-share data-url="{{ $url }}" data-title="{{ $title }}">
    <span class="share-label">Share</span>
    @foreach ($links as $name => $href)
      <a href="{{ $href }}" class="share-btn"
         @unless ($name === 'Email') target="_blank" rel="noopener noreferrer" @endunless
         aria-label="Share on {{ $name }}">{{ $name }}</a>
    @endforeach
    <button type="button" class="share-btn" data-copy>Copy link</button>
    <button type="button" class="share-btn" data-native hidden>Share…</button>
  </div>
@endif

@once
  <script>
    document.addEventListener('click', function (e) {
      var copy = e.target.closest('[data-copy]');
      var nat  = e.target.closest('[data-native]');

      if (copy) {
        var wrap = copy.closest('[data-share]');
        var url  = wrap.dataset.url;
        var done = function () {
          var old = copy.textContent;
          copy.textContent = 'Copied ✓';
          setTimeout(function () { copy.textContent = old; }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(url).then(done);
        } else {
          window.prompt('Copy this link', url);
        }
      }

      if (nat) {
        var w = nat.closest('[data-share]');
        navigator.share({ title: w.dataset.title, url: w.dataset.url }).catch(function () {});
      }
    });

    // the phone's own share sheet, only where the browser has one
    if (navigator.share) {
      document.querySelectorAll('[data-native]').forEach(function (b) { b.hidden = false; });
    }
  </script>
@endonce