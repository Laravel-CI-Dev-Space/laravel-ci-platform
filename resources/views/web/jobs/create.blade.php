@extends('layouts.web')

@section('title', 'Publier une offre — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="breadcrumb-bar">
        <a href="{{ route('home') }}">Accueil</a>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('jobs.index') }}">Emplois</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Publier une offre</span>
      </div>
      <h1 class="mb-2">Publier une offre</h1>
      <p class="lead mb-0">Votre offre sera enregistrée en brouillon et validée par l'équipe avant publication.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <livewire:job-board.submit-job-offer />
        </div>
      </div>
    </div>
  </section>

@endsection
