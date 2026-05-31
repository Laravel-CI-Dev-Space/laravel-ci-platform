@extends('layouts.web')

@section('title', 'Forum — Laravel CI')

@section('content')

  <!-- ============ SUBHEADER ============ -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Forum</span>
          </div>
          <h1 class="mb-2">Community Forum</h1>
          <p class="lead mb-3">Ask questions, share solutions, and learn from 500+ Ivorian Laravel developers.</p>
          @auth
            <a href="{{ route('forum.create') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-circle-plus"></i> Ask a Question</a>
          @else
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-circle-plus"></i> Ask a Question</a>
          @endauth
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

  {{-- LIVEWIRE: @livewire('forum.question-list') --}}

  <section class="section-sm">
    <div class="container">
      <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#forumSidebar">
        <i class="fa-solid fa-sliders"></i> Filters &amp; tags
      </button>
      <div class="row g-4">

        <!-- SIDEBAR -->
        <div class="col-lg-3">
          <div class="collapse d-lg-block" id="forumSidebar">
            <div class="sidebar-card">
              <div class="search-field">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" class="form-control" placeholder="Search questions…" />
              </div>
            </div>
            <div class="sidebar-card">
              <div class="sidebar-title">Sort by</div>
              <div class="filter-pills">
                <button class="filter-pill active">Newest</button>
                <button class="filter-pill">Top voted</button>
                <button class="filter-pill">Unanswered</button>
              </div>
            </div>
            <div class="sidebar-card">
              <div class="sidebar-title">Popular tags</div>
              <div class="tag-list">
                <div class="tag-list-item"><span class="mono">laravel-11</span><span class="count">312</span></div>
                <div class="tag-list-item"><span class="mono">eloquent</span><span class="count">208</span></div>
                <div class="tag-list-item"><span class="mono">livewire</span><span class="count">164</span></div>
                <div class="tag-list-item"><span class="mono">payments</span><span class="count">97</span></div>
                <div class="tag-list-item"><span class="mono">queues</span><span class="count">81</span></div>
                <div class="tag-list-item"><span class="mono">deployment</span><span class="count">73</span></div>
              </div>
            </div>
            <div class="sidebar-card">
              <div class="sidebar-title">Top contributors</div>
              <div class="contributor-row"><span class="avatar avatar-sm av-1">SB</span><span class="name" style="font-weight:600">Serge Brou</span><span class="rep">12.4k</span></div>
              <div class="contributor-row"><span class="avatar avatar-sm av-3">FD</span><span class="name" style="font-weight:600">Fatou Diallo</span><span class="rep">9.1k</span></div>
              <div class="contributor-row"><span class="avatar avatar-sm av-5">AD</span><span class="name" style="font-weight:600">Aïcha Doumbia</span><span class="rep">7.8k</span></div>
            </div>
          </div>
        </div>

        <!-- MAIN -->
        <div class="col-lg-9">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted-2"><strong class="text-navy">1,204</strong> questions</span>
            <div class="filter-pills">
              <button class="filter-pill active">All</button>
              <button class="filter-pill">Solved</button>
              <button class="filter-pill">Open</button>
            </div>
          </div>

          <!-- Pinned -->
          <div class="q-card pinned">
            <div class="q-stats">
              <div class="q-vote"><span>96</span><small>votes</small></div>
              <div class="q-answers accepted"><strong>8</strong>answers</div>
            </div>
            <div class="q-body">
              <div class="pin-flag"><i class="fa-solid fa-thumbtack"></i> Pinned by moderators</div>
              <h3 class="q-title"><a href="{{ route('forum.show', 1) }}">Read first: the Laravel CI community guidelines &amp; how to ask great questions</a></h3>
              <p class="q-excerpt">A welcome thread for newcomers — how we run the forum, formatting your code, and getting fast, helpful answers.</p>
              <div class="q-tags"><span class="tag">meta</span><span class="tag">welcome</span></div>
              <div class="q-foot">
                <div class="author-row"><span class="avatar avatar-sm av-2">LC</span><div class="meta"><div class="name">Laravel CI Team</div></div></div>
                <span class="read-time"><i class="fa-regular fa-clock"></i> Pinned</span>
              </div>
            </div>
          </div>

          <div class="q-card">
            <div class="q-stats"><div class="q-vote"><span>42</span><small>votes</small></div><div class="q-answers accepted"><strong>5</strong>answers</div></div>
            <div class="q-body">
              <h3 class="q-title"><a href="{{ route('forum.show', 2) }}">How to structure a multi-tenant Laravel app for a fintech in Abidjan?</a></h3>
              <p class="q-excerpt">We're building a mobile money aggregator and need to isolate tenant data cleanly.</p>
              <div class="q-tags"><span class="tag">laravel-11</span><span class="tag">multi-tenancy</span><span class="tag">architecture</span></div>
              <div class="q-foot"><div class="author-row"><span class="avatar avatar-sm av-1">KA</span><div class="meta"><div class="name">Kouamé Aristide</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 2 hours ago</span></div>
            </div>
          </div>

          <div class="q-card">
            <div class="q-stats"><div class="q-vote"><span>28</span><small>votes</small></div><div class="q-answers accepted"><strong>3</strong>answers</div></div>
            <div class="q-body">
              <h3 class="q-title"><a href="{{ route('forum.show', 3) }}">Best practice for integrating Wave &amp; Orange Money APIs with Laravel?</a></h3>
              <p class="q-excerpt">Looking for a clean abstraction layer to handle multiple mobile money providers.</p>
              <div class="q-tags"><span class="tag">payments</span><span class="tag">api</span><span class="tag">mobile-money</span></div>
              <div class="q-foot"><div class="author-row"><span class="avatar avatar-sm av-3">FD</span><div class="meta"><div class="name">Fatou Diallo</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 6 hours ago</span></div>
            </div>
          </div>

          <div class="q-card">
            <div class="q-stats"><div class="q-vote"><span>17</span><small>votes</small></div><div class="q-answers"><strong>2</strong>answers</div></div>
            <div class="q-body">
              <h3 class="q-title"><a href="{{ route('forum.show', 4) }}">Queue workers keep dying on a low-memory VPS — how to keep them alive?</a></h3>
              <p class="q-excerpt">My Horizon workers crash overnight on a 1GB DigitalOcean droplet.</p>
              <div class="q-tags"><span class="tag">queues</span><span class="tag">horizon</span><span class="tag">devops</span></div>
              <div class="q-foot"><div class="author-row"><span class="avatar avatar-sm av-4">YT</span><div class="meta"><div class="name">Yao Térence</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> Yesterday</span></div>
            </div>
          </div>

          <div class="q-card">
            <div class="q-stats"><div class="q-vote"><span>11</span><small>votes</small></div><div class="q-answers"><strong>0</strong>answers</div></div>
            <div class="q-body">
              <h3 class="q-title"><a href="{{ route('forum.show', 5) }}">Livewire 3 component re-renders too often on a large table — how to debounce?</a></h3>
              <p class="q-excerpt">I have a searchable table of 2,000 transactions. Each keystroke triggers a full re-render.</p>
              <div class="q-tags"><span class="tag">livewire</span><span class="tag">performance</span></div>
              <div class="q-foot"><div class="author-row"><span class="avatar avatar-sm av-6">NG</span><div class="meta"><div class="name">Nadège Gbané</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 2 days ago</span></div>
            </div>
          </div>

          <div class="q-card">
            <div class="q-stats"><div class="q-vote"><span>34</span><small>votes</small></div><div class="q-answers accepted"><strong>6</strong>answers</div></div>
            <div class="q-body">
              <h3 class="q-title"><a href="{{ route('forum.show', 6) }}">Deploying Laravel with zero downtime using GitHub Actions + a cheap VPS</a></h3>
              <p class="q-excerpt">Sharing my full deploy pipeline. Looking for feedback on the release symlink strategy.</p>
              <div class="q-tags"><span class="tag">deployment</span><span class="tag">ci-cd</span><span class="tag">github-actions</span></div>
              <div class="q-foot"><div class="author-row"><span class="avatar avatar-sm av-1">SB</span><div class="meta"><div class="name">Serge Brou</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 3 days ago</span></div>
            </div>
          </div>

          <!-- Pagination -->
          <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
              <li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">…</a></li>
              <li class="page-item"><a class="page-link" href="#">24</a></li>
              <li class="page-item"><a class="page-link" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </section>

@endsection
