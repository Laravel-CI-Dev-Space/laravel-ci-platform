@extends('layouts.dashboard')

@section('title', 'Mes offres — Laravel CI')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Mes offres d'emploi</h1>
        <p class="text-muted mb-0">Gérez vos annonces et suivez les candidatures.</p>
    </div>
    <a href="{{ route('company.offers.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Publier une offre
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm table-responsive">
    <table class="table mb-0 table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Candidatures</th>
                <th>Vues</th>
                <th>Publié le</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($offers as $offer)
                @php
                $statusBadge = match($offer->status) {
                    'active'   => ['bg-success-subtle text-success',     'Actif'],
                    'pending'  => ['bg-warning-subtle text-warning',     'En attente'],
                    'draft'    => ['bg-secondary-subtle text-secondary', 'Brouillon'],
                    'expired'  => ['bg-danger-subtle text-danger',       'Expiré'],
                    'filled'   => ['bg-info-subtle text-info',           'Pourvu'],
                    'rejected' => ['bg-danger-subtle text-danger',       'Refusé'],
                    default    => ['bg-secondary-subtle text-secondary', ucfirst($offer->status)],
                };
                $contractLabel = match($offer->contract_type) {
                    'cdi'            => 'CDI',
                    'cdd'            => 'CDD',
                    'freelance'      => 'Freelance',
                    'internship'     => 'Stage',
                    'apprenticeship' => 'Alternance',
                    default          => ucfirst($offer->contract_type),
                };
                @endphp
                <tr>
                    <td style="max-width:280px">
                        <div class="fw-semibold" style="font-size:.9rem">
                            {{ Str::limit($offer->title, 55) }}
                        </div>
                        @if ($offer->is_urgent)
                            <span class="badge bg-danger-subtle text-danger" style="font-size:.68rem">URGENT</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light border text-secondary" style="font-size:.75rem">{{ $contractLabel }}</span></td>
                    <td><span class="badge {{ $statusBadge[0] }}" style="font-size:.75rem">{{ $statusBadge[1] }}</span></td>
                    <td>
                        <a href="{{ route('company.applications.index', $offer) }}" class="text-decoration-none">
                            {{ $offer->applications_count }}
                            <i class="ti ti-arrow-right ms-1" style="font-size:.75rem"></i>
                        </a>
                    </td>
                    <td>{{ number_format($offer->views_count) }}</td>
                    <td class="text-muted" style="font-size:.85rem">
                        {{ $offer->published_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('company.applications.index', $offer) }}"
                           class="btn btn-light btn-sm px-2" title="Voir les candidatures">
                            <i class="ti ti-users" style="font-size:1rem"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="ti ti-briefcase d-block mb-2" style="font-size:2rem;opacity:.4"></i>
                        Aucune offre pour le moment.
                        <a href="{{ route('company.offers.create') }}" class="text-primary">Publiez votre première offre !</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($offers instanceof \Illuminate\Pagination\LengthAwarePaginator && $offers->hasPages())
    <div class="mt-3">{{ $offers->links() }}</div>
@endif

@endsection
