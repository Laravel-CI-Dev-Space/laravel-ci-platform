@extends('layouts.dashboard')

@section('title', 'Mon tableau de bord — Laravel CI')

@section('content')

@php /** @var \App\Models\User $me */ @endphp

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Vue d\'ensemble']]" />

  {{-- ── Bienvenue ──────────────────────────────────────── --}}
  <div class="row align-items-center mb-4 g-3">
    <div class="col">
      <h1 class="fs-4 fw-bold mb-1">Bon retour, {{ $me->name }} 👋</h1>
      <p class="text-secondary mb-0">Voici un résumé de votre activité sur Laravel CI.</p>
    </div>
    <div class="col-auto d-none d-md-block">
      <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI"
           style="height:72px;width:auto;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(0,0,0,.12))">
    </div>
  </div>

  {{-- ── Stat cards ────────────────────────────────────── --}}
  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Questions posées"
        :value="$questionCount"
        change="sur le forum"
        icon="ti ti-message-circle-question"
        color="primary"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Réponses données"
        :value="$answerCount"
        change="à la communauté"
        icon="ti ti-message-check"
        color="success"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Articles publiés"
        :value="$articleCount"
        change="sur le blog"
        icon="ti ti-file-text"
        color="info"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Votes reçus"
        :value="$votesScore"
        change="sur vos questions"
        icon="ti ti-star"
        color="warning"
      />
    </div>
  </div>

  {{-- ── Activité récente ───────────────────────────────── --}}
  <div class="row g-3">

    {{-- Questions --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes dernières questions</h5>
          <a href="{{ route('dashboard.member.questions') }}" class="small text-primary">Voir tout</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentQuestions as $question)
            <li class="list-group-item px-4 py-3">
              <a href="{{ route('forum.show', $question->slug) }}"
                 class="fw-semibold text-dark text-decoration-none d-block mb-1"
                 style="font-size:.9rem">{{ Str::limit($question->title, 65) }}</a>
              <div class="d-flex align-items-center gap-2 text-secondary" style="font-size:.78rem">
                <span class="{{ $question->hasAcceptedAnswer() ? 'text-success' : 'text-muted' }}">
                  <i class="ti ti-{{ $question->hasAcceptedAnswer() ? 'circle-check-filled' : 'circle' }}"></i>
                  {{ $question->hasAcceptedAnswer() ? 'Résolue' : 'Ouverte' }}
                </span>
                <span>·</span>
                <span><i class="ti ti-messages"></i> {{ $question->answers_count }}</span>
                <span>·</span>
                <span>{{ $question->created_at->diffForHumans() }}</span>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte" style="height:56px;opacity:.5" class="d-block mx-auto mb-3">
              <p class="mb-1 fw-semibold">Aucune question pour l'instant.</p>
              <a href="{{ route('forum.index') }}" class="text-primary text-decoration-none small">Poser ma première question</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Articles --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes articles</h5>
          <a href="{{ route('dashboard.member.articles') }}" class="small text-primary">Voir tout</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentArticles as $article)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <a href="{{ route('blog.show', $article->slug) }}"
                   class="fw-semibold text-dark text-decoration-none d-block mb-1"
                   style="font-size:.9rem">{{ Str::limit($article->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ $article->level->label() }} · {{ $article->created_at->diffForHumans() }}
                </div>
              </div>
              @php
                $badge = match($article->status) {
                  \App\Enums\ArticleStatus::Published => ['bg-success-subtle text-success', 'Publié'],
                  \App\Enums\ArticleStatus::Pending   => ['bg-warning-subtle text-warning', 'En attente'],
                  \App\Enums\ArticleStatus::Rejected  => ['bg-danger-subtle text-danger', 'Rejeté'],
                  default                             => ['bg-secondary-subtle text-secondary', 'Brouillon'],
                };
              @endphp
              <span class="badge {{ $badge[0] }} small">{{ $badge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte" style="height:56px;opacity:.5" class="d-block mx-auto mb-3">
              <p class="mb-1 fw-semibold">Aucun article pour l'instant.</p>
              <a href="{{ route('dashboard.member.articles') }}" class="text-primary text-decoration-none small">Écrire mon premier article</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Événements à venir --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes prochains événements</h5>
          <a href="{{ route('dashboard.member.events') }}" class="small text-primary">Voir tout</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($upcomingRegs as $reg)
            @php $event = $reg->event; @endphp
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="text-center bg-primary-subtle rounded p-2" style="min-width:48px">
                <div class="fw-bold text-primary" style="font-size:.7rem;text-transform:uppercase">
                  {{ $event->starts_at->translatedFormat('M') }}
                </div>
                <div class="fw-bold text-primary" style="font-size:1.1rem;line-height:1">
                  {{ $event->starts_at->format('d') }}
                </div>
              </div>
              <div class="flex-grow-1">
                <a href="{{ route('events.show', $event->slug) }}"
                   class="fw-semibold text-dark text-decoration-none d-block"
                   style="font-size:.9rem">{{ Str::limit($event->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ $event->location ?? 'En ligne' }} · {{ $event->starts_at->format('H:i') }}
                </div>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte" style="height:56px;opacity:.5" class="d-block mx-auto mb-3">
              <p class="mb-1 fw-semibold">Aucun événement à venir.</p>
              <a href="{{ route('events.index') }}" class="text-primary text-decoration-none small">Découvrir les événements</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Candidatures --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes candidatures</h5>
          <a href="{{ route('dashboard.member.applications') }}" class="small text-primary">Voir tout</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentApps as $application)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <a href="{{ route('jobs.show', $application->jobOffer->slug) }}"
                   class="fw-semibold text-dark text-decoration-none d-block mb-1"
                   style="font-size:.9rem">{{ Str::limit($application->jobOffer->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ $application->jobOffer->company->name ?? '—' }}
                  · {{ $application->created_at->diffForHumans() }}
                </div>
              </div>
              @php
                $appBadge = match($application->status) {
                  \App\Enums\JobApplicationStatus::Pending     => ['bg-warning-subtle text-warning', 'En attente'],
                  \App\Enums\JobApplicationStatus::Viewed      => ['bg-info-subtle text-info', 'Vue'],
                  \App\Enums\JobApplicationStatus::Shortlisted => ['bg-primary-subtle text-primary', 'Présélectionné'],
                  \App\Enums\JobApplicationStatus::Accepted    => ['bg-success-subtle text-success', 'Acceptée'],
                  \App\Enums\JobApplicationStatus::Rejected    => ['bg-danger-subtle text-danger', 'Refusée'],
                };
              @endphp
              <span class="badge {{ $appBadge[0] }}">{{ $appBadge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte" style="height:56px;opacity:.5" class="d-block mx-auto mb-3">
              <p class="mb-1 fw-semibold">Aucune candidature pour l'instant.</p>
              <a href="{{ route('jobs.index') }}" class="text-primary text-decoration-none small">Parcourir les offres d'emploi</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

  </div>{{-- /row activity --}}

@endsection
