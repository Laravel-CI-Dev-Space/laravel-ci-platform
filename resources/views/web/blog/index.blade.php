@extends('layouts.web')

@section('title', 'Blog & Resources — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar"><a href="{{ route('home') }}">Home</a> <i class="fa-solid fa-chevron-right"></i> <span>Blog</span></div>
          <h1 class="mb-2">Blog &amp; Resources</h1>
          <p class="lead mb-4">Tutorials, deep dives and downloadable resources written by the Ivorian Laravel community.</p>
          <div class="filter-pills" data-filter-group data-filter-target=".article-col">
            <button class="filter-pill active" data-filter="all">All</button>
            <button class="filter-pill" data-filter="beginner">Beginner</button>
            <button class="filter-pill" data-filter="intermediate">Intermediate</button>
            <button class="filter-pill" data-filter="advanced">Advanced</button>
          </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="mascot-art">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- LIVEWIRE: @livewire('blog.article-list') --}}

  <section class="section">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-6 col-lg-4 article-col reveal" data-category="beginner">
          <article class="card-soft article-card">
            <div class="level-banner lv-beginner"></div>
            <div class="card-pad">
              <span class="badge-pill badge-green"><span class="lv-dot" style="background:var(--green)"></span> Beginner</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 1) }}">Getting started with Laravel 11 in Abidjan: your first deploy</a></h3>
              <p class="art-excerpt">A no-nonsense guide to shipping your first Laravel app to a local VPS, from <code class="mono">.env</code> to production.</p>
              <div class="q-tags"><span class="tag">basics</span><span class="tag">deployment</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-2">MK</span><div class="meta"><div class="name">Mariam Koné</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 6 min</span></div>
            </div>
          </article>
        </div>

        <div class="col-md-6 col-lg-4 article-col reveal" data-category="intermediate" data-delay="0.05">
          <article class="card-soft article-card">
            <div class="level-banner lv-intermediate"></div>
            <div class="card-pad">
              <span class="badge-pill badge-orange"><span class="lv-dot" style="background:var(--level-intermediate)"></span> Intermediate</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 2) }}">Building a Wave payment integration the clean way</a></h3>
              <p class="art-excerpt">Wrap mobile money providers behind a single contract, test webhooks locally, and handle idempotency like a pro.</p>
              <div class="q-tags"><span class="tag">payments</span><span class="tag">api</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-1">SB</span><div class="meta"><div class="name">Serge Brou</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 11 min</span></div>
            </div>
          </article>
        </div>

        <div class="col-md-6 col-lg-4 article-col reveal" data-category="advanced" data-delay="0.1">
          <article class="card-soft article-card">
            <div class="level-banner lv-advanced"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)"><span class="lv-dot" style="background:var(--level-advanced)"></span> Advanced</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 3) }}">Scaling Laravel queues with Horizon on a budget</a></h3>
              <p class="art-excerpt">Tune Redis, supervisor and worker memory so your jobs survive the night — even on a 1GB droplet.</p>
              <div class="q-tags"><span class="tag">queues</span><span class="tag">devops</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-5">AD</span><div class="meta"><div class="name">Aïcha Doumbia</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 9 min</span></div>
            </div>
          </article>
        </div>

        <div class="col-md-6 col-lg-4 article-col reveal" data-category="beginner">
          <article class="card-soft article-card">
            <div class="level-banner lv-beginner"></div>
            <div class="card-pad">
              <span class="badge-pill badge-green"><span class="lv-dot" style="background:var(--green)"></span> Beginner</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 4) }}">Understanding Eloquent relationships with real examples</a></h3>
              <p class="art-excerpt">hasMany, belongsToMany and morphs explained with a marketplace schema you'll actually recognise.</p>
              <div class="q-tags"><span class="tag">eloquent</span><span class="tag">database</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-4">YT</span><div class="meta"><div class="name">Yao Térence</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 8 min</span></div>
            </div>
          </article>
        </div>

        <div class="col-md-6 col-lg-4 article-col reveal" data-category="intermediate" data-delay="0.05">
          <article class="card-soft article-card">
            <div class="level-banner lv-intermediate"></div>
            <div class="card-pad">
              <span class="badge-pill badge-orange"><span class="lv-dot" style="background:var(--level-intermediate)"></span> Intermediate</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 5) }}">Real-time dashboards with Livewire 3 &amp; Alpine</a></h3>
              <p class="art-excerpt">Build a live transactions dashboard with polling, lazy loading and zero custom JavaScript.</p>
              <div class="q-tags"><span class="tag">livewire</span><span class="tag">frontend</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-6">NG</span><div class="meta"><div class="name">Nadège Gbané</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 13 min</span></div>
            </div>
          </article>
        </div>

        <div class="col-md-6 col-lg-4 article-col reveal" data-category="advanced" data-delay="0.1">
          <article class="card-soft article-card">
            <div class="level-banner lv-advanced"></div>
            <div class="card-pad">
              <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)"><span class="lv-dot" style="background:var(--level-advanced)"></span> Advanced</span>
              <h3 class="art-title"><a href="{{ route('blog.show', 6) }}">Designing a multi-region Laravel architecture for West Africa</a></h3>
              <p class="art-excerpt">Latency, data residency and failover strategies for serving users from Abidjan to Lagos.</p>
              <div class="q-tags"><span class="tag">architecture</span><span class="tag">scaling</span></div>
              <div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-1">SB</span><div class="meta"><div class="name">Serge Brou</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 16 min</span></div>
            </div>
          </article>
        </div>
      </div>

      <!-- ============ RESOURCES ============ -->
      <div class="mt-5 pt-4">
        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
          <div>
            <span class="section-eyebrow">Free downloads</span>
            <h2 class="section-heading mb-0">Downloadable resources</h2>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6 reveal">
            <div class="resource-card">
              <div class="file-icon fi-pdf"><i class="fa-solid fa-file-pdf"></i></div>
              <div class="flex-grow-1">
                <h3 style="font-size:1.05rem;margin-bottom:.2rem" class="text-navy">Laravel CI Coding Standards (FR)</h3>
                <p class="mb-2" style="font-size:.88rem;color:var(--muted)">Our community PHP &amp; Laravel style guide, in French.</p>
                <span class="read-time"><i class="fa-solid fa-download"></i> 1,842 downloads</span>
              </div>
              <a href="#" class="btn btn-outline-brand"><i class="fa-solid fa-download"></i></a>
            </div>
          </div>
          <div class="col-md-6 reveal" data-delay="0.05">
            <div class="resource-card">
              <div class="file-icon fi-zip"><i class="fa-solid fa-file-zipper"></i></div>
              <div class="flex-grow-1">
                <h3 style="font-size:1.05rem;margin-bottom:.2rem" class="text-navy">Mobile Money starter kit</h3>
                <p class="mb-2" style="font-size:.88rem;color:var(--muted)">A boilerplate repo with Wave &amp; Orange Money drivers.</p>
                <span class="read-time"><i class="fa-solid fa-download"></i> 967 downloads</span>
              </div>
              <a href="#" class="btn btn-outline-brand"><i class="fa-solid fa-download"></i></a>
            </div>
          </div>
          <div class="col-md-6 reveal">
            <div class="resource-card">
              <div class="file-icon fi-doc"><i class="fa-solid fa-file-lines"></i></div>
              <div class="flex-grow-1">
                <h3 style="font-size:1.05rem;margin-bottom:.2rem" class="text-navy">Deployment checklist</h3>
                <p class="mb-2" style="font-size:.88rem;color:var(--muted)">Everything to verify before shipping Laravel to production.</p>
                <span class="read-time"><i class="fa-solid fa-download"></i> 1,205 downloads</span>
              </div>
              <a href="#" class="btn btn-outline-brand"><i class="fa-solid fa-download"></i></a>
            </div>
          </div>
          <div class="col-md-6 reveal" data-delay="0.05">
            <div class="resource-card">
              <div class="file-icon fi-vid"><i class="fa-solid fa-circle-play"></i></div>
              <div class="flex-grow-1">
                <h3 style="font-size:1.05rem;margin-bottom:.2rem" class="text-navy">Meetup #03 recording — Testing with Pest</h3>
                <p class="mb-2" style="font-size:.88rem;color:var(--muted)">Full 90-minute session, recorded in Abidjan.</p>
                <span class="read-time"><i class="fa-solid fa-download"></i> 612 views</span>
              </div>
              <a href="#" class="btn btn-outline-brand"><i class="fa-solid fa-play"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
