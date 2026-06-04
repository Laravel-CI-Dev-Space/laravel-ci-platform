@extends('layouts.dashboard')

@section('title', 'Tableau de bord Entreprise — Laravel CI')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Bonjour, {{ $account->first_name }} 👋</h1>
        <p class="text-muted mb-0">
            {{ $company?->name ?? 'Votre espace entreprise' }} · Tableau de bord
        </p>
    </div>
    <a href="{{ route('company.offers.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Publier une offre
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach ([
        ['Offres actives',       $stats['active_offers'],        '#f0f4ff', '#4a6cf7', 'ti-briefcase'],
        ['Total des offres',     $stats['total_offers'],         '#fff5e0', '#f39c12', 'ti-list'],
        ['Candidatures totales', $stats['total_applications'],   '#edfaf3', '#2ecc71', 'ti-users'],
        ['En attente',           $stats['pending_applications'],  '#fff0ef', '#e74c3c', 'ti-clock'],
    ] as [$label, $count, $bg, $color, $icon])
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;flex-shrink:0;background:{{ $bg }}">
                        <i class="ti {{ $icon }}" style="font-size:1.3rem;color:{{ $color }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $count }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    {{-- Candidatures récentes --}}
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                <h5 class="mb-0">Candidatures récentes</h5>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($recentApplications as $application)
                    <li class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem">
                                    {{ $application->user->name }}
                                </div>
                                <div class="text-muted" style="font-size:.8rem">
                                    {{ $application->jobOffer->title ?? '—' }}
                                    · {{ $application->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @php
                            $badge = match($application->status) {
                                'pending'     => ['bg-secondary-subtle text-secondary', 'En attente'],
                                'viewed'      => ['bg-info-subtle text-info', 'Vue'],
                                'shortlisted' => ['bg-primary-subtle text-primary', 'Présélectionnée'],
                                'accepted'    => ['bg-success-subtle text-success', 'Acceptée'],
                                'rejected'    => ['bg-danger-subtle text-danger', 'Refusée'],
                                default       => ['bg-secondary-subtle text-secondary', ucfirst($application->status)],
                            };
                            @endphp
                            <span class="badge {{ $badge[0] }} small">{{ $badge[1] }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item px-4 py-5 text-center text-secondary">
                        <i class="ti ti-inbox d-block mb-2" style="font-size:1.8rem"></i>
                        Aucune candidature pour le moment.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Offres actives --}}
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                <h5 class="mb-0">Mes offres actives</h5>
                <a href="{{ route('company.offers.index') }}" class="small text-primary">Voir tout</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($activeOffers as $offer)
                    <li class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem">{{ Str::limit($offer->title, 50) }}</div>
                                <div class="text-muted" style="font-size:.8rem">
                                    {{ $offer->applications_count }} candidature{{ $offer->applications_count !== 1 ? 's' : '' }}
                                    · Expire {{ $offer->expires_at?->diffForHumans() ?? 'sans limite' }}
                                </div>
                            </div>
                            <a href="{{ route('company.applications.index', $offer) }}" class="btn btn-light btn-sm">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item px-4 py-5 text-center text-secondary">
                        <i class="ti ti-briefcase d-block mb-2" style="font-size:1.8rem"></i>
                        Aucune offre active.
                        <a href="{{ route('company.offers.create') }}" class="text-primary">Publiez votre première offre !</a>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@endsection
