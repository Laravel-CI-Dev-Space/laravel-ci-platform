@extends('layouts.dashboard')

@section('title', 'Mes candidatures — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes candidatures'],
]" />

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Mes candidatures</h1>
        <p class="text-muted mb-0">Suivez l'état de toutes vos candidatures.</p>
    </div>
    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
        <i class="ti ti-briefcase me-1"></i>Voir les offres
    </a>
</div>

{{-- Stats rapides --}}
@php
    $total      = $applications->total();
    $pending    = $applications->getCollection()->where('status', 'pending')->count();
    $shortlisted = $applications->getCollection()->where('status', 'shortlisted')->count();
    $accepted   = $applications->getCollection()->where('status', 'accepted')->count();
@endphp

<div class="row g-3 mb-4">
    @foreach ([
        ['Total',          $total,        '#f0f4ff', '#4a6cf7', 'ti-send'],
        ['En attente',     $pending,      '#fff5e0', '#f39c12', 'ti-clock'],
        ['Présélectionné', $shortlisted,  '#f0f4ff', '#3498db', 'ti-star'],
        ['Acceptée',       $accepted,     '#edfaf3', '#2ecc71', 'ti-circle-check'],
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

{{-- Liste des candidatures --}}
<div class="row g-3">
    @forelse ($applications as $application)
        @php
            $offer = $application->jobOffer;
            $statusMap = [
                'pending'     => ['bg-secondary-subtle text-secondary', 'ti-clock',        'En attente'],
                'viewed'      => ['bg-info-subtle text-info',           'ti-eye',          'Vue'],
                'shortlisted' => ['bg-primary-subtle text-primary',     'ti-star',         'Présélectionné'],
                'accepted'    => ['bg-success-subtle text-success',     'ti-circle-check', 'Acceptée'],
                'rejected'    => ['bg-danger-subtle text-danger',       'ti-x',            'Refusée'],
            ];
            [$sBadge, $sIcon, $sLabel] = $statusMap[$application->status] ?? $statusMap['pending'];

            $contractLabel = match($offer->contract_type ?? '') {
                'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance',
                'internship' => 'Stage', 'apprenticeship' => 'Alternance', default => '—',
            };
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden"
                 style="border-left:4px solid
                    @if ($application->status === 'accepted') #2ecc71
                    @elseif ($application->status === 'rejected') #e74c3c
                    @elseif ($application->status === 'shortlisted') #3498db
                    @elseif ($application->status === 'viewed') #17a2b8
                    @else #adb5bd
                    @endif !important; border-radius:.75rem; transition:box-shadow .15s;"
                 onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.09)'"
                 onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">

                        {{-- Info offre --}}
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge {{ $sBadge }} d-inline-flex align-items-center gap-1" style="font-size:.72rem">
                                    <i class="ti {{ $sIcon }}"></i>{{ $sLabel }}
                                </span>
                                @if ($offer->is_urgent ?? false)
                                    <span class="badge" style="background:#fdeaec;color:#e74c3c;font-size:.68rem">URGENT</span>
                                @endif
                            </div>

                            <h6 class="fw-bold mb-1" style="font-size:.98rem">
                                @if ($offer->status === 'active')
                                    <a href="{{ route('jobs.show', $offer->slug) }}" class="text-dark text-decoration-none">
                                        {{ $offer->title }}
                                    </a>
                                @else
                                    {{ $offer->title }}
                                @endif
                            </h6>

                            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:.82rem;color:#6c757d">
                                @if ($offer->company)
                                    <span><i class="ti ti-building me-1"></i>{{ $offer->company->name }}</span>
                                @endif
                                @if ($offer->location)
                                    <span><i class="ti ti-map-pin me-1"></i>{{ $offer->location }}</span>
                                @endif
                                <span><i class="ti ti-file-text me-1"></i>{{ $contractLabel }}</span>
                                @if ($offer->is_remote ?? false)
                                    <span><i class="ti ti-wifi me-1"></i>Remote</span>
                                @endif
                            </div>

                            @if ($offer->categories->isNotEmpty())
                                <div class="d-flex gap-1 flex-wrap mt-2">
                                    @foreach ($offer->categories->take(3) as $cat)
                                        <span class="badge bg-light border text-secondary" style="font-size:.68rem">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($application->employer_note)
                                <div class="mt-2 p-2 rounded-2" style="background:#f8f9fa;font-size:.8rem;color:#495057;">
                                    <i class="ti ti-message me-1 text-muted"></i>
                                    <strong>Note de l'employeur :</strong> {{ $application->employer_note }}
                                </div>
                            @endif
                        </div>

                        {{-- Meta + Actions --}}
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                            <span class="text-muted" style="font-size:.78rem">
                                <i class="ti ti-calendar me-1"></i>{{ $application->created_at->format('d M Y') }}
                            </span>
                            @if ($offer->salaryRange())
                                <span style="font-size:.82rem;font-weight:600;color:#e8590c">{{ $offer->salaryRange() }}</span>
                            @endif
                            @if ($offer->status === 'active')
                                <a href="{{ route('jobs.show', $offer->slug) }}" class="btn btn-light btn-sm px-2" title="Voir l'offre">
                                    <i class="ti ti-external-link" style="font-size:1rem"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="ti ti-send d-block mb-3 text-muted" style="font-size:3rem;opacity:.35"></i>
                    <p class="fw-semibold mb-2">Vous n'avez pas encore postulé à une offre.</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-briefcase me-1"></i>Parcourir les offres
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if ($applications->hasPages())
    <div class="mt-4">{{ $applications->links() }}</div>
@endif

@endsection
