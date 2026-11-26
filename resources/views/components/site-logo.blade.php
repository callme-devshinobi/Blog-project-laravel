@php $logo = \App\Support\SiteSettings::logoUrl(); @endphp
@if (! $currentBlog && $logo)
  <img src="{{ $logo }}" alt="" style="height:30px;width:auto;border-radius:6px;">
@else
  <span class="logo-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="8 6 3 12 8 18"/><polyline points="16 6 21 12 16 18"/>
    </svg>
  </span>
@endif
{{ $currentBlog?->title ?? config('devnotes.site.name') }}