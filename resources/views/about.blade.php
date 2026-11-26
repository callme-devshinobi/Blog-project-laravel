<x-layout>

  @if ($currentBlog)
  
    @php
      $owner = $currentBlog->user;
      $stack = $currentBlog->tech_stack ?? [];
      $isOwner = auth()->check() && auth()->id() === $currentBlog->user_id;
      $toParagraphs = fn ($text) => preg_split('/\R{2,}/', trim($text ?? ''));
  
      // shown when the owner hasn't written a bio yet
      $defaultAbout = $currentBlog->title . ' is a blog on ' . config('devnotes.site.name') . ', a platform where developers, creators and curious minds get their own space to write, share and learn in public.';
    @endphp
  
    {{-- ================= Blogger's About page: /@name/about ================= --}}
  
    <!-- Hero -->
    <section class="page-hero">
      <p class="eyebrow">About {{ $currentBlog->title }}</p>
      <h1>Hi, I'm {{ $owner->name }} 👋</h1>
      <p>{{ $currentBlog->bio ?: $defaultAbout }}</p>
    </section>
  
    @if ($isOwner && ! $currentBlog->bio && empty($stack) && ! $currentBlog->write_about && ! $currentBlog->why_write)
      <section class="section">
        <p style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:10px;padding:14px 18px;font-size:14px;">
          Only you can see this: your About page is mostly empty.
          <a href="{{ route('profile') }}"><b>Fill in your profile</b></a> to add your bio, tech stack, and what and why you write.
        </p>
      </section>
    @endif
  
    <!-- Who's behind it -->
    <section class="section">
      <div class="two-col">
        <div>
          <h2>{{ $currentBlog->bio ? 'About me' : 'About this blog' }}</h2>
  
          @if ($currentBlog->bio)
            <p class="lead">{{ $currentBlog->bio }}</p>
          @else
            <p class="lead">{{ $defaultAbout }}</p>
            <p>{{ $owner->name }} is just getting started, so check back soon for tutorials, lessons from real projects, and ideas worth sharing.</p>
            <p>Every blog on {{ config('devnotes.site.name') }} belongs to its author: their own posts, their own address, and, if they like, their own domain.</p>
          @endif
  
          @if (! empty($stack))
            <p><b>Tech stack</b></p>
            <div class="chips">
              @foreach ($stack as $tag)
                <span class="chip">{{ $tag }}</span>
              @endforeach
            </div>
          @elseif ($topics->isNotEmpty())
            <p><b>Topics covered</b></p>
            <div class="chips">
              @foreach ($topics as $topic)
                <span class="chip">{{ $topic }}</span>
              @endforeach
            </div>
          @endif
        </div>
  
        <div class="portrait-card">
          @if ($owner->avatar_url)
            <div class="portrait" style="background: center/cover no-repeat url('{{ $owner->avatar_url }}');"></div>
          @else
            <div class="portrait" style="display:grid;place-items:center;font-size:56px;font-weight:800;color:#fff;">{{ $owner->initials }}</div>
          @endif
          <div class="portrait-meta">
            <strong>{{ $owner->name }}</strong>
            <span>Author of {{ $currentBlog->title }}</span>
          </div>
        </div>
      </div>
    </section>
  
    <!-- Stats (real numbers from the database) -->
    <section class="section">
      <div class="stats">
        <div class="stat"><b>{{ $postCount }}</b><span>{{ Str::plural('Article', $postCount) }} published</span></div>
        <div class="stat"><b>{{ $categoryCount }}</b><span>{{ Str::plural('Category', $categoryCount) }} covered</span></div>
        <div class="stat"><b>{{ $since }}</b><span>Blogging since</span></div>
      </div>
    </section>
  
    <!-- What I write about / Why I write -->
    @if ($currentBlog->write_about || $currentBlog->why_write)
      <section class="section">
        <div class="two-col">
          @if ($currentBlog->write_about)
            <div>
              <h2>What I write about</h2>
              @foreach ($toParagraphs($currentBlog->write_about) as $para)
                <p>{!! nl2br(e($para)) !!}</p>
              @endforeach
            </div>
          @endif
  
          @if ($currentBlog->why_write)
            <div>
              <h2>Why I write</h2>
              @foreach ($toParagraphs($currentBlog->why_write) as $para)
                <p>{!! nl2br(e($para)) !!}</p>
              @endforeach
            </div>
          @endif
        </div>
      </section>
    @endif
  
    <!-- CTA -->
    <section class="section">
      <div class="cta">
        <h2>Let's connect</h2>
        <p>Read my latest posts, or reach out if you have a question or a topic you'd love to see covered.</p>
        <div class="cta-actions">
          <a href="{{ $base }}/posts" class="btn-subscribe">Read the latest posts</a>
          <a href="{{ $base }}/contact" class="btn-ghost">Get in touch →</a>
        </div>
      </div>
    </section>
  
  @else
  
    {{-- ================= Main site About page: /about (edited in Admin, Pages) ================= --}}
  
    @php
      $a        = \App\Support\SiteContent::about();
      $fill     = fn ($text) => \App\Support\SiteContent::fill($text);
      $portrait = \App\Support\SiteContent::portraitUrl();
      $isExt    = fn ($url) => \App\Support\SiteContent::isExternal($url);
    @endphp
  
    <!-- Hero -->
    <section class="page-hero">
      @if ($a['eyebrow'])<p class="eyebrow">{{ $a['eyebrow'] }}</p>@endif
      <h1>{{ $a['title'] }}</h1>
      @if ($a['intro'])<p>{{ $a['intro'] }}</p>@endif
    </section>
  
    <!-- Who's behind it -->
    <section class="section">
      <div class="two-col">
        <div>
          @if ($a['author_heading'])<h2>{{ $a['author_heading'] }}</h2>@endif
          @if ($a['lead'])<p class="lead">{{ $a['lead'] }}</p>@endif
  
          @foreach (\App\Support\SiteContent::paragraphs($a['body']) as $para)
            <p>{!! nl2br(e($para)) !!}</p>
          @endforeach
  
          @if (! empty($a['chips']))
            <div class="chips">
              @foreach ($a['chips'] as $chip)
                <span class="chip">{{ $chip }}</span>
              @endforeach
            </div>
          @endif
        </div>
  
        <div class="portrait-card">
          @if ($portrait)
            <style>.portrait.has-photo::after { display: none; }</style>
            <div class="portrait has-photo" style="background: center/cover no-repeat url('{{ $portrait }}');"></div>
          @else
            <div class="portrait"></div>
          @endif
          <div class="portrait-meta">
            <strong>{{ $a['portrait_name'] }}</strong>
            <span>{{ $a['portrait_role'] }}</span>
          </div>
        </div>
      </div>
    </section>
  
    <!-- Stats -->
    @if (! empty($a['stats']))
      <section class="section">
        <div class="stats">
          @foreach ($a['stats'] as $stat)
            <div class="stat"><b>{{ $fill($stat['value']) }}</b><span>{{ $stat['label'] }}</span></div>
          @endforeach
        </div>
      </section>
    @endif
  
    <!-- What you'll find / Why I write -->
    @if (! empty($a['find']) || $a['why_body'])
      <section class="section">
        <div class="two-col">
          <div>
            @if (! empty($a['find']))
              <h2>{{ $a['find_heading'] }}</h2>
              <div class="timeline">
                @foreach ($a['find'] as $item)
                  <div class="timeline-item">
                    @if ($item['tag'])<small>{{ $item['tag'] }}</small>@endif
                    <h3>{{ $item['title'] }}</h3>
                    @if ($item['text'])<p>{{ $item['text'] }}</p>@endif
                  </div>
                @endforeach
              </div>
            @endif
          </div>
          <div>
            @if ($a['why_body'])
              <h2>{{ $a['why_heading'] }}</h2>
              @foreach (\App\Support\SiteContent::paragraphs($a['why_body']) as $para)
                <p>{!! nl2br(e($para)) !!}</p>
              @endforeach
            @endif
          </div>
        </div>
      </section>
    @endif
  
    <!-- CTA -->
    @if ($a['cta_title'] || $a['cta_text'])
      <section class="section">
        <div class="cta">
          @if ($a['cta_title'])<h2>{{ $a['cta_title'] }}</h2>@endif
          @if ($a['cta_text'])<p>{{ $a['cta_text'] }}</p>@endif
  
          <div class="cta-actions">
            @if ($a['cta_primary_label'] && $a['cta_primary_url'])
              <a href="{{ $a['cta_primary_url'] }}" class="btn-subscribe" @if ($isExt($a['cta_primary_url'])) target="_blank" rel="noopener noreferrer" @endif>{{ $a['cta_primary_label'] }}</a>
            @endif
            @if ($a['cta_secondary_label'] && $a['cta_secondary_url'])
              <a href="{{ $a['cta_secondary_url'] }}" class="btn-ghost" @if ($isExt($a['cta_secondary_url'])) target="_blank" rel="noopener noreferrer" @endif>{{ $a['cta_secondary_label'] }}</a>
            @endif
          </div>
        </div>
      </section>
    @endif
  
  @endif
  
  </x-layout>