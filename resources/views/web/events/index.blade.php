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

  <livewire:events.event-list />

@endsection
