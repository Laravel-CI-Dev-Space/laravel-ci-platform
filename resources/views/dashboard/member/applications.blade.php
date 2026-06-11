@extends('layouts.dashboard')

@section('title', 'Mes candidatures')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Mes candidatures']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Mes candidatures</h1>
          <p class="mb-0 text-muted">Suivez l'état de vos candidatures.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="ti ti-briefcase me-1"></i> Parcourir les offres</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-responsive">
        <table class="table mb-0 table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Poste</th>
              <th>Entreprise</th>
              <th>Contrat</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applications as $application)
              @php
                $job = $application->jobOffer;
                $status = match ($application->status) {
                  \App\Enums\Jobs\JobApplicationStatus::PENDING  => ['class' => 'warning', 'label' => $application->status->label()],
                  \App\Enums\Jobs\JobApplicationStatus::ACCEPTED => ['class' => 'success', 'label' => $application->status->label()],
                  \App\Enums\Jobs\JobApplicationStatus::REJECTED => ['class' => 'danger', 'label' => $application->status->label()],
                };
              @endphp
              <tr class="align-middle">
                <td>
                  <a href="{{ route('jobs.show', $job) }}" class="text-navy fw-semibold">{{ $job->title }}</a>
                </td>
                <td>{{ $job->company?->name ?? '—' }}</td>
                <td>{{ $job->type?->label() ?? '—' }}</td>
                <td>{{ $application->created_at->format('d/m/Y') }}</td>
                <td>
                  <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }}">{{ $status['label'] }}</span>
                </td>
                <td>
                  <a href="{{ route('dashboard.member.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> Détails
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-secondary">
                  <i class="ti ti-briefcase fs-1 d-block mb-2"></i>
                  Aucune candidature. <a href="{{ route('jobs.index') }}">Parcourir les offres !</a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($applications instanceof \Illuminate\Pagination\LengthAwarePaginator && $applications->hasPages())
        <div class="mt-3">{{ $applications->links() }}</div>
      @endif
    </div>
  </div>

@endsection
