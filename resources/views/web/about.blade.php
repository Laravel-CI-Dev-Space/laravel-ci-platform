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
          <h1 class="my-3">{{ $settings->firstWhere('key', 'about_hero_title')?->value ?? "We're building the home for Ivorian Laravel developers" }}</h1>
          <p class="lead" style="max-width:42rem">{{ $settings->firstWhere('key', 'about_hero_subtitle')?->value ?? "Laravel Côte d'Ivoire is the first structured developer community dedicated to Laravel & PHP in Côte d'Ivoire and the diaspora — built on knowledge sharing, inclusion, and collective growth." }}</p>
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
            <p class="text-muted-2 mb-0">{{ $settings->firstWhere('key', 'about_mission')?->value ?? 'Give every Ivorian developer a structured place to learn Laravel properly.' }}</p>
          </div>
        </div>
        <div class="col-md-6 reveal" data-delay="0.08">
          <div class="card-soft" style="padding:2rem;height:100%">
            <div class="value-icon" style="margin:0 0 1.2rem"><i class="fa-solid fa-eye"></i></div>
            <h2 style="font-size:var(--fs-h3)">Our vision</h2>
            <p class="text-muted-2 mb-0">{{ $settings->firstWhere('key', 'about_vision')?->value ?? 'A West Africa where world-class software is built by local talent.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NOTRE NAISSANCE -->
  @if($origin && $origin->is_active)
  <section class="section">
    <div class="container">
      <div class="row align-items-center g-5 {{ $origin->media_position === 'left' ? 'flex-row-reverse' : '' }}">

        {{-- Colonne texte --}}
        <div class="col-lg-{{ $origin->media_type === \App\Enums\MediaType::None ? '12' : '6' }} reveal">
          @if($origin->eyebrow)
            <span class="section-eyebrow">{{ $origin->eyebrow }}</span>
          @endif
          <h2>{{ $origin->title }}</h2>
          <div class="text-muted-2">{!! clean($origin->content) !!}</div>
        </div>

        {{-- Colonne média --}}
        @if($origin->media_type !== \App\Enums\MediaType::None)
        <div class="col-lg-6 reveal" data-delay="0.08">
          @if($origin->isImage())
            <img src="{{ $origin->mediaUrl() }}"
                 alt="{{ $origin->caption ?? $origin->title }}"
                 class="img-fluid rounded-3 shadow"
                 style="width:100%;object-fit:cover;max-height:420px">
          @elseif($origin->isVideo())
            <video controls class="w-100 rounded-3 shadow" style="max-height:420px">
              <source src="{{ $origin->mediaUrl() }}">
            </video>
          @elseif($origin->isYoutube())
            <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow">
              <iframe src="{{ $origin->youtube_url }}"
                      title="{{ $origin->title }}"
                      allowfullscreen></iframe>
            </div>
          @endif
          @if($origin->caption)
            <p class="text-muted-2 text-center mt-2" style="font-size:.88rem">{{ $origin->caption }}</p>
          @endif
        </div>
        @endif

      </div>
    </div>
  </section>
  @endif

  <!-- STORY TIMELINE -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Our story</span>
        <h2>From a WhatsApp group to a movement</h2>
      </div>
      <div class="timeline">
        @foreach($timeline as $event)
        <div class="tl-item reveal">
          <span class="tl-dot"></span>
          <div class="tl-card">
            <div class="tl-year">{{ $event->period }}</div>
            <h3 style="font-size:1.1rem">{{ $event->title }}</h3>
            <p class="mb-0 text-muted-2" style="font-size:.92rem">{{ $event->description }}</p>
          </div>
        </div>
        @endforeach
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
        @foreach($values as $value)
        <div class="col-6 col-lg-3 reveal">
          <div class="card-soft value-card">
            <div class="value-icon"><i class="{{ $value->icon }}"></i></div>
            <h3 style="font-size:1.1rem">{{ $value->title }}</h3>
            <p class="text-muted-2 mb-0" style="font-size:.9rem">{{ $value->description }}</p>
          </div>
        </div>
        @endforeach
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
        @foreach($team as $member)
        <div class="col-6 col-md-4 col-lg-3 reveal">
          <div class="card-soft team-card">
            @if($member->avatarUrl())
              <img src="{{ $member->avatarUrl() }}"
                   alt="{{ $member->fullName() }}"
                   class="avatar avatar-xl mx-auto mb-3"
                   style="object-fit:cover">
            @else
              <span class="avatar avatar-xl {{ $member->avatar_color }} mx-auto mb-3">
                {{ $member->initials() }}
              </span>
            @endif
            <h3 style="font-size:1.05rem;margin-bottom:.1rem">{{ $member->fullName() }}</h3>
            <div class="role mb-2">{{ $member->role }}</div>
            <div class="d-flex gap-2 justify-content-center">
              @if($member->github_url)
                <a href="{{ $member->github_url }}" class="social-icon" target="_blank"
                   style="background:var(--light);color:var(--navy)" aria-label="GitHub">
                  <i class="fa-brands fa-github"></i>
                </a>
              @endif
              @if($member->linkedin_url)
                <a href="{{ $member->linkedin_url }}" class="social-icon" target="_blank"
                   style="background:#e8f0fe;color:#0a66c2" aria-label="LinkedIn">
                  <i class="fa-brands fa-linkedin-in"></i>
                </a>
              @endif
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- PARTNERS -->
  <section class="section-sm">
    <div class="container">
      <p class="text-center text-muted-2 mb-4" style="font-weight:500;letter-spacing:.05em">Our partner communities</p>
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

  <!-- CTA -->
  <section class="section pt-0">
    <div class="container">
      <div class="cta-banner reveal">
        <h2 class="mb-3">{{ $settings->firstWhere('key', 'about_cta_title')?->value ?? 'Your seat at the table is ready' }}</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.9);max-width:38rem;margin-inline:auto">
          {{ $settings->firstWhere('key', 'about_cta_text')?->value ?? 'Join 500+ Ivorian developers building the future of African tech, one commit at a time.' }}
        </p>
        <a href="{{ route('join') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Join the Community</a>
      </div>
    </div>
  </section>

@endsection
