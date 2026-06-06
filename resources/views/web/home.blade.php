@extends('layouts.web')

@section('title', $settings->firstWhere('key', 'home_hero_title')?->value ?? "Laravel CI — The Laravel Community of Côte d'Ivoire")

@section('content')

  <!-- ============ HERO ============ -->
  <section class="hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="hero-badge">
            <i class="fa-brands fa-laravel"></i>
            {{ $settings->firstWhere('key', 'home_hero_badge')?->value ?? 'Laravel 13 · PHP 8.3 · Open source' }}
          </span>
          <h1>{!! $settings->firstWhere('key', 'home_hero_title')?->value ?? "The Laravel Community of <span class=\"accent\">Côte d'Ivoire</span>" !!}</h1>
          <p class="lead">{{ $settings->firstWhere('key', 'home_hero_subtitle')?->value ?? 'Join 500+ developers — share, learn, and grow together.' }}</p>
          <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-user-plus"></i>
              {{ $settings->firstWhere('key', 'home_cta_primary_label')?->value ?? 'Join the Community' }}
            </a>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-navy btn-lg">
              <i class="fa-solid fa-comments"></i>
              {{ $settings->firstWhere('key', 'home_cta_secondary_label')?->value ?? 'Explore the Forum' }}
            </a>
          </div>
          <div class="trust-badges">
            <div class="trust-badge"><i class="fa-solid fa-people-group"></i> <span><strong>500+</strong> members</span></div>
            <div class="trust-badge"><i class="fa-solid fa-location-dot"></i> <span>Abidjan, <strong>Côte d'Ivoire</strong></span></div>
            <div class="trust-badge"><i class="fa-brands fa-github"></i> <span>Built on <strong>Laravel</strong></span></div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-art">
            <div class="art-ring"></div>
            <div class="art-blob"></div>
            <div class="ide-window">
              <div class="ide-bar">
                <span class="ide-dots"><i></i><i></i><i></i></span>
                <span class="ide-tab"><i class="fa-brands fa-php"></i> Developer.php</span>
              </div>
              <pre class="ide-code"><code><span class="ln"><span class="c-key">&lt;?php</span></span><span class="ln"> </span><span class="ln"><span class="c-key">namespace</span> App\Models;</span><span class="ln"> </span><span class="ln"><span class="c-key">class</span> <span class="c-cls">Developer</span> <span class="c-key">extends</span> <span class="c-cls">Model</span></span><span class="ln">{</span><span class="ln">    <span class="c-com">// Questions posées à Abidjan</span></span><span class="ln">    <span class="c-key">public function</span> <span class="c-fn">questions</span>()</span><span class="ln">    {</span><span class="ln">        <span class="c-key">return</span> <span class="c-var">$this</span>-&gt;<span class="c-fn">hasMany</span>(<span class="c-cls">Question</span>::<span class="c-key">class</span>)</span><span class="ln">            -&gt;<span class="c-fn">where</span>(<span class="c-str">'city'</span>, <span class="c-str">'Abidjan'</span>)</span><span class="ln">            -&gt;<span class="c-fn">latest</span>();<span class="caret"></span></span><span class="ln">    }</span><span class="ln">}</span></code></pre>
            </div>
            <div class="art-mark" style="top:-4%;left:2%"><i class="fa-brands fa-laravel"></i></div>
            <div class="term-chip" style="bottom:9%;right:-4%"><span>php artisan serve</span></div>
            <div class="card-float float-1"><i class="fa-solid fa-circle-check text-green"></i> Tests passing</div>
            <div class="card-float float-2"><i class="fa-solid fa-code-pull-request text-orange"></i> PR merged</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ MARQUEE BAND ============ -->
  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <span class="marquee-item">composer require laravel/sanctum</span>
      <span class="marquee-item">php artisan make:model Question -mfc</span>
      <span class="marquee-item">php artisan migrate</span>
      <span class="marquee-item">./vendor/bin/pest</span>
      <span class="marquee-item">php artisan queue:work</span>
      <span class="marquee-item">npm run dev</span>
      <span class="marquee-item">php artisan tinker</span>
      <span class="marquee-item">git push origin main</span>
      <span class="marquee-item">composer require laravel/sanctum</span>
      <span class="marquee-item">php artisan make:model Question -mfc</span>
      <span class="marquee-item">php artisan migrate</span>
      <span class="marquee-item">./vendor/bin/pest</span>
      <span class="marquee-item">php artisan queue:work</span>
      <span class="marquee-item">npm run dev</span>
      <span class="marquee-item">php artisan tinker</span>
      <span class="marquee-item">git push origin main</span>
    </div>
  </div>

  <!-- ============ STATS STRIP ============ -->
  <section class="stats-strip">
    <div class="container">
      <div class="row">
        @foreach($stats as $stat)
        <div class="col-6 col-md-3 reveal">
          <div class="stat-item">
            <div class="stat-icon"><i class="{{ $stat->icon }}"></i></div>
            <div class="stat-num">
              <span data-count="{{ $stat->resolvedValue() }}">{{ number_format($stat->resolvedValue()) }}</span>
              <span class="plus">{{ $stat->suffix }}</span>
            </div>
            <div class="stat-label">{{ $stat->label }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ============ LATEST FORUM QUESTIONS ============ -->
  <section class="section">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
        <div>
          <span class="section-eyebrow">Community Q&amp;A</span>
          <h2 class="section-heading mb-0">Latest forum questions</h2>
        </div>
        <a href="{{ route('forum.index') }}" class="view-all-link">View all questions <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      @forelse($questions as $question)
      <div class="q-card reveal">
        <div class="q-stats">
          <div class="q-vote"><span>{{ $question->votes_score ?? 0 }}</span><small>votes</small></div>
          <div class="q-answers {{ $question->accepted_answer_id ? 'accepted' : '' }}">
            <strong>{{ $question->answers_count }}</strong>answers
          </div>
        </div>
        <div class="q-body">
          <h3 class="q-title">
            <a href="{{ route('forum.show', $question->slug) }}">{{ $question->title }}</a>
          </h3>
          <p class="q-excerpt">{{ Str::limit(strip_tags($question->body), 120) }}</p>
          <div class="q-tags">
            @foreach($question->tags as $tag)
              <span class="tag" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">
                {{ $tag->name }}
              </span>
            @endforeach
          </div>
          <div class="q-foot">
            <div class="author-row">
              <span class="avatar avatar-sm {{ $question->user->profile?->avatar_color ?? 'av-1' }}">
                {{ strtoupper(substr($question->user->name, 0, 2)) }}
              </span>
              <div class="meta"><div class="name">{{ $question->user->name }}</div></div>
            </div>
            <span class="read-time">
              <i class="fa-regular fa-clock"></i> {{ $question->created_at->diffForHumans() }}
            </span>
          </div>
        </div>
      </div>
      @empty
        <p class="text-muted-2 text-center py-4">Aucune question pour l'instant.</p>
      @endforelse
    </div>
  </section>

  <!-- ============ LATEST ARTICLES ============ -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
        <div>
          <span class="section-eyebrow">Knowledge base</span>
          <h2 class="section-heading mb-0">Latest articles</h2>
        </div>
        <a href="{{ route('blog.index') }}" class="view-all-link">Read the blog <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        @forelse($articles as $article)
        @php
          $levelCssMap = [
            'beginner'     => ['class' => 'lv-beginner',     'badge' => 'badge-green',  'dot' => 'var(--green)',            'label' => 'Beginner'],
            'intermediate' => ['class' => 'lv-intermediate', 'badge' => 'badge-orange', 'dot' => 'var(--level-intermediate)', 'label' => 'Intermediate'],
            'advanced'     => ['class' => 'lv-advanced',     'badge' => '',             'dot' => 'var(--level-advanced)',    'label' => 'Advanced'],
          ];
          $lv       = $levelCssMap[$article->level] ?? $levelCssMap['beginner'];
          $wordCount = str_word_count(strip_tags($article->body ?? ''));
          $readTime  = max(1, (int) round($wordCount / 200));
        @endphp
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft article-card">
            <div class="level-banner {{ $lv['class'] }}"></div>
            <div class="card-pad">
              <span class="badge-pill {{ $lv['badge'] }}" @if(!$lv['badge']) style="background:#fdeaec;color:var(--level-advanced)" @endif>
                <span class="lv-dot" style="background:{{ $lv['dot'] }}"></span> {{ $lv['label'] }}
              </span>
              <h3 class="art-title">
                <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
              </h3>
              <p class="art-excerpt">{{ Str::limit($article->excerpt ?? strip_tags($article->body ?? ''), 100) }}</p>
              <div class="art-foot">
                <div class="author-row">
                  <span class="avatar avatar-sm av-1">{{ strtoupper(substr($article->author->name, 0, 2)) }}</span>
                  <div class="meta"><div class="name">{{ $article->author->name }}</div></div>
                </div>
                <span class="read-time"><i class="fa-regular fa-clock"></i> {{ $readTime }} min</span>
              </div>
            </div>
          </article>
        </div>
        @empty
          <div class="col-12">
            <p class="text-muted-2 text-center py-4">Aucun article pour l'instant.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- ============ UPCOMING EVENTS ============ -->
  <section class="section" style="background:var(--orange-50)">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
        <div>
          <span class="section-eyebrow">Meet in person &amp; online</span>
          <h2 class="section-heading mb-0">Upcoming events</h2>
        </div>
        <a href="{{ route('events.index') }}" class="view-all-link">All events <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        @forelse($events as $event)
        @php
          $typeMap = [
            'meetup'    => ['class' => 'ev-meetup',    'badge' => 'badge-orange',                             'icon' => 'fa-solid fa-people-roof',   'label' => 'Meetup'],
            'webinar'   => ['class' => 'ev-webinar',   'badge' => '',                                         'icon' => 'fa-solid fa-video',         'label' => 'Webinar'],
            'hackathon' => ['class' => 'ev-hackathon', 'badge' => '',                                         'icon' => 'fa-solid fa-laptop-code',   'label' => 'Hackathon'],
          ];
          $ev          = $typeMap[$event->type] ?? $typeMap['meetup'];
          $spotsUsed   = $event->registrations_count ?? 0;
          $capacity    = $event->capacity;
          $progress    = $capacity ? min(100, round($spotsUsed / $capacity * 100)) : 0;
          $spotsLeft   = $capacity ? max(0, $capacity - $spotsUsed) : null;
        @endphp
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft event-card">
            <div class="event-banner {{ $ev['class'] }}"></div>
            <div class="card-pad">
              <span class="badge-pill {{ $ev['badge'] }}"
                @if($event->type === 'webinar') style="background:#e7ebff;color:#4361ee"
                @elseif($event->type === 'hackathon') style="background:#f1e7ff;color:#7209b7"
                @endif>
                <i class="{{ $ev['icon'] }}"></i> {{ $ev['label'] }}
              </span>
              <h3 class="art-title">{{ $event->title }}</h3>
              <div class="d-flex flex-column gap-2 my-3" style="font-size:.88rem;color:var(--muted)">
                <span><i class="fa-regular fa-calendar text-orange me-2"></i> {{ $event->starts_at->format('D, M j · g:i A') }}</span>
                @if($event->isOnline())
                  <span><i class="fa-solid fa-globe text-orange me-2"></i> Online · {{ $event->online_url }}</span>
                @else
                  <span><i class="fa-solid fa-location-dot text-orange me-2"></i> {{ $event->location }}</span>
                @endif
              </div>
              @if($capacity)
              <div class="spots-label">
                <span>{{ $spotsUsed }} / {{ $capacity }} spots</span>
                <span>{{ $spotsLeft }} left</span>
              </div>
              <div class="progress-spots mb-3"><div class="bar" style="width:{{ $progress }}%"></div></div>
              @endif
              <a href="{{ route('events.index') }}" class="btn btn-brand w-100"><i class="fa-solid fa-ticket"></i> Register</a>
            </div>
          </article>
        </div>
        @empty
          <div class="col-12">
            <p class="text-muted-2 text-center py-4">Aucun événement à venir pour l'instant.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- ============ PARTNERS ============ -->
  <section class="section-sm">
    <div class="container">
      <p class="text-center text-muted-2 mb-4" style="font-weight:500;letter-spacing:.05em">Part of the global Laravel community</p>
      <div class="row g-3 justify-content-center">
        @foreach($partners as $partner)
        <div class="col-6 col-md-3 reveal">
          <div class="partner-logo">
            @if($partner->logo)
              <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->name }}" style="height:32px">
            @else
              <i class="{{ $partner->icon ?? 'fa-solid fa-hippo' }}"></i>
            @endif
            {{ $partner->name }}
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ============ FINAL CTA ============ -->
  <section class="section pt-0">
    <div class="container">
      <div class="cta-banner reveal">
        <img src="{{ asset('assets/web/img/mascot.png') }}" class="cta-mascot d-none d-xl-block" alt="" aria-hidden="true" />
        <h2 class="mb-3">{{ $settings->firstWhere('key', 'home_cta_banner_title')?->value ?? 'Ready to build the future of Ivorian tech?' }}</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.92);max-width:40rem;margin-inline:auto">
          {{ $settings->firstWhere('key', 'home_cta_banner_text')?->value ?? 'Sign in with GitHub, ask your first question, and meet 500+ developers who have your back.' }}
        </p>
        <div class="cta-cmd"><span class="cta-cmd-prompt">$</span> composer create-project laravel-ci/community <button class="cta-cmd-copy" type="button" aria-label="Copy"><i class="fa-regular fa-copy"></i></button></div>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Join the Community</a>
      </div>
    </div>
  </section>

@endsection
