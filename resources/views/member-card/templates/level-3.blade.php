{{--  CARTE MEMBRE — Niveau 3 : Maître Artisan  --}}
@php
  $logoData   = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/web/img/logo.png')));
  $mascotData = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/web/img/mascot.png')));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{width:800px;height:450px;background:#b84208;font-family:'Outfit',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

/* Orange → Vert — combinaison premium */
.card{
  width:800px;height:450px;position:relative;border-radius:22px;overflow:hidden;
  background:linear-gradient(140deg,#b84208 0%,#e8590c 25%,#e8590c 45%,#16a34a 70%,#0a6632 100%);
  box-shadow:0 40px 80px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.2);
}
.card::before{content:'';position:absolute;inset:0;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.65' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");pointer-events:none;z-index:1}
.card::after{content:'';position:absolute;inset:0;background:linear-gradient(118deg,transparent 22%,rgba(255,255,255,.03) 30%,rgba(255,255,255,.12) 40%,rgba(255,255,255,.05) 50%,rgba(255,255,255,.12) 60%,rgba(255,255,255,.03) 70%,transparent 78%);pointer-events:none;z-index:2}

.topbar{position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.55) 30%,rgba(255,255,255,.9) 50%,rgba(255,255,255,.55) 70%,transparent);z-index:5}
.botbar{position:absolute;bottom:0;left:0;right:0;height:1.5px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25) 40%,rgba(255,255,255,.25) 60%,transparent);z-index:5}
.dots{position:absolute;top:0;right:0;width:220px;height:220px;background-image:radial-gradient(circle,rgba(255,255,255,.14) 1px,transparent 1px);background-size:18px 18px;pointer-events:none;z-index:3}

/* Mascotte ancrée bas-droite — légèrement plus grande pour le niveau premium */
.mascot{position:absolute;bottom:-8px;right:18px;height:215px;width:auto;object-fit:contain;z-index:6;filter:drop-shadow(0 10px 26px rgba(0,0,0,.5)) drop-shadow(0 0 1.5px rgba(255,255,255,.25));pointer-events:none}

.inner{position:relative;z-index:10;padding:30px 40px 24px 40px;height:100%;display:flex;flex-direction:column;justify-content:space-between}

/* ── TOP ── */
.top{display:flex;align-items:flex-start;justify-content:space-between}

/* QR stylisé — double ring orange + vert */
.qr-chip{position:relative;width:58px;height:58px;border-radius:9px;background:#fff;overflow:hidden;flex-shrink:0;box-shadow:0 0 0 2.5px rgba(255,255,255,.95),0 0 0 5px rgba(232,89,12,.5),0 0 0 7px rgba(22,163,74,.3),0 5px 16px rgba(0,0,0,.4)}
.qr-chip svg{width:58px!important;height:58px!important;display:block}
.qr-chip-empty{width:100%;height:100%;background:repeating-linear-gradient(0deg,#e8590c 0,#e8590c 3px,transparent 3px,transparent 8px),repeating-linear-gradient(90deg,#16a34a 0,#16a34a 3px,transparent 3px,transparent 8px);opacity:.2}

/* Logo + pill */
.brand{text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px}
.brand-logo-wrap{background:#fff;border-radius:8px;padding:5px 8px;display:inline-flex;align-items:center}
.brand-logo{height:34px;width:auto;display:block}
.level-pill{display:inline-block;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.45);border-radius:30px;padding:4px 13px;font-size:10px;font-weight:700;color:#fff;letter-spacing:.12em;text-transform:uppercase}

/* ── MID ── */
.mid{display:flex;align-items:center;gap:18px;padding-right:225px}

.avatar-wrap{flex-shrink:0}
.avatar{width:78px;height:78px;border-radius:50%;overflow:hidden;border:2.5px solid rgba(255,255,255,.75);box-shadow:0 0 0 5px rgba(255,255,255,.12),0 4px 18px rgba(0,0,0,.3),0 0 24px rgba(255,255,255,.15)}
.avatar img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-init{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#e8590c;background:rgba(255,255,255,.95)}

.info{flex:1;min-width:0}
.info-name{font-size:20px;font-weight:700;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:5px;text-shadow:0 2px 10px rgba(0,0,0,.25);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.info-gh{font-family:'JetBrains Mono',monospace;font-size:12px;color:rgba(255,255,255,.8);letter-spacing:.02em;margin-bottom:8px}
.info-poste{font-size:11px;color:rgba(255,255,255,.65);margin-bottom:7px;font-style:italic}
.info-grade{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:6px;padding:4px 10px;font-size:10px;font-weight:600;color:rgba(255,255,255,.92);letter-spacing:.08em;text-transform:uppercase}
.grade-dot{width:5px;height:5px;border-radius:50%;background:#fff;flex-shrink:0}

.pts{text-align:right;flex-shrink:0}
.pts-num{font-size:46px;font-weight:900;color:#fff;line-height:1;letter-spacing:-.05em;text-shadow:0 2px 24px rgba(0,0,0,.35),0 0 50px rgba(255,255,255,.25);font-variant-numeric:tabular-nums}
.pts-lbl{font-size:10px;font-weight:700;color:rgba(255,255,255,.7);letter-spacing:.15em;text-transform:uppercase;margin-top:3px}

/* ── DIVIDER ── */
.divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.3) 30%,rgba(255,255,255,.3) 70%,transparent)}

/* ── MATRICULE ── */
.mat{font-family:'JetBrains Mono',monospace;font-size:14px;font-weight:500;color:rgba(255,255,255,.28);letter-spacing:.2em;text-transform:uppercase}

/* ── BOTTOM ── */
.bot{display:flex;align-items:flex-end;padding-right:230px}
.since-lbl{font-size:9px;font-weight:600;color:rgba(255,255,255,.55);letter-spacing:.12em;text-transform:uppercase;margin-bottom:3px}
.since-val{font-size:13px;font-weight:600;color:rgba(255,255,255,.88);margin-bottom:3px}
.since-url{font-family:'JetBrains Mono',monospace;font-size:10px;color:rgba(255,255,255,.6)}
</style>
</head>
<body>
<div class="card">
  <div class="topbar"></div>
  <div class="botbar"></div>
  <div class="dots"></div>
  <img class="mascot" src="{{ $mascotData }}" alt=""/>
  <div class="inner">

    <div class="top">
      <div class="qr-chip">
        @if($card->qr_code_svg)
          {!! $card->qr_code_svg !!}
        @else
          <div class="qr-chip-empty"></div>
        @endif
      </div>
      <div class="brand">
        <div class="brand-logo-wrap">
          <img class="brand-logo" src="{{ $logoData }}" alt="Laravel CI"/>
        </div>
        <div class="level-pill">★★★ Maître Artisan</div>
      </div>
    </div>

    <div class="mid">
      <div class="avatar-wrap">
        <div class="avatar">
          @if($card->resolvedAvatar())
            <img src="{{ $card->resolvedAvatar() }}" alt="{{ $card->user->name }}"/>
          @else
            <div class="avatar-init">{{ mb_strtoupper(mb_substr($card->user->name, 0, 1)) }}</div>
          @endif
        </div>
      </div>
      <div class="info">
        <div class="info-name">{{ $card->user->name }}</div>
        <div class="info-gh">{{ '@' . $card->user->github_username }}</div>
        @if($card->poste)<div class="info-poste">{{ $card->poste }}</div>@endif
        <div class="info-grade"><span class="grade-dot"></span>{{ $card->gradeName() }}</div>
      </div>
      @php $points = $card->user->profile?->points ?? 0; @endphp
      <div class="pts">
        <div class="pts-num">{{ number_format($points) }}</div>
        <div class="pts-lbl">points</div>
      </div>
    </div>

    <div class="divider"></div>
    <div class="mat">{{ $card->user->matricule ?? 'LARAVELCI-••-••-••••-••••' }}</div>

    <div class="bot">
      <div class="since">
        <div class="since-lbl">Membre depuis</div>
        <div class="since-val">{{ $card->user->created_at->translatedFormat('F Y') }}</div>
        <div class="since-url">laravel.ci/{{ $card->user->github_username }}</div>
      </div>
    </div>

  </div>
</div>
</body>
</html>
