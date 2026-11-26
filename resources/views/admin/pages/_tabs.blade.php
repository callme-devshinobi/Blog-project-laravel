<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;">
    @foreach (['about' => 'About page', 'contact' => 'Contact page', 'faq' => 'FAQ'] as $key => $label)
      <a href="{{ route('admin.pages.' . $key) }}"
         style="padding:9px 18px;border-radius:999px;font-weight:600;font-size:14px;border:1px solid var(--border);
                {{ request()->routeIs('admin.pages.' . $key . '*') ? 'background:var(--indigo);color:#fff;border-color:var(--indigo);' : 'background:var(--card);color:var(--text);' }}">{{ $label }}</a>
    @endforeach
    <a href="{{ url('/' . (request()->routeIs('admin.pages.faq*') ? 'contact' : (request()->routeIs('admin.pages.contact*') ? 'contact' : 'about'))) }}" target="_blank" rel="noopener"
       style="margin-left:auto;padding:9px 4px;color:var(--indigo);font-weight:600;font-size:14px;">View the page →</a>
  </div>