@extends('layouts.dashboard')

@section('title', 'Mes événements — Laravel CI')

@section('content')

  <x-dashboard.breadcrumb :items="[
    ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes événements'],
  ]" />

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Mes événements</h4>
      <small class="text-muted">Événements auxquels vous êtes inscrit(e).</small>
    </div>
    <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
      <i class="ti ti-calendar-event me-1"></i> Voir tous les événements
    </a>
  </div>

  <div class="row g-3">
    @forelse($registrations as $registration)
    @php $event = $registration->event; @endphp
    @if($event)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex gap-3 mb-3">
              <div class="text-center flex-shrink-0" style="background:#fff7f0;border:1px solid #fde8d8;border-radius:10px;padding:8px 14px;min-width:54px">
                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;color:#e8580a;letter-spacing:.05em">
                  {{ $event->starts_at->translatedFormat('M') }}
                </div>
                <div style="font-size:1.5rem;font-weight:800;line-height:1;color:#1a1a2e">
                  {{ $event->starts_at->format('d') }}
                </div>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                  <span class="badge" style="font-size:.68rem;background:{{ $event->isPast() ? '#f3f4f6' : '#fff7f0' }};color:{{ $event->isPast() ? '#6b7280' : '#e8580a' }};border:1px solid {{ $event->isPast() ? '#e0e0e0' : '#fde8d8' }}">
                    {{ $event->isPast() ? 'Passé' : 'À venir' }}
                  </span>
                  <span class="badge bg-{{ $registration->status->color() }}-subtle text-{{ $registration->status->color() }}" style="font-size:.68rem">
                    {{ $registration->status->label() }}
                  </span>
                </div>
                <h3 class="mb-0 fw-semibold" style="font-size:.95rem;line-height:1.3">
                  <a href="{{ route('events.show', $event) }}" class="text-dark text-decoration-none">
                    {{ $event->title }}
                  </a>
                </h3>
              </div>
            </div>

            <div class="text-muted mb-3" style="font-size:.82rem">
              <div class="mb-1">
                <i class="ti ti-clock me-1"></i>
                {{ $event->starts_at->format('H:i') }}
                @if($event->ends_at)
                  — {{ $event->ends_at->format('H:i') }}
                @endif
              </div>
              @if($event->location)
                <div>
                  <i class="ti ti-map-pin me-1"></i> {{ $event->location }}
                </div>
              @endif
            </div>

            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm w-100">
              Voir l'événement
            </a>
          </div>
        </div>
      </div>
    @endif
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-5 text-center text-muted">
            <i class="ti ti-calendar-event d-block mb-3" style="font-size:2.5rem;opacity:.3"></i>
            <div class="fw-medium mb-1">Aucun événement pour l'instant</div>
            <p class="small mb-3">Vous n'êtes inscrit(e) à aucun événement.</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
              <i class="ti ti-calendar-event me-1"></i> Parcourir les événements
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

@endsection
