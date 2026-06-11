@extends('layouts.dashboard')

@section('title', 'Détail candidature')

@section('content')

  @php
    $job = $application->jobOffer;
    $status = match ($application->status) {
      \App\Enums\Jobs\JobApplicationStatus::PENDING  => ['class' => 'warning', 'label' => $application->status->label()],
      \App\Enums\Jobs\JobApplicationStatus::ACCEPTED => ['class' => 'success', 'label' => $application->status->label()],
      \App\Enums\Jobs\JobApplicationStatus::REJECTED => ['class' => 'danger', 'label' => $application->status->label()],
    };
  @endphp

  <x-dashboard.breadcrumb :items="[
    ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes candidatures', 'href' => route('dashboard.member.applications')],
    ['label' => 'Détail'],
  ]" />

  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h1 class="fs-3 mb-1">{{ $job->title }}</h1>
        <p class="mb-0 text-muted">{{ $job->company?->name ?? '—' }} · {{ $job->location ?? '—' }}</p>
      </div>
      <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }} fs-6">{{ $status['label'] }}</span>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-white">
          <h2 class="h6 mb-0">Lettre de motivation</h2>
        </div>
        <div class="card-body">
          @if(filled($application->cover_letter))
            <p class="mb-0 whitespace-pre-wrap">{{ $application->cover_letter }}</p>
          @else
            <p class="mb-0 text-muted">Aucune lettre de motivation.</p>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-white">
          <h2 class="h6 mb-0">Informations</h2>
        </div>
        <div class="card-body">
          <dl class="mb-0">
            <dt class="text-muted small">Envoyée le</dt>
            <dd class="mb-3">{{ $application->created_at->format('d/m/Y à H:i') }}</dd>

            <dt class="text-muted small">Type de contrat</dt>
            <dd class="mb-3">{{ $job->type?->label() ?? '—' }}</dd>

            <dt class="text-muted small">CV joint</dt>
            <dd class="mb-0">
              @if(filled($application->cv_path))
                <a href="{{ route('cv.download', ['userId' => auth()->id()]) }}" class="text-decoration-none">
                  <i class="ti ti-download me-1"></i> Télécharger mon CV
                </a>
              @else
                <span class="text-muted">—</span>
              @endif
            </dd>
          </dl>
        </div>
      </div>

      <div class="d-grid gap-2 mt-3">
        <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-primary">
          <i class="ti ti-briefcase me-1"></i> Voir l'offre
        </a>
        <a href="{{ route('dashboard.member.applications') }}" class="btn btn-light">
          <i class="ti ti-arrow-left me-1"></i> Retour aux candidatures
        </a>
      </div>
    </div>
  </div>

@endsection
