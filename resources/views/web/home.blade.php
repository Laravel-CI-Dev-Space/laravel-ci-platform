@extends('layouts.web')

@section('title', 'Laravel CI — The Laravel Community of Côte d\'Ivoire')
@section('description', 'Join 500+ Ivorian Laravel & PHP developers. Share knowledge, find jobs, attend events, and grow together in Abidjan and across the diaspora.')

@section('content')

  <!-- ============ HERO ============ -->
  <section class="hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="hero-badge"><i class="fa-brands fa-laravel"></i> {{ $s['home_hero_badge'] }}</span>
          <h1>{{ $s['home_hero_title'] }}</h1>
          <p class="lead">{{ $s['home_hero_lead'] }}</p>
          <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="{{ route('join') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-user-plus"></i> {{ $s['home_hero_cta_primary'] }}</a>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-navy btn-lg"><i class="fa-solid fa-comments"></i> {{ $s['home_hero_cta_secondary'] }}</a>
          </div>
          <div class="trust-badges">
            <div class="trust-badge"><i class="fa-solid fa-people-group"></i> <span><strong>{{ $s['home_stats_members'] }}+</strong> members</span></div>
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
        <div class="col-6 col-md-3 reveal">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-num"><span data-count="{{ $s['home_stats_members'] }}">{{ $s['home_stats_members'] }}</span><span class="plus">+</span></div>
            <div class="stat-label">Members</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.1">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-circle-question"></i></div>
            <div class="stat-num"><span data-count="{{ $s['home_stats_questions'] }}">{{ $s['home_stats_questions'] }}</span><span class="plus">+</span></div>
            <div class="stat-label">Questions</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.2">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-num"><span data-count="{{ $s['home_stats_events'] }}">{{ $s['home_stats_events'] }}</span><span class="plus">+</span></div>
            <div class="stat-label">Events</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.3">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
            <div class="stat-num"><span data-count="{{ $s['home_stats_articles'] }}">{{ $s['home_stats_articles'] }}</span><span class="plus">+</span></div>
            <div class="stat-label">Articles</div>
          </div>
        </div>
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

      <div class="q-card reveal">
        <div class="q-stats">
          <div class="q-vote"><span>42</span><small>votes</small></div>
          <div class="q-answers accepted"><strong>5</strong>answers</div>
        </div>
        <div class="q-body">
          <h3 class="q-title"><a href="{{ route('forum.show', 1) }}">How to structure a multi-tenant Laravel app for a fintech in Abidjan?</a></h3>
          <p class="q-excerpt">We're building a mobile money aggregator and need to isolate tenant data cleanly. Single database with scopes or separate databases per tenant?</p>
          <div class="q-tags">
            <span class="tag">laravel-11</span><span class="tag">multi-tenancy</span><span class="tag">architecture</span>
          </div>
          <div class="q-foot">
            <div class="author-row">
              <span class="avatar avatar-sm av-1">KA</span>
              <div class="meta"><div class="name">Kouamé Aristide</div></div>
            </div>
            <span class="read-time"><i class="fa-regular fa-clock"></i> 2 hours ago</span>
          </div>
        </div>
      </div>

      <div class="q-card reveal" data-delay="0.08">
        <div class="q-stats">
          <div class="q-vote"><span>28</span><small>votes</small></div>
          <div class="q-answers"><strong>3</strong>answers</div>
        </div>
        <div class="q-body">
          <h3 class="q-title"><a href="{{ route('forum.show', 2) }}">Best practice for integrating Wave &amp; Orange Money APIs with Laravel?</a></h3>
          <p class="q-excerpt">Looking for a clean abstraction layer to handle multiple mobile money providers. Should I use a Payment contract with driver implementations?</p>
          <div class="q-tags">
            <span class="tag">payments</span><span class="tag">api</span><span class="tag">mobile-money</span>
          </div>
          <div class="q-foot">
            <div class="author-row">
              <span class="avatar avatar-sm av-3">FD</span>
              <div class="meta"><div class="name">Fatou Diallo</div></div>
            </div>
            <span class="read-time"><i class="fa-regular fa-clock"></i> 6 hours ago</span>
          </div>
        </div>
      </div>

      <div class="q-card reveal" data-delay="0.16">
        <div class="q-stats">
          <div class="q-vote"><span>17</span><small>votes</small></div>
          <div class="q-answers"><strong>2</strong>answers</div>
        </div>
        <div class="q-body">
          <h3 class="q-title"><a href="{{ route('forum.show', 3) }}">Queue workers keep dying on a low-memory VPS — how to keep them alive?</a></h3>
          <p class="q-excerpt">My Horizon workers crash overnight on a 1GB DigitalOcean droplet. Supervisor restarts them but jobs pile up. What's the right setup?</p>
          <div class="q-tags">
            <span class="tag">queues</span><span class="tag">horizon</span><span class="tag">devops</span>
          </div>
          <div class="q-foot">
            <div class="author-row">
              <span class="avatar avatar-sm av-4">YT</span>
              <div class="meta"><div class="name">Yao Térence</div></div>
            </div>
            <span class="read-time"><i class="fa-regular fa-clock"></i> Yesterday</span>
          </div>
        </div>
      </div>
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
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft article-card">
            <div class="level-banner lv-beginner"></div>
            <div class="card-pad">
              <span class="badge-pill badge-green"><span class="lv-dot" style="background:var(--green)"></span> Beginner</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 1) }}">Getting started with Laravel 11 in Abidjan: your first deploy</a></h3>
              <p class="art-excerpt">A no-nonsense guide to shipping your first Laravel app to a local VPS, from <code class="mono">.env</code> to production in an afternoon.</p>
              <div class="art-foot">
                <div class="author-row">
                  <span class="avatar avatar-sm av-2">MK</span>
                  <div class="meta"><div class="name">Mariam Koné</div></div>
                </div>
                <span class="read-time"><i class="fa-regular fa-clock"></i> 6 min</span>
              </div>
            </div>
          </article>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.1">
          <article class="card-soft article-card">
            <div class="level-banner lv-intermediate"></div>
            <div class="card-pad">
              <span class="badge-pill badge-orange"><span class="lv-dot" style="background:var(--level-intermediate)"></span> Intermediate</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 2) }}">Building a Wave payment integration the clean way</a></h3>
              <p class="art-excerpt">Wrap mobile money providers behind a single contract, test webhooks locally, and handle idempotency like a pro.</p>
              <div class="art-foot">
                <div class="author-row">
                  <span class="avatar avatar-sm av-1">SB</span>
                  <div class="meta"><div class="name">Serge Brou</div></div>
                </div>
                <span class="read-time"><i class="fa-regular fa-clock"></i> 11 min</span>
              </div>
            </div>
          </article>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.2">
          <article class="card-soft article-card">
            <div class="level-banner lv-advanced"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)"><span class="lv-dot" style="background:var(--level-advanced)"></span> Advanced</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 3) }}">Scaling Laravel queues with Horizon on a budget</a></h3>
              <p class="art-excerpt">Tune Redis, supervisor and worker memory so your jobs survive the night — even on a 1GB droplet.</p>
              <div class="art-foot">
                <div class="author-row">
                  <span class="avatar avatar-sm av-5">AD</span>
                  <div class="meta"><div class="name">Aïcha Doumbia</div></div>
                </div>
                <span class="read-time"><i class="fa-regular fa-clock"></i> 9 min</span>
              </div>
            </div>
          </article>
        </div>
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
        <div class="col-md-6 col-lg-4 reveal">
          <article class="card-soft event-card">
            <div class="event-banner ev-meetup"></div>
            <div class="card-pad">
              <span class="badge-pill badge-orange"><i class="fa-solid fa-people-roof"></i> Meetup</span>
              <h3 class="art-title">Laravel CI Meetup #04 — Eloquent Deep Dive</h3>
              <div class="d-flex flex-column gap-2 my-3" style="font-size:.88rem;color:var(--muted)">
                <span><i class="fa-regular fa-calendar text-orange me-2"></i> Sat, June 14 · 2:00 PM</span>
                <span><i class="fa-solid fa-location-dot text-orange me-2"></i> Jokkolabs, Cocody, Abidjan</span>
              </div>
              <div class="spots-label"><span>32 / 50 spots</span><span>18 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:64%"></div></div>
              <a href="{{ route('events.index') }}" class="btn btn-brand w-100"><i class="fa-solid fa-ticket"></i> Register</a>
            </div>
          </article>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.1">
          <article class="card-soft event-card">
            <div class="event-banner ev-webinar"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:#e7ebff;color:#4361ee"><i class="fa-solid fa-video"></i> Webinar</span>
              <h3 class="art-title">Testing Laravel APIs with Pest — live coding</h3>
              <div class="d-flex flex-column gap-2 my-3" style="font-size:.88rem;color:var(--muted)">
                <span><i class="fa-regular fa-calendar text-orange me-2"></i> Wed, June 18 · 7:00 PM</span>
                <span><i class="fa-solid fa-globe text-orange me-2"></i> Online · Google Meet</span>
              </div>
              <div class="spots-label"><span>87 / 200 spots</span><span>113 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:43%"></div></div>
              <a href="{{ route('events.index') }}" class="btn btn-brand w-100"><i class="fa-solid fa-ticket"></i> Register</a>
            </div>
          </article>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.2">
          <article class="card-soft event-card">
            <div class="event-banner ev-hackathon"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:#f1e7ff;color:#7209b7"><i class="fa-solid fa-laptop-code"></i> Hackathon</span>
              <h3 class="art-title">CivTech Hack — 48h building for local impact</h3>
              <div class="d-flex flex-column gap-2 my-3" style="font-size:.88rem;color:var(--muted)">
                <span><i class="fa-regular fa-calendar text-orange me-2"></i> Jul 5–6 · All weekend</span>
                <span><i class="fa-solid fa-location-dot text-orange me-2"></i> Orange Digital Center</span>
              </div>
              <div class="spots-label"><span>64 / 80 spots</span><span>16 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:80%"></div></div>
              <a href="{{ route('events.index') }}" class="btn btn-brand w-100"><i class="fa-solid fa-ticket"></i> Register</a>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ PARTNERS ============ -->
  <section class="section-sm">
    <div class="container">
      <p class="text-center text-muted-2 mb-4" style="font-weight:500;letter-spacing:.05em">Part of the global Laravel community</p>
      <div class="row g-3 justify-content-center">
        <div class="col-6 col-md-3 reveal"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel France</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.08"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Cameroun</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.16"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Sénégal</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.24"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Nigeria</div></div>
      </div>
    </div>
  </section>

  <!-- ============ FINAL CTA ============ -->
  <section class="section pt-0">
    <div class="container">
      <div class="cta-banner reveal">
        <img src="{{ asset('assets/web/img/mascot.png') }}" class="cta-mascot d-none d-xl-block" alt="" aria-hidden="true" />
        <h2 class="mb-3">{{ $s['home_cta_title'] }}</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.92);max-width:40rem;margin-inline:auto">{{ $s['home_cta_lead'] }}</p>
        <div class="cta-cmd"><span class="cta-cmd-prompt">$</span> composer create-project laravel-ci/community <button class="cta-cmd-copy" type="button" aria-label="Copy"><i class="fa-regular fa-copy"></i></button></div>
        <a href="{{ route('join') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> {{ $s['home_cta_button'] }}</a>
      </div>
    </div>
  </section>

@endsection
