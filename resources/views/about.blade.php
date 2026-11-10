<x-layout>

  @if ($currentBlog)
  
    @php
      $owner = $currentBlog->user;
      $stack = $currentBlog->tech_stack ?? [];
      $isOwner = auth()->check() && auth()->id() === $currentBlog->user_id;
      $toParagraphs = fn ($text) => preg_split('/\R{2,}/', trim($text ?? ''));
       // shown when the owner hasn't written a bio yet
    $defaultAbout = $currentBlog->title . ' is a blog on DevNotes, a platform where developers, creators and curious minds get their own space to write, share and learn in public.';
 
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
          <p>Every blog on DevNotes belongs to its author: their own posts, their own address, and, if they like, their own domain.</p>
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
  
    {{-- ================= Main DevNotes About page: /about ================= --}}
  
     <!-- Hero -->
    <section class="page-hero">
      <p class="eyebrow">About DevNotes</p>
      <h1>Learn, build and grow — one post at a time.</h1>
      <p>DevNotes is a blog for developers, creators and curious minds. Here I share practical tutorials, honest lessons from real projects, and the habits that keep me improving.</p>
    </section>
  
    <!-- Who's behind it -->
    <section class="section">
      <div class="two-col">
        <div>
          <h2>Hi, I'm Dennis Onwuegbu 👋</h2>
          <p class="lead">I'm a software developer and writer who believes the best way to learn is to build — and then write about it.</p>
          <p>I started DevNotes to document my own journey with Laravel, JavaScript and web development, and it quickly grew into a place where beginners and experienced devs alike can find clear, practical guidance.</p>
          <p>Everything on this blog comes from real projects and real mistakes. No fluff, no gatekeeping — just what actually works.</p>
          <div class="chips">
            <span class="chip">Laravel</span>
            <span class="chip">PHP</span>
            <span class="chip">JavaScript</span>
            <span class="chip">CSS</span>
            <span class="chip">MySQL</span>
            <span class="chip">HTML</span>
            <span class="chip">Iconic</span>
  
          </div>
        </div>
        <div class="portrait-card">
          <div class="portrait"></div>
          <div class="portrait-meta">
            <strong>Dennis Onwuegbu</strong>
            <span>Writer & Developer</span>
          </div>
        </div>
      </div>
    </section>
  
    <!-- Stats -->
    <section class="section">
      <div class="stats">
        <div class="stat"><b>40+</b><span>Articles published</span></div>
        <div class="stat"><b>5</b><span>Categories covered</span></div>
        <div class="stat"><b>120K</b><span>Monthly readers</span></div>
        <div class="stat"><b>2024</b><span>DevNotes founded</span></div>
      </div>
    </section>
  
    <!-- What you'll find -->
    <section class="section">
      <div class="two-col">
        <div>
          <h2>What you'll find here</h2>
          <div class="timeline">
            <div class="timeline-item">
              <small>TUTORIALS</small>
              <h3>Step-by-step guides</h3>
              <p>From "Getting Started with Laravel" to full project builds — written for humans, not robots.</p>
            </div>
            <div class="timeline-item">
              <small>TOOLS</small>
              <h3>Essential tools & resources</h3>
              <p>VS Code extensions, packages and workflows that genuinely make development faster and more enjoyable.</p>
            </div>
            <div class="timeline-item">
              <small>MINDSET</small>
              <h3>Honest career insights</h3>
              <p>Habits, lessons and the occasional hard truth about growing as a developer.</p>
            </div>
          </div>
        </div>
        <div>
          <h2>Why I write</h2>
          <p>Teaching is the best form of learning. Every tutorial on DevNotes forces me to understand a topic deeply enough to explain it simply.</p>
          <p>If a post helps one person avoid a bug I spent three hours debugging, it's worth it.</p>
          <p>New articles drop regularly — subscribe so you never miss one.</p>
        </div>
      </div>
    </section>
  
    <!-- CTA -->
    <section class="section">
      <div class="cta">
        <h2>Let's build something together</h2>
        <p>Subscribe for new tutorials every week, or reach out if you have a topic you'd love to see covered.</p>
        <div class="cta-actions">
          <button class="btn-subscribe">Subscribe to the newsletter</button>
          <button class="btn-ghost">Get in touch →</button>
        </div>
      </div>
    </section>
  
  @endif
  
  </x-layout>