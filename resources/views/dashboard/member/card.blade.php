@extends('layouts.dashboard')

@section('title', 'Ma carte membre — Laravel CI')

@section('content')
@php /** @var \App\Models\User $me */ @endphp

<x-dashboard.breadcrumb :items="[
  ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
  ['label' => 'Ma carte membre'],
]" />

<div class="row g-4">
  <div class="col-12">
    <h1 class="fs-4 fw-bold mb-1">Ma carte membre</h1>
    <p class="text-secondary mb-0">Ta carte est générée automatiquement selon ta réputation. Tu peux personnaliser ton poste et ton avatar.</p>
  </div>

  {{-- ── Niveau actuel et progression ──────────────────────── --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:48px;height:48px;background:var(--orange-light,#fff1e6)">
            <i class="fa-solid fa-id-card" style="color:var(--orange,#FF6600);font-size:1.2rem"></i>
          </div>
          <div>
            <div class="fw-bold">{{ number_format($points) }} points de réputation</div>
            <div class="text-secondary small">
              @php $nextLevel = collect([1,2,3])->first(fn($l) => ($thresholds[$l] ?? 999) > $points); @endphp
              @if($nextLevel)
                Prochain niveau ({{ config('member-card.level_names')[$nextLevel] }}) à {{ $thresholds[$nextLevel] }} pts
                — il te manque {{ $thresholds[$nextLevel] - $points }} pts
              @else
                Niveau maximum atteint 🎉
              @endif
            </div>
          </div>
        </div>
        {{-- Barre de progression globale --}}
        @php $maxPts = $thresholds[3] ?? 900; $pct = min(100, round($points / $maxPts * 100)); @endphp
        <div class="progress" style="height:8px;border-radius:4px">
          <div class="progress-bar" role="progressbar"
               style="width:{{ $pct }}%;background:linear-gradient(90deg,#FF6600,#FF9F1C)"
               aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
          @foreach([1,2,3] as $lvl)
            <small class="text-secondary">
              {{ config('member-card.level_names')[$lvl] }}
              <span class="fw-semibold">{{ $thresholds[$lvl] }} pts</span>
            </small>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- ── Cartes débloquées ─────────────────────────────────── --}}
  @forelse($cards as $card)
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body p-4">
        {{-- En-tête carte --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill"
                  style="background:{{ $card->level === 3 ? '#FFD700' : ($card->level === 2 ? '#1C1C2E' : '#FF6600') }};color:{{ $card->level === 3 ? '#1C1C2E' : '#fff' }}">
              Niveau {{ $card->level }} — {{ $card->levelName() }}
            </span>
            @if($card->is_active)
              <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
            @else
              <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
            @endif
            @if($card->forced_by_admin)
              <span class="badge bg-info-subtle text-info border border-info-subtle small">Activée par admin</span>
            @endif
          </div>
        </div>

        {{-- Aperçu iframe de la carte --}}
        <div class="mb-3 rounded overflow-hidden border" style="aspect-ratio:16/9">
          <iframe src="{{ route('member-card.preview', [$me->github_username, $card->level]) }}"
                  style="width:800px;height:450px;border:none;transform:scale(0.5);transform-origin:top left;pointer-events:none"
                  loading="lazy"></iframe>
        </div>

        {{-- Bouton téléchargement --}}
        <div class="d-flex gap-2 mb-4">
          <a href="{{ route('member-card.download', [$me->github_username, $card->level]) }}"
             class="btn btn-sm btn-dark">
            <i class="fa-solid fa-download me-1"></i> Télécharger PNG
          </a>
          <a href="{{ route('member-card.preview', [$me->github_username, $card->level]) }}"
             target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-external-link-alt me-1"></i> Voir en plein écran
          </a>
        </div>

        {{-- Formulaire personnalisation --}}
        <form action="{{ route('member-card.update', $card) }}" method="POST">
          @csrf @method('PATCH')
          <div class="mb-3">
            <label class="form-label fw-semibold small">Poste affiché sur la carte</label>
            <input type="text" name="poste" value="{{ old('poste', $card->poste) }}"
                   class="form-control form-control-sm"
                   placeholder="ex: Développeur Full-Stack, Lead Dev, CTO…"
                   maxlength="120">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Avatar spécifique à la carte (URL)</label>
            <input type="url" name="card_avatar" value="{{ old('card_avatar', $card->card_avatar) }}"
                   class="form-control form-control-sm"
                   placeholder="https://… (laissez vide pour utiliser votre avatar GitHub)">
            <div class="form-text">Par défaut, votre photo GitHub est utilisée.</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-sm" style="background:#FF6600;color:#fff;border:none">
              <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
            </button>
            @if(session('card_updated'))
              <span class="text-success small"><i class="fa-solid fa-check me-1"></i>Mis à jour</span>
            @endif
          </div>
        </form>

        {{-- Matricule --}}
        @if($me->matricule)
          <div class="mt-3 pt-3 border-top">
            <small class="text-secondary">Matricule</small>
            <div class="font-monospace small fw-semibold">{{ $me->matricule }}</div>
          </div>
        @endif
      </div>
    </div>
  </div>
  @empty
  {{-- Aucune carte --}}
  <div class="col-12">
    <div class="card border-dashed border-2 text-center p-5">
      <div class="mb-3" style="font-size:3rem">🪪</div>
      <h5 class="fw-bold">Aucune carte débloquée pour l'instant</h5>
      <p class="text-secondary mb-0">
        Il te faut <strong>{{ $thresholds[1] }} points</strong> pour débloquer ta première carte.<br>
        Tu en as actuellement <strong>{{ $points }}</strong> — encore {{ max(0, $thresholds[1] - $points) }} à gagner !
      </p>
    </div>
  </div>
  @endforelse
</div>

@endsection
