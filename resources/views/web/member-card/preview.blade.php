@extends('layouts.web')

@section('title', 'Carte de ' . $user->name . ' — Laravel CI')
@section('description', 'Carte membre officielle de ' . $user->name . ' sur la plateforme Laravel Côte d\'Ivoire.')

@push('styles')
<style>
  .card-preview-page {
    background: var(--orange-50, #fff8f2);
    min-height: calc(100vh - 160px);
    padding: 48px 0 64px;
  }

  /* ── Hero identité ── */
  .card-member-identity {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 36px;
  }
  .card-member-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--orange);
  }
  .card-member-avatar-init {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: var(--orange);
    color: #fff;
    font-weight: 700;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .card-member-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--navy, #0d1726);
    margin: 0 0 2px;
  }
  .card-member-gh {
    font-family: 'JetBrains Mono', monospace;
    font-size: .8rem;
    color: var(--muted, #888);
  }

  /* ── Carte iframe container ── */
  .card-iframe-outer {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
  }
  .card-iframe-scaler {
    width: 800px;
    height: 450px;
    transform-origin: top left;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 24px 72px rgba(13, 23, 38, .18), 0 4px 12px rgba(13, 23, 38, .08);
  }
  .card-iframe-scaler iframe {
    width: 800px;
    height: 450px;
    border: 0;
    display: block;
    border-radius: 22px;
  }

  /* ── Niveau selector ── */
  .level-tabs {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 28px;
    flex-wrap: wrap;
  }
  .level-tab {
    padding: 6px 18px;
    border-radius: 30px;
    font-size: .8rem;
    font-weight: 600;
    border: 1.5px solid rgba(0,0,0,.12);
    color: var(--muted, #888);
    background: #fff;
    text-decoration: none;
    transition: all .18s ease;
    letter-spacing: .04em;
  }
  .level-tab:hover {
    border-color: var(--orange);
    color: var(--orange);
  }
  .level-tab.active {
    background: var(--orange);
    border-color: var(--orange);
    color: #fff;
  }

  /* ── Actions ── */
  .card-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 28px;
    flex-wrap: wrap;
  }

  /* ── Breadcrumb ── */
  .breadcrumb-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .8rem;
    color: var(--muted, #888);
    margin-bottom: 24px;
  }
  .breadcrumb-bar a { color: var(--orange); }
  .breadcrumb-bar i { font-size: .65rem; opacity: .5; }
</style>
@endpush

@section('content')
<div class="card-preview-page">
  <div class="container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-bar">
      <a href="{{ route('home') }}">Accueil</a>
      <i class="fa-solid fa-chevron-right"></i>
      <a href="{{ route('members.show', $user->github_username) }}">{{ $user->name }}</a>
      <i class="fa-solid fa-chevron-right"></i>
      <span>Carte membre</span>
    </div>

    {{-- Identité --}}
    <div class="card-member-identity">
      @if($user->avatar)
        <img class="card-member-avatar" src="{{ $user->avatar }}" alt="{{ $user->name }}"/>
      @else
        <div class="card-member-avatar-init">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</div>
      @endif
      <div>
        <p class="card-member-name">{{ $user->name }}</p>
        <p class="card-member-gh">{{ '@' . $user->github_username }}</p>
      </div>
    </div>

    {{-- Sélecteur de niveau (si plusieurs cartes) --}}
    @if($allCards->count() > 1)
      <div class="level-tabs">
        @foreach($allCards as $c)
          <a href="{{ route('member-card.preview', [$user->github_username, $c->level]) }}"
             class="level-tab {{ $c->id === $card->id ? 'active' : '' }}">
            {{ $c->levelName() }}
          </a>
        @endforeach
      </div>
    @endif

    {{-- Carte centrée avec iframe scalable --}}
    <div class="card-iframe-outer">
      <div class="card-iframe-scaler" id="cardScaler">
        <iframe
          src="{{ route('member-card.embed', [$user->github_username, $card->level]) }}"
          title="Carte membre de {{ $user->name }}"
          scrolling="no"
          loading="eager">
        </iframe>
      </div>
    </div>

    {{-- Actions --}}
    <div class="card-actions">
      <a href="{{ route('member-card.download', [$user->github_username, $card->level]) }}"
         class="btn btn-brand">
        <i class="fa-solid fa-download me-2"></i>Télécharger PNG
      </a>
      <button class="btn btn-outline-brand" onclick="copyCardLink()">
        <i class="fa-solid fa-link me-2"></i>Copier le lien
      </button>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  /* Scale the iframe to fill its container while keeping the 800×450 aspect ratio */
  function scaleCard() {
    const outer  = document.querySelector('.card-iframe-outer');
    const scaler = document.getElementById('cardScaler');
    if (!outer || !scaler) return;

    const available = outer.offsetWidth;
    const scale     = Math.min(1, available / 800);

    scaler.style.transform = 'scale(' + scale + ')';
    scaler.style.marginBottom = '-' + (450 * (1 - scale)) + 'px';
    outer.style.height = (450 * scale) + 'px';
  }

  scaleCard();
  window.addEventListener('resize', scaleCard);

  function copyCardLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
      const btn = event.currentTarget;
      btn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Lien copié !';
      setTimeout(() => {
        btn.innerHTML = '<i class="fa-solid fa-link me-2"></i>Copier le lien';
      }, 2000);
    });
  }
</script>
@endpush
