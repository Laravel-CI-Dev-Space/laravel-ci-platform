@extends('layouts.web')

@section('title', 'Événements — Laravel CI')

@push('head')
    <meta name="description" content="Meetups, webinars et hackathons de la communauté Laravel Côte d'Ivoire.">
    <link rel="canonical" href="{{ route('events.index') }}">
@endpush

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Événements</span>
          </div>
          <h1 class="mb-2">Événements</h1>
          <p class="lead mb-4">Meetups, webinars et hackathons — à Abidjan et en ligne. Inscrivez-vous en un clic.</p>
          <div class="d-flex flex-wrap gap-2">
            @foreach(['upcoming' => 'À venir', 'past' => 'Passés', 'all' => 'Tous'] as $value => $label)
              <a href="{{ route('events.index', array_filter(['period' => $value, 'type' => $type])) }}"
                 class="filter-pill {{ $period === $value ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
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

  <section class="section">
    <div class="container">
      @if($types->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="text-muted-2 small fw-semibold align-self-center me-1">Type</span>
          <a href="{{ route('events.index', ['period' => $period]) }}"
             class="filter-pill {{ ! $type ? 'active' : '' }}">Tous</a>
          @foreach($types as $eventType)
            <a href="{{ route('events.index', ['period' => $period, 'type' => $eventType->slug]) }}"
               class="filter-pill {{ $type === $eventType->slug ? 'active' : '' }}">{{ $eventType->name }}</a>
          @endforeach
        </div>
      @endif

      @if($events->isEmpty())
        <div class="text-center py-5">
          <i class="fa-regular fa-calendar-xmark fa-3x text-muted-2 mb-3"></i>
          <p class="text-muted-2 mb-3">Aucun événement pour ces filtres.</p>
          <a href="{{ route('events.index') }}" class="btn btn-brand">Voir les événements à venir</a>
        </div>
      @else
        <div class="row g-4">
          @foreach($events as $event)
            <x-web.event-card :event="$event" />
          @endforeach
        </div>
        {{ $events->links('vendor.pagination.web') }}
      @endif
    </div>
  </section>

@endsection
