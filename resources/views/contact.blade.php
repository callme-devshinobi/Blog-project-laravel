<x-layout>

  @if ($currentBlog)

    @php
      $owner   = $currentBlog->user;
      $phone   = $currentBlog->phone;
      $socials = array_filter([
        'GitHub'      => $currentBlog->github,
        'LinkedIn'    => $currentBlog->linkedin,
        'X / Twitter' => $currentBlog->twitter,
      ]);
      $hasContact = $currentBlog->contact_email || $phone || $socials;
      $isOwner    = auth()->check() && auth()->id() === $currentBlog->user_id;
    @endphp

    {{-- ================= Blogger's contact page: /@name/contact ================= --}}
    <section class="page-hero">
      <p class="eyebrow">Get in touch</p>
      <h1>Contact {{ $owner->name }}</h1>
      <p>{{ $currentBlog->bio ?: 'Have a question or a topic suggestion? Reach out using the details below.' }}</p>
    </section>

    <section class="section">
      <div class="info-stack" style="max-width: 640px; margin: 0 auto;">

        @if ($currentBlog->contact_email)
          <div class="info-card">
            <div class="info-icon">✉</div>
            <div>
              <h3>Email</h3>
              <p>The best way to reach {{ $owner->name }}.<br>
              <a href="mailto:{{ $currentBlog->contact_email }}">{{ $currentBlog->contact_email }}</a></p>
            </div>
          </div>
        @endif

        @if ($phone)
          <div class="info-card">
            <div class="info-icon">☎</div>
            <div>
              <h3>Phone</h3>
              <p>Call or message {{ $owner->name }}.<br>
              <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></p>
            </div>
          </div>
        @endif

        @if ($socials)
          <div class="social-panel">
            <h3>Find me elsewhere</h3>
            <p>You can also reach {{ $owner->name }} on these platforms.</p>
            <div class="social-links">
              @foreach ($socials as $label => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
              @endforeach
            </div>
          </div>
        @endif

        @unless ($hasContact)
          <div class="info-card">
            <div class="info-icon">✉</div>
            <div>
              <h3>No contact details yet</h3>
              <p>{{ $owner->name }} hasn't added contact details yet, but you can read their latest posts in the meantime.</p>
              @if ($isOwner)
                <p><b>Only you can see this:</b> <a href="{{ route('profile') }}">add your contact details</a> in your profile.</p>
              @endif
            </div>
          </div>
        @endunless

        <div class="info-card">
          <div class="info-icon">✎</div>
          <div>
            <h3>Read the blog</h3>
            <p>See what {{ $owner->name }} has been writing lately.<br>
            <a href="{{ $base }}/posts">Browse all posts →</a></p>
          </div>
        </div>

      </div>
    </section>

  @else

    {{-- ================= Main site Contact page: /contact (edited in Admin, Pages) ================= --}}

    @php
      $c     = \App\Support\SiteContent::contact();
      $faq   = \App\Support\SiteContent::faq();
      $links = array_values(array_filter($c['social'] ?? [], fn ($l) => ! empty($l['label']) && ! empty($l['url'])));
      $isExt = fn ($url) => \App\Support\SiteContent::isExternal($url);
      $formOn = (bool) ($c['form_enabled'] ?? true);
    @endphp

    <!-- Hero -->
    <section class="page-hero">
      @if ($c['eyebrow'])<p class="eyebrow">{{ $c['eyebrow'] }}</p>@endif
      <h1>{{ $c['title'] }}</h1>
      @if ($c['intro'])<p>{{ $c['intro'] }}</p>@endif
    </section>

    <!-- Contact -->
    <section class="section">
      <div class="contact-grid" @unless ($formOn) style="grid-template-columns:1fr;max-width:640px;margin:0 auto;" @endunless>

        <!-- Info column -->
        <div class="info-stack">

          @foreach ($c['emails'] ?? [] as $e)
            <div class="info-card">
              <div class="info-icon">{{ $loop->first ? '✉' : '◈' }}</div>
              <div>
                <h3>{{ $e['title'] }}</h3>
                <p>{{ $e['text'] }}@if (! empty($e['address']))<br>
                <a href="mailto:{{ $e['address'] }}">{{ $e['address'] }}</a>@endif</p>
              </div>
            </div>
          @endforeach

          @if ($c['response_title'] || $c['response_text'])
            <div class="info-card">
              <div class="info-icon">⚡</div>
              <div>
                <h3>{{ $c['response_title'] }}</h3>
                <p>{{ $c['response_text'] }}</p>
              </div>
            </div>
          @endif

          @if ($links)
            <div class="social-panel">
              <h3>{{ $c['social_heading'] }}</h3>
              @if ($c['social_text'])<p>{{ $c['social_text'] }}</p>@endif
              <div class="social-links">
                @foreach ($links as $l)
                  <a href="{{ $l['url'] }}" @if ($isExt($l['url'])) target="_blank" rel="noopener noreferrer" @endif>{{ $l['label'] }}</a>
                @endforeach
              </div>
            </div>
          @endif
        </div>

        <!-- Form column -->
        @if ($formOn)
          <div class="form-card">
            <h2>{{ $c['form_heading'] }}</h2>
            <p>Fields marked <i style="color:#dc2626">*</i> are required.</p>
            <form action="#" method="POST">
              <div class="form-row">
                <div class="field">
                  <label for="name">Name <i>*</i></label>
                  <input type="text" id="name" name="name" placeholder="Your name" required />
                </div>
                <div class="field">
                  <label for="email">Email <i>*</i></label>
                  <input type="email" id="email" name="email" placeholder="you@example.com" required />
                </div>
              </div>
              <div class="field">
                <label for="subject">Subject</label>
                <select id="subject" name="subject">
                  <option value="general">General enquiry</option>
                  <option value="topic">Suggest a topic</option>
                  <option value="collab">Collaboration / sponsorship</option>
                  <option value="feedback">Feedback on an article</option>
                  <option value="bug">Report a code issue</option>
                </select>
              </div>
              <div class="field">
                <label for="message">Message <i>*</i></label>
                <textarea id="message" name="message" placeholder="Write your message here…" required></textarea>
              </div>
              <div class="form-footer">
                <small>{{ $c['form_note'] }}</small>
                <button type="submit" class="btn-send">Send message →</button>
              </div>
            </form>
          </div>
        @endif

      </div>
    </section>

    <!-- FAQ -->
    @if (! empty($faq['items']))
      <section class="section">
        <div class="faq">
          <div class="section-title">
            @if ($faq['title'])<h2>{{ $faq['title'] }}</h2>@endif
            @if ($faq['subtitle'])<p>{{ $faq['subtitle'] }}</p>@endif
          </div>

          @foreach ($faq['items'] as $item)
            <details class="faq-item">
              <summary>{{ $item['q'] }}</summary>
              <div class="faq-body">{!! nl2br(e($item['a'])) !!}</div>
            </details>
          @endforeach
        </div>
      </section>
    @endif

  @endif

</x-layout>