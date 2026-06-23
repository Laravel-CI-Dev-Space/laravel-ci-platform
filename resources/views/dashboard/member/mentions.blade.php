@extends('layouts.dashboard')

@section('title', 'Mes mentions — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes mentions'],
]" />

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div>
                <h4 class="mb-0 fw-bold">Mes mentions</h4>
                <small class="text-muted">Toutes les fois où un membre vous a tagué avec #{{ auth()->user()->github_username }}</small>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">

                @if ($mentions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-hash" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
                        <div class="fw-medium mb-1">Aucune mention pour l'instant</div>
                        <small>Quand un membre vous taguera avec <code>#{{ auth()->user()->github_username }}</code>, vous verrez la notification ici.</small>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($mentions as $mention)
                        @php
                            $data    = $mention->data;
                            $message = is_array($data) ? ($data['message'] ?? '') : (json_decode($data, true)['message'] ?? '');
                            $url     = is_array($data) ? ($data['url'] ?? '#') : (json_decode($data, true)['url'] ?? '#');
                            $isRead  = ! is_null($mention->read_at);
                        @endphp
                        <li class="list-group-item px-4 py-3 {{ $isRead ? '' : 'bg-light' }}">
                            <div class="d-flex align-items-start gap-3">
                                <div class="mt-1" style="color:#e8580a;font-size:1.1rem;flex-shrink:0">
                                    <i class="ti ti-hash"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium" style="font-size:.9rem">
                                        {{ $message }}
                                        @if (! $isRead)
                                            <span class="badge ms-2" style="background:#e8580a;color:#fff;font-size:.65rem;border-radius:9999px;padding:2px 8px">Nouveau</span>
                                        @endif
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:.78rem">
                                        {{ $mention->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                @if ($url && $url !== '#')
                                    <a href="{{ $url }}" class="btn btn-sm btn-outline-secondary flex-shrink-0" style="font-size:.78rem">
                                        Voir <i class="ti ti-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    @if ($mentions->hasPages())
                        <div class="px-4 py-3 border-top">
                            {{ $mentions->links() }}
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
