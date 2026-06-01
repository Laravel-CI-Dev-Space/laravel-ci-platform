@extends('layouts.web')

@section('title', 'Poser une question — Forum Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('forum.index') }}">Forum</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Poser une question</span>
          </div>
          <h1 class="mb-2">Poser une question</h1>
          <p class="lead mb-0">
            Décrivez votre problème clairement pour obtenir des réponses pertinentes de la communauté.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="section-sm">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          @livewire('forum.ask-question')
        </div>

        <div class="col-lg-4 d-none d-lg-block">
          <div class="sidebar-card">
            <div class="sidebar-title">Conseils pour une bonne question</div>
            <ul class="list-unstyled" style="font-size:.88rem;line-height:1.7">
              <li><i class="fa-solid fa-check text-green me-2"></i>Soyez précis dans le titre</li>
              <li><i class="fa-solid fa-check text-green me-2"></i>Incluez les messages d'erreur exacts</li>
              <li><i class="fa-solid fa-check text-green me-2"></i>Montrez ce que vous avez déjà essayé</li>
              <li><i class="fa-solid fa-check text-green me-2"></i>Partagez le code minimal reproductible</li>
              <li><i class="fa-solid fa-check text-green me-2"></i>Choisissez des tags pertinents</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
