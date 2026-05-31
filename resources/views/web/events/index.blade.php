@extends('layouts.web')

@section('title', 'Events — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar"><a href="{{ route('home') }}">Home</a> <i class="fa-solid fa-chevron-right"></i> <span>Events</span></div>
          <h1 class="mb-2">Events</h1>
          <p class="lead mb-4">Meetups, webinars and hackathons — in Abidjan and online. Come learn and build with the community.</p>
          <div class="filter-pills" data-filter-group data-filter-target=".event-col">
            <button class="filter-pill active" data-filter="all">All</button>
            <button class="filter-pill" data-filter="meetup">Meetup</button>
            <button class="filter-pill" data-filter="webinar">Webinar</button>
            <button class="filter-pill" data-filter="hackathon">Hackathon</button>
            <button class="filter-pill" data-filter="upcoming">Upcoming</button>
            <button class="filter-pill" data-filter="past">Past</button>
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

  {{-- LIVEWIRE: @livewire('events.event-list') --}}

  <section class="section">
    <div class="container">
      <!-- FEATURED -->
      <div class="featured-event reveal mb-5">
        <div class="fe-visual">
          <div class="glow"></div>
          <div class="text-center position-relative">
            <div class="mascot-circle" style="width:110px;height:110px;font-size:3rem;background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.25)"><i class="fa-solid fa-laptop-code"></i></div>
            <span class="badge-pill" style="background:rgba(255,255,255,.16);color:#fff">Featured · Hackathon</span>
          </div>
        </div>
        <div class="fe-body">
          <span class="section-eyebrow">Don't miss it</span>
          <h2 style="font-size:var(--fs-h3)"><a href="{{ route('events.show', 1) }}" class="text-navy">CivTech Hack 2026 — 48h building for local impact</a></h2>
          <p class="text-muted-2">Team up with fellow Ivorian developers for a weekend of building. Mentors from across the diaspora, prizes for the most impactful civic-tech project, and a demo day at the Orange Digital Center.</p>
          <div class="d-flex flex-wrap gap-4 my-3" style="font-size:.9rem">
            <span><i class="fa-regular fa-calendar text-orange me-2"></i> July 5–6, 2026</span>
            <span><i class="fa-solid fa-location-dot text-orange me-2"></i> Orange Digital Center, Abidjan</span>
          </div>
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="avatar-stack"><span class="avatar avatar-sm av-1">SB</span><span class="avatar avatar-sm av-3">FD</span><span class="avatar avatar-sm av-5">AD</span><span class="avatar avatar-sm av-4">YT</span></div>
            <span class="text-muted-2" style="font-size:.85rem">+8 mentors confirmed</span>
          </div>
          <div class="spots-label"><span>64 / 80 spots filled</span><span>16 left</span></div>
          <div class="progress-spots mb-4"><div class="bar" style="width:80%"></div></div>
          <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-ticket"></i> Register now</a>
            <a href="#" class="btn btn-outline-navy btn-lg"><i class="fa-regular fa-calendar-plus"></i> Add to calendar</a>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Upcoming meetup -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="meetup upcoming">
          <article class="card-soft event-card">
            <div class="event-banner ev-meetup"></div>
            <div class="card-pad">
              <div class="d-flex gap-3 mb-3">
                <div class="event-date-chip"><div class="m">Jun</div><div class="d">14</div></div>
                <div><span class="badge-pill badge-orange"><i class="fa-solid fa-people-roof"></i> Meetup</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 1) }}" class="text-navy">Eloquent Deep Dive</a></h3></div>
              </div>
              <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                <span><i class="fa-regular fa-clock text-orange me-2"></i> Sat 2:00 PM</span>
                <span><i class="fa-solid fa-location-dot text-orange me-2"></i> Jokkolabs, Cocody</span>
              </div>
              <div class="avatar-stack mb-3"><span class="avatar avatar-sm av-3">FD</span><span class="avatar avatar-sm av-1">SB</span></div>
              <div class="spots-label"><span>32 / 50</span><span>18 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:64%"></div></div>
              <div class="d-flex gap-2"><a href="{{ route('login') }}" class="btn btn-brand flex-grow-1"><i class="fa-solid fa-ticket"></i> Register</a><a href="{{ route('events.show', 1) }}" class="btn btn-ghost" aria-label="Details"><i class="fa-solid fa-arrow-right"></i></a></div>
            </div>
          </article>
        </div>

        <!-- Upcoming webinar -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="webinar upcoming" data-delay="0.05">
          <article class="card-soft event-card">
            <div class="event-banner ev-webinar"></div>
            <div class="card-pad">
              <div class="d-flex gap-3 mb-3">
                <div class="event-date-chip"><div class="m">Jun</div><div class="d">18</div></div>
                <div><span class="badge-pill" style="background:#e7ebff;color:#4361ee"><i class="fa-solid fa-video"></i> Webinar</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 2) }}" class="text-navy">Testing APIs with Pest</a></h3></div>
              </div>
              <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                <span><i class="fa-regular fa-clock text-orange me-2"></i> Wed 7:00 PM</span>
                <span><i class="fa-solid fa-globe text-orange me-2"></i> Online · Google Meet</span>
              </div>
              <div class="avatar-stack mb-3"><span class="avatar avatar-sm av-6">NG</span></div>
              <div class="spots-label"><span>87 / 200</span><span>113 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:43%"></div></div>
              <div class="d-flex gap-2"><a href="{{ route('login') }}" class="btn btn-brand flex-grow-1"><i class="fa-solid fa-ticket"></i> Register</a><a href="{{ route('events.show', 2) }}" class="btn btn-ghost" aria-label="Details"><i class="fa-solid fa-arrow-right"></i></a></div>
            </div>
          </article>
        </div>

        <!-- Upcoming hackathon -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="hackathon upcoming" data-delay="0.1">
          <article class="card-soft event-card">
            <div class="event-banner ev-hackathon"></div>
            <div class="card-pad">
              <div class="d-flex gap-3 mb-3">
                <div class="event-date-chip"><div class="m">Jul</div><div class="d">05</div></div>
                <div><span class="badge-pill" style="background:#f1e7ff;color:#7209b7"><i class="fa-solid fa-laptop-code"></i> Hackathon</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 3) }}" class="text-navy">CivTech Hack 2026</a></h3></div>
              </div>
              <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                <span><i class="fa-regular fa-clock text-orange me-2"></i> All weekend</span>
                <span><i class="fa-solid fa-location-dot text-orange me-2"></i> Orange Digital Center</span>
              </div>
              <div class="avatar-stack mb-3"><span class="avatar avatar-sm av-1">SB</span><span class="avatar avatar-sm av-3">FD</span><span class="avatar avatar-sm av-5">AD</span></div>
              <div class="spots-label"><span>64 / 80</span><span>16 left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:80%"></div></div>
              <div class="d-flex gap-2"><a href="{{ route('login') }}" class="btn btn-brand flex-grow-1"><i class="fa-solid fa-ticket"></i> Register</a><a href="{{ route('events.show', 3) }}" class="btn btn-ghost" aria-label="Details"><i class="fa-solid fa-arrow-right"></i></a></div>
            </div>
          </article>
        </div>

        <!-- Past meetup -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="meetup past">
          <article class="event-card past">
            <span class="past-badge badge-pill badge-soft"><i class="fa-solid fa-clock-rotate-left"></i> Past Event</span>
            <div class="card-soft">
              <div class="event-banner ev-meetup"></div>
              <div class="card-pad">
                <div class="d-flex gap-3 mb-3">
                  <div class="event-date-chip"><div class="m">Apr</div><div class="d">26</div></div>
                  <div><span class="badge-pill badge-orange"><i class="fa-solid fa-people-roof"></i> Meetup</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 4) }}" class="text-navy">Meetup #03 — Testing</a></h3></div>
                </div>
                <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                  <span><i class="fa-solid fa-location-dot me-2"></i> Jokkolabs, Cocody</span>
                  <span><i class="fa-solid fa-users me-2"></i> 48 attended</span>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost w-100"><i class="fa-solid fa-circle-play"></i> Watch recording</a>
              </div>
            </div>
          </article>
        </div>

        <!-- Past webinar -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="webinar past" data-delay="0.05">
          <article class="event-card past">
            <span class="past-badge badge-pill badge-soft"><i class="fa-solid fa-clock-rotate-left"></i> Past Event</span>
            <div class="card-soft">
              <div class="event-banner ev-webinar"></div>
              <div class="card-pad">
                <div class="d-flex gap-3 mb-3">
                  <div class="event-date-chip"><div class="m">Mar</div><div class="d">15</div></div>
                  <div><span class="badge-pill" style="background:#e7ebff;color:#4361ee"><i class="fa-solid fa-video"></i> Webinar</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 5) }}" class="text-navy">Intro to Livewire 3</a></h3></div>
                </div>
                <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                  <span><i class="fa-solid fa-globe me-2"></i> Online</span>
                  <span><i class="fa-solid fa-users me-2"></i> 156 attended</span>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost w-100"><i class="fa-solid fa-circle-play"></i> Watch recording</a>
              </div>
            </div>
          </article>
        </div>

        <!-- Past hackathon -->
        <div class="col-md-6 col-lg-4 event-col reveal" data-category="hackathon past" data-delay="0.1">
          <article class="event-card past">
            <span class="past-badge badge-pill badge-soft"><i class="fa-solid fa-clock-rotate-left"></i> Past Event</span>
            <div class="card-soft">
              <div class="event-banner ev-hackathon"></div>
              <div class="card-pad">
                <div class="d-flex gap-3 mb-3">
                  <div class="event-date-chip"><div class="m">Feb</div><div class="d">08</div></div>
                  <div><span class="badge-pill" style="background:#f1e7ff;color:#7209b7"><i class="fa-solid fa-laptop-code"></i> Hackathon</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 6) }}" class="text-navy">Launch Hack 2026</a></h3></div>
                </div>
                <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
                  <span><i class="fa-solid fa-location-dot me-2"></i> Abidjan</span>
                  <span><i class="fa-solid fa-users me-2"></i> 72 attended</span>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost w-100"><i class="fa-solid fa-images"></i> View recap</a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

@endsection
