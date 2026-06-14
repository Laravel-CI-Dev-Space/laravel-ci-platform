@extends('layouts.dashboard')

@section('title', 'Candidatures — Laravel CI')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <div class="breadcrumb-bar text-muted mb-1" style="font-size:.85rem">
            <a href="{{ route('company.offers.index') }}" class="text-muted">Mes offres</a>
            <i class="ti ti-chevron-right mx-1"></i>
            <span>{{ Str::limit($offer->title, 50) }}</span>
        </div>
        <h1 class="fs-3 fw-bold mb-0">Candidatures reçues</h1>
        <p class="text-muted mt-1 mb-0">
            {{ $applications->total() }} candidature{{ $applications->total() !== 1 ? 's' : '' }}
            pour <strong>{{ $offer->title }}</strong>
        </p>
    </div>
    <a href="{{ route('company.offers.index') }}" class="btn btn-ghost">
        <i class="ti ti-arrow-left me-1"></i>Retour
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm table-responsive">
    <table class="table mb-0 table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Candidat</th>
                <th>Date</th>
                <th>Statut</th>
                <th>CV</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $application)
                @php
                $badge = match($application->status) {
                    \App\Enums\JobApplicationStatus::Pending     => ['bg-secondary-subtle text-secondary', 'En attente'],
                    \App\Enums\JobApplicationStatus::Viewed      => ['bg-info-subtle text-info', 'Vue'],
                    \App\Enums\JobApplicationStatus::Shortlisted => ['bg-primary-subtle text-primary', 'Présélectionnée'],
                    \App\Enums\JobApplicationStatus::Accepted    => ['bg-success-subtle text-success', 'Acceptée'],
                    \App\Enums\JobApplicationStatus::Rejected    => ['bg-danger-subtle text-danger', 'Refusée'],
                };
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:.9rem">{{ $application->user->name }}</div>
                        <div class="text-muted" style="font-size:.78rem">{{ $application->user->email }}</div>
                    </td>
                    <td class="text-muted" style="font-size:.85rem">
                        {{ $application->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <span class="badge {{ $badge[0] }}" style="font-size:.72rem">{{ $badge[1] }}</span>
                    </td>
                    <td>
                        @if ($application->cv_path)
                            <a href="{{ route('company.applications.cv', $application) }}"
                               class="btn btn-light btn-sm px-2" title="Télécharger le CV">
                                <i class="ti ti-download" style="font-size:1rem"></i>
                            </a>
                        @else
                            <span class="text-muted" style="font-size:.8rem">Aucun</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('company.applications.show', $application) }}"
                           class="btn btn-light btn-sm px-2" title="Voir le détail">
                            <i class="ti ti-eye" style="font-size:1rem"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="ti ti-inbox d-block mb-2" style="font-size:2rem;opacity:.4"></i>
                        Aucune candidature reçue pour cette offre.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($applications->hasPages())
    <div class="mt-3">{{ $applications->links() }}</div>
@endif

@endsection
