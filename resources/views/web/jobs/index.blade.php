@extends('layouts.web')

@section('title', 'Offres d\'emploi — Laravel CI')

@push('head')
    <meta name="description" content="Offres Laravel et PHP de la communauté Laravel Côte d'Ivoire.">
    <link rel="canonical" href="{{ route('jobs.index') }}">
@endpush

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Emplois</span>
          </div>
          <h1 class="mb-2">Job board</h1>
          <p class="lead mb-3">Offres Laravel, PHP et tech publiées par la communauté Laravel CI.</p>
          @auth
            @if(auth()->user()->hasRole('member'))
              <a href="{{ route('jobs.create') }}" class="btn btn-brand btn-lg">
                <i class="fa-solid fa-circle-plus"></i> Publier une offre
              </a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-circle-plus"></i> Publier une offre
            </a>
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

  <section class="section-sm">
    <div class="container">
      <livewire:job-board.job-list />
    </div>
  </section>

@endsection
