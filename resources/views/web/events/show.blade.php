@extends('layouts.web')

@section('title', $event->title . ' — Laravel CI')

@push('head')
    <meta name="description" content="{{ str(strip_tags($event->description))->limit(160) }}">
    <meta property="og:title" content="{{ $event->title }} — Laravel CI">
    <meta property="og:description" content="{{ str(strip_tags($event->description))->limit(160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('events.show', $event) }}">
    <link rel="canonical" href="{{ route('events.show', $event) }}">
    @if($event->coverUrl())
        <meta property="og:image" content="{{ $event->coverUrl() }}">
    @endif
@endpush

@section('content')

  @php
    $typeSlug = $event->type?->slug ?? 'meetup';
    $typeIcon = match($typeSlug) {
      'webinar'   => 'fa-solid fa-video',
      'hackathon' => 'fa-solid fa-laptop-code',
      default     => 'fa-solid fa-people-roof',
    };
    $typeBadgeClass = match($typeSlug) {
      'webinar'   => '',
      'hackathon' => '',
      default     => 'badge-orange',
    };
    $typeBadgeStyle = match($typeSlug) {
      'webinar'   => 'background:#e7ebff;color:#4361ee',
      'hackathon' => 'background:#f1e7ff;color:#7209b7',
      default     => '',
    };
    $taken = (int) ($event->confirmed_registrations_count ?? $event->confirmedRegistrationsCount());
    $total = (int) ($event->capacity ?? 0);
    $pct = $total > 0 ? min(100, ($taken / $total) * 100) : 0;
  @endphp

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('events.index') }}">Événements</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $event->title }}</span>
          </div>
          <span class="badge-pill {{ $typeBadgeClass }}" @if($typeBadgeStyle) style="{{ $typeBadgeStyle }}" @endif>
            <i class="{{ $typeIcon }}"></i> {{ $event->type?->name }}
          </span>
          <h1 class="my-3" style="font-size:var(--fs-h1)">{{ $event->title }}</h1>
          <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
            <span><i class="fa-regular fa-calendar me-1 text-orange"></i>
              {{ $event->start_date->translatedFormat('l j F Y') }}</span>
            <span><i class="fa-regular fa-clock me-1 text-orange"></i>
              {{ $event->start_date->format('H:i') }} — {{ $event->end_date->format('H:i') }}</span>
            @if($event->location)
              <span><i class="fa-solid fa-location-dot me-1 text-orange"></i> {{ $event->location }}</span>
            @endif
          </div>
        </div>
        <div class="col-lg-3 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(130px,12vw,170px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          @if($event->coverUrl())
            <div class="event-cover event-cover--detail mb-4">
              <img src="{{ $event->coverUrl() }}" alt="{{ $event->title }}" width="1200" height="525" />
            </div>
          @endif

          <div class="prose mb-4">
            {!! nl2br(e($event->description)) !!}
          </div>

          @if($event->meeting_link)
            <div class="info-card mb-4">
              <a href="{{ $event->meeting_link }}" target="_blank" rel="noopener" class="btn btn-brand">
                <i class="fa-solid fa-video"></i> Rejoindre en ligne
              </a>
            </div>
          @endif

          @if($event->speakers->isNotEmpty())
            <div class="prose"><h2>Intervenants</h2></div>
            <div class="row g-3 mb-4">
              @foreach($event->speakers as $speaker)
                <div class="col-md-6">
                  <div class="speaker-card">
                    @if($speaker->avatar)
                      <img src="{{ $speaker->avatar }}" alt="{{ $speaker->name }}" class="avatar avatar-lg" style="object-fit:cover">
                    @else
                      <span class="avatar avatar-lg av-1">{{ strtoupper(substr($speaker->name, 0, 2)) }}</span>
                    @endif
                    <div>
                      <div class="name" style="font-weight:600">{{ $speaker->name }}</div>
                      @if($speaker->bio)
                        <div class="sub text-muted-2" style="font-size:.85rem">{{ $speaker->bio }}</div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <div class="col-lg-4">
          <div class="apply-card">
            <div class="info-card">
              <h2 class="sidebar-title mb-3">Participation</h2>

              <div class="d-flex gap-3 mb-3">
                <div class="event-date-chip">
                  <div class="m">{{ $event->start_date->translatedFormat('M') }}</div>
                  <div class="d">{{ $event->start_date->format('d') }}</div>
                </div>
                <div>
                  @if($total > 0)
                    <div class="spots-label"><span>{{ $taken }} / {{ $total }} inscrits</span><span>{{ max(0, $total - $taken) }} restantes</span></div>
                    <div class="progress-spots mb-0"><div class="bar" style="width:{{ $pct }}%"></div></div>
                  @else
                    <div class="val" style="font-size:1rem">Places illimitées</div>
                  @endif
                </div>
              </div>

              @if($registration)
                <div class="alert alert-success mb-0">
                  <i class="fa-solid fa-circle-check me-2"></i> Vous êtes inscrit à cet événement.
                </div>
              @elseif($waitlist)
                <div class="alert alert-warning mb-0">
                  <i class="fa-solid fa-hourglass-half me-2"></i>
                  Liste d'attente — position <strong>#{{ $waitlist->position }}</strong>.
                </div>
              @elseif($canRegister)
                <form method="POST" action="{{ route('events.register', $event) }}">
                  @csrf
                  <button type="submit" class="btn btn-brand w-100">
                    <i class="fa-solid fa-ticket"></i>
                    {{ $event->isFull() ? 'Rejoindre la liste d\'attente' : 'Confirmer mon inscription' }}
                  </button>
                </form>
                <p class="text-muted-2 text-center mt-2 mb-0" style="font-size:.8rem">Réservé aux membres actifs</p>
              @elseif(auth()->check() && ! auth()->user()->hasRole('member'))
                <p class="text-muted-2 mb-0">Seuls les membres actifs peuvent s'inscrire.</p>
              @elseif(! $event->isRegisterable())
                <p class="text-muted-2 mb-0">Les inscriptions sont closes pour cet événement.</p>
              @else
                <a href="{{ route('login') }}" class="btn btn-brand w-100">
                  <i class="fa-brands fa-github"></i> Se connecter pour s'inscrire
                </a>
              @endif

              @if($errors->any())
                <div class="alert alert-danger mt-3 mb-0">
                  @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                  @endforeach
                </div>
              @endif
            </div>

            <div class="info-card">
              <div class="sidebar-title">Détails</div>
              <div class="info-row">
                <div class="ic"><i class="{{ $typeIcon }}"></i></div>
                <div><div class="lbl">Type</div><div class="val">{{ $event->type?->name }}</div></div>
              </div>
              <div class="info-row">
                <div class="ic"><i class="fa-regular fa-calendar"></i></div>
                <div><div class="lbl">Date</div><div class="val">{{ $event->start_date->translatedFormat('d/m/Y') }}</div></div>
              </div>
              @if($event->location)
                <div class="info-row">
                  <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
                  <div><div class="lbl">Lieu</div><div class="val">{{ $event->location }}</div></div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
