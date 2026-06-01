@extends('layouts.web')

@section('title', 'Forum — Laravel CI')

@section('content')

  {{-- HERO --}}
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Forum</span>
          </div>
          <h1 class="mb-2">Forum Laravel CI</h1>
          <p class="lead mb-3">Posez vos questions, partagez vos solutions et apprenez avec 500+ développeurs Laravel ivoiriens.</p>
          @auth
            <a href="{{ route('forum.ask') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-circle-plus"></i> Poser une question
            </a>
          @else
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-circle-plus"></i> Poser une question
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

  @livewire('forum.question-list')

@endsection
