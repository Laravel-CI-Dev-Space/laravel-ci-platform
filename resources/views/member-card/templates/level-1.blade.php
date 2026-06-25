{{--
  CARTE MEMBRE — Niveau 1 : Initié (300 pts)
  Style : blanc lumineux, bande orange, hard shadow brutaliste
  Variables : $card (MemberCard avec user, user.profile.grade chargés)
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { width: 800px; height: 450px; overflow: hidden; font-family: 'Outfit', system-ui, sans-serif; background: #fff; }

  .card {
    position: relative;
    width: 800px; height: 450px;
    background: #FFFFFF;
    border: 2.5px solid #1C1C2E;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 7px 7px 0 #1C1C2E;
  }

  /* Grille de fond subtile */
  .card::before {
    content: "";
    position: absolute; inset: 0;
    background-image: linear-gradient(#E9ECEF 1px, transparent 1px), linear-gradient(90deg, #E9ECEF 1px, transparent 1px);
    background-size: 36px 36px;
    opacity: 0.4;
    pointer-events: none;
  }

  /* Bande orange gauche */
  .stripe {
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 10px;
    background: linear-gradient(180deg, #FF6600, #E65C00);
    border-radius: 22px 0 0 22px;
  }

  .inner { position: relative; z-index: 1; padding: 36px 36px 28px 52px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }

  /* Header */
  .header { display: flex; align-items: center; gap: 20px; }
  .avatar {
    width: 72px; height: 72px; border-radius: 50%;
    border: 3px solid #1C1C2E;
    object-fit: cover; flex-shrink: 0;
    background: #F8F9FA;
  }
  .avatar-placeholder {
    width: 72px; height: 72px; border-radius: 50%;
    border: 3px solid #1C1C2E;
    background: #F8F9FA;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #6C757D; flex-shrink: 0;
  }
  .identity { flex: 1; }
  .name { font-size: 22px; font-weight: 700; color: #1C1C2E; letter-spacing: -0.02em; line-height: 1.1; }
  .github { font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #FF6600; margin-top: 3px; }
  .poste { font-size: 13px; color: #6C757D; margin-top: 5px; font-weight: 500; }

  /* Badge niveau */
  .badge-level {
    display: inline-flex; align-items: center; gap: 8px;
    background: #FFF1E6; border: 1.5px solid #FF6600;
    border-radius: 999px; padding: 6px 14px;
    font-size: 12px; font-weight: 700; color: #FF6600;
    letter-spacing: 0.06em; text-transform: uppercase;
  }

  /* Divider */
  .divider { height: 2px; background: linear-gradient(90deg, #FF6600 60%, transparent); margin: 0 0; border-radius: 2px; }

  /* Infos grille */
  .info-row { display: flex; align-items: flex-end; justify-content: space-between; }

  .info-left {}
  .grade-label { font-size: 11px; font-weight: 600; color: #6C757D; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 4px; }
  .grade-name { font-size: 17px; font-weight: 700; color: #1C1C2E; }
  .since { font-size: 11px; color: #6C757D; margin-top: 6px; }
  .matricule { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #9CA3AF; margin-top: 3px; letter-spacing: 0.04em; }

  .info-right { display: flex; align-items: flex-end; gap: 20px; }

  /* Points + barre */
  .points-block { text-align: right; }
  .points-value { font-size: 28px; font-weight: 700; color: #FF6600; line-height: 1; letter-spacing: -0.03em; }
  .points-label { font-size: 10px; color: #6C757D; text-transform: uppercase; letter-spacing: 0.08em; }
  .progress-bar { width: 120px; height: 5px; background: #FFE8D6; border-radius: 3px; margin-top: 6px; overflow: hidden; }
  .progress-fill { height: 100%; background: linear-gradient(90deg, #FF6600, #FF9F1C); border-radius: 3px; }

  /* QR code */
  .qr-wrap {
    width: 72px; height: 72px;
    border: 2px solid #1C1C2E;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    flex-shrink: 0;
  }
  .qr-wrap svg { width: 72px !important; height: 72px !important; }

  /* Logo CI */
  .logo-ci {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700; color: #1C1C2E;
  }
  .logo-ci img { height: 24px; }

  /* Coin badge niveau en haut à droite */
  .top-right { position: absolute; top: 24px; right: 28px; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
</head>
<body>
<div class="card">
  <div class="stripe"></div>

  <div class="top-right">
    <div class="badge-level">
      <span>⬡</span> {{ $card->levelName() }}
    </div>
    <div class="logo-ci">
      <img src="{{ public_path('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
      Laravel CI
    </div>
  </div>

  <div class="inner">
    {{-- Header --}}
    <div class="header">
      @if($card->resolvedAvatar())
        <img class="avatar" src="{{ $card->resolvedAvatar() }}" alt="{{ $card->user->name }}" />
      @else
        <div class="avatar-placeholder">{{ mb_strtoupper(mb_substr($card->user->name, 0, 1)) }}</div>
      @endif
      <div class="identity">
        <div class="name">{{ $card->user->name }}</div>
        <div class="github">{{ '@' . $card->user->github_username }}</div>
        @if($card->poste)
          <div class="poste">{{ $card->poste }}</div>
        @endif
      </div>
    </div>

    {{-- Divider --}}
    <div class="divider"></div>

    {{-- Infos bas --}}
    <div class="info-row">
      <div class="info-left">
        <div class="grade-label">Grade communauté</div>
        <div class="grade-name">{{ $card->gradeName() }}</div>
        <div class="since">Membre depuis {{ $card->user->created_at->translatedFormat('M Y') }}</div>
        <div class="matricule">{{ $card->user->matricule }}</div>
      </div>

      <div class="info-right">
        @php $points = $card->user->profile?->points ?? 0; $max = config('member-card.thresholds.3', 900); $pct = min(100, round($points / $max * 100)); @endphp
        <div class="points-block">
          <div class="points-value">{{ number_format($points) }}</div>
          <div class="points-label">points</div>
          <div class="progress-bar"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
        </div>

        @if($card->qr_code_svg)
          <div class="qr-wrap">{!! $card->qr_code_svg !!}</div>
        @endif
      </div>
    </div>
  </div>
</div>
</body>
</html>
