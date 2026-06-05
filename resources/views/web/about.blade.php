@extends('layouts.web')

@section('title', 'About — Laravel CI')
@section('description', 'Learn about Laravel Côte d\'Ivoire — our mission, founding team, and vision for the Ivorian PHP & Laravel developer community.')

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="badge-pill badge-navy"><i class="fa-brands fa-laravel text-orange"></i> Founded 2026 · Open source</span>
          <h1 class="my-3">We're building the home for Ivorian Laravel developers</h1>
          <p class="lead" style="max-width:42rem">Laravel Côte d'Ivoire is the first structured developer community dedicated to Laravel &amp; PHP in Côte d'Ivoire and the diaspora — built on knowledge sharing, inclusion, and collective growth.</p>
          <a href="{{ route('join') }}" class="btn btn-brand btn-lg mt-2"><i class="fa-solid fa-user-plus"></i> Join us</a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(200px,22vw,280px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MISSION & VISION -->
  <section class="section">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-6 reveal">
          <div class="card-soft" style="padding:2rem;height:100%">
            <div class="value-icon" style="margin:0 0 1.2rem"><i class="fa-solid fa-bullseye"></i></div>
            <h2 style="font-size:var(--fs-h3)">Our mission</h2>
            <p class="text-muted-2 mb-0">Give every Ivorian developer a structured place to learn Laravel properly, ask questions without fear, find meaningful work, and grow alongside peers who understand their context.</p>
          </div>
        </div>
        <div class="col-md-6 reveal" data-delay="0.08">
          <div class="card-soft" style="padding:2rem;height:100%">
            <div class="value-icon" style="margin:0 0 1.2rem"><i class="fa-solid fa-eye"></i></div>
            <h2 style="font-size:var(--fs-h3)">Our vision</h2>
            <p class="text-muted-2 mb-0">A West Africa where world-class software is built by local talent — and where Côte d'Ivoire is recognized as a hub of Laravel and PHP excellence on the global stage.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- STORY TIMELINE -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Our story</span>
        <h2>From a WhatsApp group to a movement</h2>
      </div>
      <div class="timeline">
        <div class="tl-item reveal"><span class="tl-dot"></span><div class="tl-card"><div class="tl-year">Jan 2026</div><h3 style="font-size:1.1rem">The first message</h3><p class="mb-0 text-muted-2" style="font-size:.92rem">A handful of Abidjan developers start a WhatsApp group to swap Laravel tips. Within a week, it's 40 people.</p></div></div>
        <div class="tl-item reveal" data-delay="0.05"><span class="tl-dot"></span><div class="tl-card"><div class="tl-year">Feb 2026</div><h3 style="font-size:1.1rem">Launch Hack</h3><p class="mb-0 text-muted-2" style="font-size:.92rem">Our first hackathon brings 72 developers together for a weekend of building. The community has a heartbeat.</p></div></div>
        <div class="tl-item reveal"><span class="tl-dot"></span><div class="tl-card"><div class="tl-year">Mar 2026</div><h3 style="font-size:1.1rem">Going open source</h3><p class="mb-0 text-muted-2" style="font-size:.92rem">We build this platform in public under the MIT license — built with Laravel, for Laravel developers.</p></div></div>
        <div class="tl-item reveal" data-delay="0.05"><span class="tl-dot"></span><div class="tl-card"><div class="tl-year">May 2026</div><h3 style="font-size:1.1rem">500 strong</h3><p class="mb-0 text-muted-2" style="font-size:.92rem">Across WhatsApp, LinkedIn and GitHub, we cross 500 members — and we're just getting started.</p></div></div>
      </div>
    </div>
  </section>

  <!-- VALUES -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">What we stand for</span>
        <h2>Community values</h2>
      </div>
      <div class="row g-4">
        <div class="col-6 col-lg-3 reveal"><div class="card-soft value-card"><div class="value-icon"><i class="fa-solid fa-award"></i></div><h3 style="font-size:1.1rem">Excellence</h3><p class="text-muted-2 mb-0" style="font-size:.9rem">We hold each other to a high bar — clean code, real craft.</p></div></div>
        <div class="col-6 col-lg-3 reveal" data-delay="0.06"><div class="card-soft value-card"><div class="value-icon"><i class="fa-solid fa-hands-holding-circle"></i></div><h3 style="font-size:1.1rem">Sharing</h3><p class="text-muted-2 mb-0" style="font-size:.9rem">Knowledge grows when given away. No gatekeeping here.</p></div></div>
        <div class="col-6 col-lg-3 reveal" data-delay="0.12"><div class="card-soft value-card"><div class="value-icon"><i class="fa-solid fa-people-group"></i></div><h3 style="font-size:1.1rem">Inclusion</h3><p class="text-muted-2 mb-0" style="font-size:.9rem">Every level, every background, every question is welcome.</p></div></div>
        <div class="col-6 col-lg-3 reveal" data-delay="0.18"><div class="card-soft value-card"><div class="value-icon"><i class="fa-solid fa-seedling"></i></div><h3 style="font-size:1.1rem">Impact</h3><p class="text-muted-2 mb-0" style="font-size:.9rem">We build tech that improves life in Côte d'Ivoire.</p></div></div>
      </div>
    </div>
  </section>

  <!-- TEAM -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">The people</span>
        <h2>Founding team</h2>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="col-6 col-md-4 col-lg-3 reveal"><div class="card-soft team-card"><span class="avatar avatar-xl av-1 mx-auto mb-3">SB</span><h3 style="font-size:1.05rem;margin-bottom:.1rem">Serge Brou</h3><div class="role mb-2">Founder &amp; Architect</div><a href="#" class="social-icon mx-auto" style="background:var(--light);color:var(--navy)" aria-label="GitHub"><i class="fa-brands fa-github"></i></a></div></div>
        <div class="col-6 col-md-4 col-lg-3 reveal" data-delay="0.06"><div class="card-soft team-card"><span class="avatar avatar-xl av-3 mx-auto mb-3">FD</span><h3 style="font-size:1.05rem;margin-bottom:.1rem">Fatou Diallo</h3><div class="role mb-2">Community Lead</div><a href="#" class="social-icon mx-auto" style="background:var(--light);color:var(--navy)" aria-label="GitHub"><i class="fa-brands fa-github"></i></a></div></div>
        <div class="col-6 col-md-4 col-lg-3 reveal" data-delay="0.12"><div class="card-soft team-card"><span class="avatar avatar-xl av-5 mx-auto mb-3">AD</span><h3 style="font-size:1.05rem;margin-bottom:.1rem">Aïcha Doumbia</h3><div class="role mb-2">Content &amp; Events</div><a href="#" class="social-icon mx-auto" style="background:var(--light);color:var(--navy)" aria-label="GitHub"><i class="fa-brands fa-github"></i></a></div></div>
        <div class="col-6 col-md-4 col-lg-3 reveal" data-delay="0.18"><div class="card-soft team-card"><span class="avatar avatar-xl av-4 mx-auto mb-3">YT</span><h3 style="font-size:1.05rem;margin-bottom:.1rem">Yao Térence</h3><div class="role mb-2">Open Source Maintainer</div><a href="#" class="social-icon mx-auto" style="background:var(--light);color:var(--navy)" aria-label="GitHub"><i class="fa-brands fa-github"></i></a></div></div>
      </div>
    </div>
  </section>

  <!-- PARTNERS -->
  <section class="section-sm">
    <div class="container">
      <p class="text-center text-muted-2 mb-4" style="font-weight:500;letter-spacing:.05em">Our partner communities</p>
      <div class="row g-3 justify-content-center">
        <div class="col-6 col-md-3 reveal"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel France</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.08"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Cameroun</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.16"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Sénégal</div></div>
        <div class="col-6 col-md-3 reveal" data-delay="0.24"><div class="partner-logo"><i class="fa-solid fa-hippo"></i> Laravel Nigeria</div></div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section pt-0">
    <div class="container">
      <div class="cta-banner reveal">
        <h2 class="mb-3">Your seat at the table is ready</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.9);max-width:38rem;margin-inline:auto">Join 500+ Ivorian developers building the future of African tech, one commit at a time.</p>
        <a href="{{ route('join') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Join the Community</a>
      </div>
    </div>
  </section>

@endsection
