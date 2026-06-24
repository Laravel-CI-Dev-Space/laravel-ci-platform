@extends('layouts.web')

@section('title', $settings->firstWhere('key', 'home_hero_title')?->value ?? "Laravel CI · La communauté Laravel de Côte d'Ivoire")
@section('description', $settings->firstWhere('key', 'home_hero_subtitle')?->value ?? 'Join 500+ Ivorian Laravel & PHP developers. Share knowledge, find jobs, attend events, and grow together.')

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
          <h1>
            @if ($heroTitle = $settings->firstWhere('key', 'home_hero_title')?->value)
              {{ $heroTitle }}
            @else
              La communauté Laravel de <span class="accent">Côte d'Ivoire</span>
            @endif
          </h1>
          <p class="lead">{{ $settings->firstWhere('key', 'home_hero_subtitle')?->value ?? 'Rejoins 900+ développeurs ivoiriens — partage, apprends et grandis avec la communauté.' }}</p>
          <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="{{ route('join') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-user-plus"></i>
              {{ $settings->firstWhere('key', 'home_cta_primary_label')?->value ?? 'Rejoindre la communauté' }}
            </a>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-navy btn-lg">
              <i class="fa-solid fa-comments"></i>
              {{ $settings->firstWhere('key', 'home_cta_secondary_label')?->value ?? 'Accéder au forum' }}
            </a>
          </div>
          <div class="trust-badges">
            <div class="trust-badge"><i class="fa-brands fa-linkedin"></i> <span><strong>900+</strong> sur LinkedIn</span></div>
            <div class="trust-badge"><i class="fa-brands fa-whatsapp"></i> <span><strong>340+</strong> sur WhatsApp</span></div>
            <div class="trust-badge"><i class="fa-solid fa-location-dot"></i> <span>Abidjan, <strong>Côte d'Ivoire</strong></span></div>
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

  <!-- ============ MANIFESTE ============ -->
  <section style="background:var(--navy);padding:3.5rem 0">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center reveal">
          <p style="font-size:clamp(1.05rem,2vw,1.4rem);line-height:1.65;color:#fff;font-style:italic;margin:0">
            "La Côte d'Ivoire ne manque pas de développeurs talentueux. Elle manque d'un espace commun pour les réunir, les faire grandir ensemble et les rendre visibles au reste du monde tech."
          </p>
          <p style="color:rgba(255,255,255,.45);font-size:.82rem;margin-top:1.1rem;margin-bottom:0;font-weight:600;letter-spacing:.05em;text-transform:uppercase">Manifeste fondateur · Laravel Côte d'Ivoire</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ LATEST FORUM QUESTIONS ============ -->
  <section class="section">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
        <div>
          <span class="section-eyebrow">Entraide communautaire</span>
          <h2 class="section-heading mb-0">Dernières questions du forum</h2>
        </div>
        <a href="{{ route('forum.index') }}" class="view-all-link">Toutes les questions <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      @forelse($questions as $question)
      <div class="q-card reveal">
        <div class="q-stats">
          <div class="q-vote"><span>{{ $question->votes_score ?? 0 }}</span><small>votes</small></div>
          <div class="q-answers {{ $question->accepted_answer_id ? 'accepted' : '' }}">
            <strong>{{ $question->answers_count }}</strong>réponses
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
          <span class="section-eyebrow">Base de connaissances</span>
          <h2 class="section-heading mb-0">Derniers articles</h2>
        </div>
        <a href="{{ route('blog.index') }}" class="view-all-link">Lire le blog <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        @forelse($articles as $article)
        @php
          $wordCount = str_word_count(strip_tags($article->body ?? ''));
          $readTime  = max(1, (int) round($wordCount / 200));
        @endphp
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft article-card">
            <div class="level-banner {{ $article->level->bannerClass() }}"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:{{ $article->level->badgeBackground() }};color:{{ $article->level->accentColor() }}">
                <span class="lv-dot" style="background:{{ $article->level->accentColor() }}"></span> {{ $article->level->label() }}
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
          <span class="section-eyebrow">En présentiel et en ligne</span>
          <h2 class="section-heading mb-0">Prochains événements</h2>
        </div>
        <a href="{{ route('events.index') }}" class="view-all-link">Tous les événements <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        @forelse($events as $event)
        @php
          $spotsUsed   = $event->registrations_count ?? 0;
          $capacity    = $event->capacity;
          $progress    = $capacity ? min(100, round($spotsUsed / $capacity * 100)) : 0;
          $spotsLeft   = $capacity ? max(0, $capacity - $spotsUsed) : null;
        @endphp
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft event-card">
            <div class="event-banner {{ $event->type->bannerClass() }}"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:{{ $event->type->background() }};color:{{ $event->type->color() }}">
                <i class="{{ $event->type->icon() }}"></i> {{ $event->type->label() }}
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
                <span>{{ $spotsUsed }} / {{ $capacity }} places</span>
                <span>{{ $spotsLeft }} restantes</span>
              </div>
              <div class="progress-spots mb-3"><div class="bar" style="width:{{ $progress }}%"></div></div>
              @endif
              <a href="{{ route('events.index') }}" class="btn btn-brand w-100"><i class="fa-solid fa-ticket"></i> S'inscrire</a>
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
      <p class="text-center text-muted-2 mb-4" style="font-weight:500;letter-spacing:.05em">Membre de la communauté Laravel mondiale</p>
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
        <h2 class="mb-3">{{ $settings->firstWhere('key', 'home_cta_banner_title')?->value ?? "Prêt à construire l'avenir de la tech ivoirienne ?" }}</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.92);max-width:40rem;margin-inline:auto">
          {{ $settings->firstWhere('key', 'home_cta_banner_text')?->value ?? 'Connecte-toi avec GitHub, pose ta première question et rejoins 900+ développeurs qui progressent ensemble.' }}
        </p>
        <div class="cta-cmd"><span class="cta-cmd-prompt">$</span> composer create-project laravel-ci/community <button class="cta-cmd-copy" type="button" aria-label="Copy"><i class="fa-regular fa-copy"></i></button></div>
        <a href="{{ route('join') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Rejoindre la communauté</a>
      </div>
    </div>
  </section>

@endsection
