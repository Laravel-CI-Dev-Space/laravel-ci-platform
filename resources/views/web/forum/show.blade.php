@extends('layouts.web')

@section('title', ($question->title ?? 'Question') . ' — Forum Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('forum.index') }}">Forum</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $question->tags->first()->name ?? 'Question' }}</span>
          </div>
          <h1 class="mb-3" style="font-size:var(--fs-h1)">{{ $question->title }}</h1>
          <div class="d-flex flex-wrap gap-3 align-items-center" style="font-size:.88rem;color:var(--muted)">
            <span>
              <i class="fa-regular fa-clock me-1"></i>
              Posée {{ $question->created_at?->diffForHumans() }}
            </span>
            <span>
              <i class="fa-regular fa-eye me-1"></i>
              {{ number_format($question->views_count) }} vues
            </span>
            @if ($question->hasAcceptedAnswer())
              <span><i class="fa-solid fa-check text-green me-1"></i> Résolue</span>
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

  <section class="section-sm">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
          @endif

          @livewire('forum.question-detail', ['slug' => $slug])
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4">
          <div class="sidebar-card">
            <div class="sidebar-title">Statistiques</div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted-2">Posée</span>
              <strong>{{ $question->created_at?->diffForHumans() }}</strong>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted-2">Vues</span>
              <strong>{{ number_format($question->views_count) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted-2">Réponses</span>
              <strong>{{ $question->answers_count }}</strong>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted-2">Votes</span>
              <strong>{{ $question->votes_score }}</strong>
            </div>
          </div>

          <div class="sidebar-card">
            <div class="sidebar-title">Tags</div>
            <div class="q-tags">
              @foreach ($question->tags as $tag)
                <a href="{{ route('forum.index', ['tagId' => $tag->id]) }}" class="tag">
                  {{ $tag->name }}
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
