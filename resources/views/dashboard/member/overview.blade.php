@extends('layouts.dashboard')

@section('title', 'Mon tableau de bord')

@section('content')

@php
  /** @var \App\Models\User $me */
  $me = auth()->user();
@endphp

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Vue d\'ensemble']]" />

  <div class="row mb-4">
    <div class="col-12">
      <h1 class="fs-4 fw-bold mb-1">Bon retour, {{ $me->name }} 👋</h1>
      <p class="text-secondary mb-0">Voici votre activité sur Laravel CI.</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i class="ti ti-circle-check me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Questions"
        :value="$questionCount"
        change="posées"
        icon="ti ti-message-circle-question"
        color="primary"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Réponses"
        :value="$answerCount"
        change="données"
        icon="ti ti-message-check"
        color="success"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Articles"
        :value="$articleCount"
        change="publiés"
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

  <div class="row g-3">

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes questions récentes</h5>
          <a href="{{ route('dashboard.member.questions') }}" class="small text-primary">Tout voir</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentQuestions as $question)
            <li class="list-group-item px-4 py-3">
              <a href="{{ route('forum.show', $question->slug) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1"
                 style="font-size:.9rem">{{ Str::limit($question->title, 65) }}</a>
              <div class="d-flex align-items-center gap-2 text-secondary" style="font-size:.78rem">
                <span class="{{ $question->hasAcceptedAnswer() ? 'text-success' : 'text-muted' }}">
                  <i class="ti ti-{{ $question->hasAcceptedAnswer() ? 'circle-check-filled' : 'circle' }}"></i>
                  {{ $question->hasAcceptedAnswer() ? 'Résolue' : 'Ouverte' }}
                </span>
                <span>·</span>
                <span><i class="ti ti-messages"></i> {{ $question->answers()->count() }}</span>
                <span>·</span>
                <span>{{ $question->created_at->diffForHumans() }}</span>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-message-circle-question d-block mb-2" style="font-size:1.8rem"></i>
              Aucune question pour l'instant.
              <a href="{{ route('forum.index') }}" class="text-primary text-decoration-none">Posez la première !</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes articles</h5>
          <a href="{{ route('dashboard.member.articles') }}" class="small text-primary">Tout voir</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentArticles as $article)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <a href="{{ route('blog.show', $article->slug) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1"
                   style="font-size:.9rem">{{ Str::limit($article->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ ucfirst($article->level ?? 'débutant') }}
                  · {{ $article->created_at->diffForHumans() }}
                </div>
              </div>
              @php
                $badge = match($article->status) {
                  'published' => ['bg-success-subtle text-success', 'Publié'],
                  'pending'   => ['bg-warning-subtle text-warning', 'En attente'],
                  'rejected'  => ['bg-danger-subtle text-danger', 'Refusé'],
                  default     => ['bg-secondary-subtle text-secondary', 'Brouillon'],
                };
              @endphp
              <span class="badge {{ $badge[0] }} small">{{ $badge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-file-text d-block mb-2" style="font-size:1.8rem"></i>
              Aucun article pour l'instant.
              <a href="{{ route('dashboard.member.articles') }}" class="text-primary text-decoration-none">Rédigez le premier !</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes prochains événements</h5>
          <a href="{{ route('dashboard.member.events') }}" class="small text-primary">Tout voir</a>
        </div>
        <ul class="list-group list-group-flush p-0">
          @forelse($upcomingRegs as $reg)
            <li class="list-group-item p-0 border-0">
              <livewire:dashboard.registered-event-card
                :registration-id="$reg->id"
                variant="row"
                :key="'member-event-row-'.$reg->id"
              />
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-calendar-event d-block mb-2" style="font-size:1.8rem"></i>
              Aucun événement à venir.
              <a href="{{ route('events.index') }}" class="text-primary text-decoration-none">Parcourir les événements</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">Mes candidatures</h5>
          <a href="{{ route('dashboard.member.applications') }}" class="small text-primary">Tout voir</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentApps as $application)
            @php $job = $application->jobOffer; @endphp
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                @if($job)
                  <a href="{{ route('jobs.show', $job) }}"
                     class="fw-semibold text-dark text-decoration-none d-block mb-1"
                     style="font-size:.9rem">{{ Str::limit($job->title, 55) }}</a>
                  <div class="text-secondary" style="font-size:.78rem">
                    {{ $job->company?->name ?? '—' }}
                    · {{ $application->created_at->diffForHumans() }}
                  </div>
                @else
                  <span class="fw-semibold d-block mb-1" style="font-size:.9rem">Offre supprimée</span>
                  <div class="text-secondary" style="font-size:.78rem">
                    {{ $application->created_at->diffForHumans() }}
                  </div>
                @endif
              </div>
              @php
                $appBadge = match ($application->status) {
                  \App\Enums\Jobs\JobApplicationStatus::PENDING  => ['bg-warning-subtle text-warning', $application->status->label()],
                  \App\Enums\Jobs\JobApplicationStatus::ACCEPTED => ['bg-success-subtle text-success', $application->status->label()],
                  \App\Enums\Jobs\JobApplicationStatus::REJECTED => ['bg-danger-subtle text-danger', $application->status->label()],
                };
              @endphp
              <span class="badge {{ $appBadge[0] }}">{{ $appBadge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-briefcase d-block mb-2" style="font-size:1.8rem"></i>
              Aucune candidature pour l'instant.
              <a href="{{ route('jobs.index') }}" class="text-primary text-decoration-none">Parcourir les offres</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

  </div>

@endsection
