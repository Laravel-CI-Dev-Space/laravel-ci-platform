{{--
  CARTE MEMBRE — Niveau 2 : Bâtisseur (600 pts)
  Style : dark navy, orange + vert actif, texture grain
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { width: 800px; height: 450px; overflow: hidden; font-family: 'Outfit', system-ui, sans-serif; background: #1C1C2E; }

  .card {
    position: relative;
    width: 800px; height: 450px;
    background: linear-gradient(135deg, #1C1C2E 0%, #2A2A42 100%);
    border: 1.5px solid rgba(255,255,255,0.10);
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 0 60px rgba(255,102,0,0.18), 6px 6px 0 rgba(255,102,0,0.35);
  }

  /* Texture grain */
  .card::before {
    content: "";
    position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    opacity: 0.06;
    pointer-events: none;
  }

  /* Motif de points */
  .card::after {
    content: "";
    position: absolute; top: 0; right: 0; width: 200px; height: 200px;
    background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
    background-size: 16px 16px;
    pointer-events: none;
  }

  .inner { position: relative; z-index: 1; padding: 32px 36px 28px 36px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }

  /* Header */
  .header { display: flex; align-items: center; gap: 18px; }
  .avatar-wrap { position: relative; flex-shrink: 0; }
  .avatar {
    width: 68px; height: 68px; border-radius: 50%;
    border: 3px solid #FF6600;
    object-fit: cover;
    background: #2A2A42;
  }
  .avatar-placeholder {
    width: 68px; height: 68px; border-radius: 50%;
    border: 3px solid #FF6600;
    background: #2A2A42;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #FF6600;
  }
  .active-dot {
    position: absolute; bottom: 2px; right: 2px;
    width: 12px; height: 12px; border-radius: 50%;
    background: #2ECC71;
    border: 2px solid #1C1C2E;
  }
  .identity { flex: 1; }
  .name { font-size: 22px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.02em; line-height: 1.1; }
  .github { font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #FF6600; margin-top: 3px; }
  .poste { font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 5px; font-weight: 500; }
  .active-label { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #2ECC71; text-transform: uppercase; letter-spacing: 0.08em; }

  /* Badge niveau */
  .badge-level {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,102,0,0.12); border: 1.5px solid rgba(255,102,0,0.40);
    border-radius: 999px; padding: 6px 14px;
    font-size: 12px; font-weight: 700; color: #FF6600;
    letter-spacing: 0.06em; text-transform: uppercase;
  }

  /* Divider */
  .divider { height: 2px; background: linear-gradient(90deg, #FF6600 50%, transparent); border-radius: 2px; }

  /* Infos bas */
  .info-row { display: flex; align-items: flex-end; justify-content: space-between; }

  .info-left {}
  .grade-label { font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.40); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 4px; }
  .grade-name { font-size: 17px; font-weight: 700; color: #FFFFFF; }
  .since { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 6px; }
  .matricule { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: rgba(255,255,255,0.28); margin-top: 3px; letter-spacing: 0.04em; }

  .info-right { display: flex; align-items: flex-end; gap: 20px; }

  .points-block { text-align: right; }
  .points-value { font-size: 28px; font-weight: 700; color: #FF6600; line-height: 1; letter-spacing: -0.03em; }
  .points-label { font-size: 10px; color: rgba(255,255,255,0.40); text-transform: uppercase; letter-spacing: 0.08em; }
  .progress-bar { width: 120px; height: 5px; background: rgba(255,102,0,0.18); border-radius: 3px; margin-top: 6px; overflow: hidden; }
  .progress-fill { height: 100%; background: linear-gradient(90deg, #FF6600, #FF9F1C); border-radius: 3px; }

  .qr-wrap {
    width: 70px; height: 70px;
    border: 2px solid rgba(255,255,255,0.15);
    border-radius: 8px; overflow: hidden;
    background: #fff; flex-shrink: 0;
  }
  .qr-wrap svg { width: 70px !important; height: 70px !important; }

  /* Logo CI */
  .logo-ci { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: rgba(255,255,255,0.80); }
  .logo-ci img { height: 22px; filter: brightness(0) invert(1); }

  .top-right { position: absolute; top: 24px; right: 28px; display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
</head>
<body>
<div class="card">
  <div class="top-right">
    <div class="badge-level">🔨 {{ $card->levelName() }}</div>
    <div class="logo-ci">
      <img src="{{ public_path('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
      Laravel CI
    </div>
  </div>

  <div class="inner">
    <div class="header">
      <div class="avatar-wrap">
        @if($card->resolvedAvatar())
          <img class="avatar" src="{{ $card->resolvedAvatar() }}" alt="{{ $card->user->name }}" />
        @else
          <div class="avatar-placeholder">{{ mb_strtoupper(mb_substr($card->user->name, 0, 1)) }}</div>
        @endif
        <div class="active-dot"></div>
      </div>
      <div class="identity">
        <div class="name">{{ $card->user->name }}</div>
        <div class="github">@{{ $card->user->github_username }}</div>
        @if($card->poste)
          <div class="poste">{{ $card->poste }}</div>
        @endif
        <div class="active-label" style="margin-top:6px">
          <span style="width:7px;height:7px;border-radius:50%;background:#2ECC71;display:inline-block"></span>
          Actif
        </div>
      </div>
    </div>

    <div class="divider"></div>

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
