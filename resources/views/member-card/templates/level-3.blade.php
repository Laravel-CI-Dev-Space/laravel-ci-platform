{{--
  CARTE MEMBRE — Niveau 3 : Maître Artisan (900+ pts)
  Style : premium dark, gradient gold-orange, anneau double, lueur dorée
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { width: 800px; height: 450px; overflow: hidden; font-family: 'Outfit', system-ui, sans-serif; background: #0F0D09; }

  .card {
    position: relative;
    width: 800px; height: 450px;
    background: linear-gradient(145deg, #1C1C2E 0%, #20180E 55%, #1C1C2E 100%);
    border: 1.5px solid rgba(255,215,0,0.35);
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 0 80px rgba(255,215,0,0.14), 0 0 30px rgba(255,102,0,0.12);
  }

  /* Grain premium */
  .card::before {
    content: "";
    position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    opacity: 0.08;
    pointer-events: none;
  }

  /* Header strip doré */
  .header-strip {
    position: absolute; top: 0; left: 0; right: 0;
    height: 40px;
    background: linear-gradient(90deg, #FFD700, #FF9F1C 50%, #FF6600);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 28px;
    border-radius: 22px 22px 0 0;
  }
  .strip-stars { font-size: 14px; letter-spacing: 6px; color: #1C1C2E; font-weight: 700; }
  .strip-level { font-size: 12px; font-weight: 800; color: #1C1C2E; letter-spacing: 0.12em; text-transform: uppercase; }
  .strip-logo { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 800; color: #1C1C2E; }
  .strip-logo img { height: 20px; }

  .inner { position: relative; z-index: 1; padding: 58px 36px 28px 36px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }

  /* Header content */
  .header { display: flex; align-items: center; gap: 20px; }

  /* Anneau double gold */
  .avatar-ring {
    width: 82px; height: 82px; border-radius: 50%;
    background: linear-gradient(135deg, #FFD700, #FF9F1C, #FF6600);
    padding: 3px; flex-shrink: 0;
  }
  .avatar-inner-ring {
    width: 100%; height: 100%; border-radius: 50%;
    background: #1C1C2E;
    padding: 2px;
  }
  .avatar {
    width: 100%; height: 100%; border-radius: 50%;
    object-fit: cover; display: block;
    background: #2A2A42;
  }
  .avatar-placeholder {
    width: 100%; height: 100%; border-radius: 50%;
    background: #2A2A42;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #FFD700;
  }

  .identity { flex: 1; }
  .name { font-size: 23px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.02em; line-height: 1.1; }
  .github { font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #FF9F1C; margin-top: 3px; }
  .poste { font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 5px; font-weight: 500; }

  /* Points mis en valeur */
  .points-hero { text-align: right; }
  .points-value { font-size: 42px; font-weight: 800; color: #FFFFFF; line-height: 1; letter-spacing: -0.04em; }
  .points-label { font-size: 11px; color: #FF9F1C; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; }

  /* Divider doré */
  .divider { height: 1.5px; background: linear-gradient(90deg, #FFD700 30%, rgba(255,215,0,0.1)); border-radius: 2px; }

  /* Infos bas */
  .info-row { display: flex; align-items: flex-end; justify-content: space-between; }

  /* Badge grade interne */
  .grade-card {
    display: inline-flex; align-items: center; gap: 10px;
    background: rgba(28,28,46,0.80);
    border: 1px solid rgba(255,215,0,0.30);
    border-radius: 10px;
    padding: 8px 14px;
    backdrop-filter: blur(4px);
  }
  .grade-icon { font-size: 16px; }
  .grade-text {}
  .grade-label-small { font-size: 10px; color: rgba(255,255,255,0.40); letter-spacing: 0.08em; text-transform: uppercase; }
  .grade-name { font-size: 15px; font-weight: 700; color: #FFFFFF; }

  .info-left { display: flex; flex-direction: column; gap: 8px; }
  .meta { }
  .since { font-size: 11px; color: rgba(255,255,255,0.45); }
  .matricule { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: rgba(255,215,0,0.40); margin-top: 3px; letter-spacing: 0.04em; }
  .profile-url { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #FF9F1C; margin-top: 4px; }

  .info-right { display: flex; align-items: flex-end; gap: 18px; }

  .qr-wrap {
    width: 72px; height: 72px;
    border: 1.5px solid rgba(255,215,0,0.35);
    border-radius: 8px; overflow: hidden;
    background: #fff; flex-shrink: 0;
  }
  .qr-wrap svg { width: 72px !important; height: 72px !important; }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
</head>
<body>
<div class="card">
  {{-- Header strip doré --}}
  <div class="header-strip">
    <div class="strip-stars">★ ★ ★</div>
    <div class="strip-level">{{ $card->levelName() }}</div>
    <div class="strip-logo">
      <img src="{{ public_path('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
      Laravel CI
    </div>
  </div>

  <div class="inner">
    {{-- Header --}}
    <div class="header">
      <div class="avatar-ring">
        <div class="avatar-inner-ring">
          @if($card->resolvedAvatar())
            <img class="avatar" src="{{ $card->resolvedAvatar() }}" alt="{{ $card->user->name }}" />
          @else
            <div class="avatar-placeholder">{{ mb_strtoupper(mb_substr($card->user->name, 0, 1)) }}</div>
          @endif
        </div>
      </div>
      <div class="identity">
        <div class="name">{{ $card->user->name }}</div>
        <div class="github">@{{ $card->user->github_username }}</div>
        @if($card->poste)
          <div class="poste">{{ $card->poste }}</div>
        @endif
      </div>
      @php $points = $card->user->profile?->points ?? 0; @endphp
      <div class="points-hero">
        <div class="points-value">{{ number_format($points) }}</div>
        <div class="points-label">points</div>
      </div>
    </div>

    <div class="divider"></div>

    {{-- Infos bas --}}
    <div class="info-row">
      <div class="info-left">
        <div class="grade-card">
          <span class="grade-icon">🔧</span>
          <div class="grade-text">
            <div class="grade-label-small">Grade communauté</div>
            <div class="grade-name">{{ $card->gradeName() }}</div>
          </div>
        </div>
        <div class="meta">
          <div class="since">Membre depuis {{ $card->user->created_at->translatedFormat('M Y') }}</div>
          <div class="matricule">{{ $card->user->matricule }}</div>
          <div class="profile-url">laravel.ci/@{{ $card->user->github_username }}</div>
        </div>
      </div>

      <div class="info-right">
        @if($card->qr_code_svg)
          <div class="qr-wrap">{!! $card->qr_code_svg !!}</div>
        @endif
      </div>
    </div>
  </div>
</div>
</body>
</html>
