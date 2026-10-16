@if ($paginator->hasPages())

@php
    $current = $paginator->currentPage();   // the page you are on
    $last    = $paginator->lastPage();      // the total number of pages

    // Pick 3 page numbers around the current page
    $start = max(1, min($current - 1, $last - 2));
    $end   = min($last, $start + 2);
@endphp

<nav class="pagination" aria-label="Pagination">

  {{-- The (up to) 3 numbered buttons --}}
  @for ($page = $start; $page <= $end; $page++)
    @if ($page == $current)
      <span class="page-btn active" aria-current="page">{{ $page }}</span>
    @else
      <a href="{{ $paginator->url($page) }}" class="page-btn">{{ $page }}</a>
    @endif
  @endfor

  {{-- Show "..." only if there are more pages after these --}}
  @if ($end < $last)
    <span class="page-btn disabled">...</span>
  @endif

  {{-- Next button --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" aria-label="Next page">
      Next
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
  @else
    <span class="page-btn disabled" aria-label="Next page">
      Next
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </span>
  @endif

</nav>
@endif