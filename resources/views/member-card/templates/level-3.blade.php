{{--  CARTE MEMBRE — Niveau 3 : Maître Artisan  --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{width:800px;height:450px;background:#0a0807;font-family:'Outfit',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

.card{
  width:800px;height:450px;position:relative;border-radius:22px;overflow:hidden;
  background:linear-gradient(150deg,#0a0807 0%,#160e07 35%,#1a1209 65%,#0a0807 100%);
  box-shadow:
    0 40px 80px rgba(0,0,0,.9),
    0 0 0 1.5px rgba(212,175,55,.28),
    inset 0 1px 0 rgba(255,215,0,.06);
}

/* Noise */
.card::before{
  content:'';position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.65' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
  pointer-events:none;z-index:1;
}
/* Gold shimmer sweep */
.card::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(118deg,transparent 20%,rgba(212,175,55,.03) 30%,rgba(255,223,0,.07) 42%,rgba(255,159,28,.05) 52%,rgba(212,175,55,.03) 62%,transparent 72%);
  pointer-events:none;z-index:2;
}

/* Gold top border */
.topbar{
  position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,transparent 0%,#b8891a 10%,#e8c050 30%,#ffd700 50%,#ff9f1c 70%,#d4941e 85%,transparent 100%);
  z-index:5;
}
/* Gold bottom border */
.botbar{
  position:absolute;bottom:0;left:0;right:0;height:1.5px;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,.35) 40%,rgba(212,175,55,.35) 60%,transparent);
  z-index:5;
}

/* Radial gold aura center */
.aura{
  position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  width:500px;height:300px;
  background:radial-gradient(ellipse,rgba(212,175,55,.06) 0%,transparent 70%);
  pointer-events:none;z-index:3;
}
/* Corner orb top-right */
.orb{
  position:absolute;top:-60px;right:-60px;width:250px;height:250px;border-radius:50%;
  background:radial-gradient(circle,rgba(255,159,28,.1) 0%,transparent 68%);
  pointer-events:none;z-index:3;
}
/* Corner orb bottom-left */
.orb2{
  position:absolute;bottom:-50px;left:-50px;width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(212,175,55,.07) 0%,transparent 68%);
  pointer-events:none;z-index:3;
}

/* Fine dot grid */
.dots{
  position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(212,175,55,.07) 1px,transparent 1px);
  background-size:22px 22px;
  pointer-events:none;z-index:4;
}

.inner{position:relative;z-index:10;padding:28px 38px 26px 38px;height:100%;display:flex;flex-direction:column;justify-content:space-between}

/* ── TOP ── */
.top{display:flex;align-items:flex-start;justify-content:space-between}

.chip{
  width:56px;height:44px;border-radius:7px;position:relative;flex-shrink:0;
  background:linear-gradient(145deg,#a07010 0%,#d4af37 18%,#f5d76e 32%,#b8891a 48%,#ffd700 62%,#c9a227 78%,#ffe066 92%,#b8891a 100%);
  box-shadow:
    inset 0 1.5px 0 rgba(255,255,255,.55),
    inset 0 -1.5px 0 rgba(0,0,0,.35),
    0 4px 14px rgba(0,0,0,.7),
    0 0 0 .5px rgba(212,175,55,.4);
}
.chip::before{content:'';position:absolute;inset:7px;border:1.5px solid rgba(130,80,5,.6);border-radius:4px}
.chip::after{content:'';position:absolute;top:50%;left:7px;right:7px;height:1.5px;background:rgba(130,80,5,.45);transform:translateY(-50%)}
.chip-v{position:absolute;left:50%;top:7px;bottom:7px;width:1.5px;background:rgba(130,80,5,.45);transform:translateX(-50%)}

.brand{text-align:right}
.brand-name{
  font-size:11px;font-weight:800;letter-spacing:.24em;text-transform:uppercase;margin-bottom:8px;
  background:linear-gradient(135deg,rgba(255,255,255,.4) 0%,#ffd700 50%,rgba(255,255,255,.35) 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.brand-name b{
  font-weight:900;
  background:linear-gradient(135deg,#ffd700,#ff9f1c);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.level-pill{
  display:inline-block;
  background:linear-gradient(135deg,rgba(212,175,55,.2),rgba(255,159,28,.14));
  border:1px solid rgba(212,175,55,.5);
  border-radius:30px;padding:4px 16px;font-size:10px;font-weight:700;
  letter-spacing:.12em;text-transform:uppercase;
  color:#ffd700;text-shadow:0 0 14px rgba(255,215,0,.4);
}

/* ── DIVIDER TOP ── */
.divider-top{
  height:1px;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,.3) 25%,rgba(212,175,55,.18) 75%,transparent);
}

/* ── MID ── */
.mid{display:flex;align-items:center;gap:22px;justify-content:space-between}

.avatar{
  width:76px;height:76px;border-radius:50%;flex-shrink:0;overflow:hidden;
  border:2.5px solid rgba(212,175,55,.6);
  box-shadow:
    0 0 0 5px rgba(212,175,55,.07),
    0 0 28px rgba(212,175,55,.28),
    0 0 60px rgba(255,159,28,.1),
    0 6px 20px rgba(0,0,0,.7);
}
.avatar img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-init{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;
  font-size:28px;font-weight:800;
  background:linear-gradient(135deg,#160e07,#211407);
  color:#ffd700;
}

.info{flex:1;min-width:0}
.info-name{
  font-size:22px;font-weight:700;line-height:1.1;letter-spacing:-.02em;margin-bottom:5px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  background:linear-gradient(135deg,#fff 0%,rgba(255,215,0,.85) 60%,#fff 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.info-gh{
  font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:.02em;margin-bottom:8px;
  color:#ffd700;text-shadow:0 0 10px rgba(255,215,0,.35);
}
.info-poste{font-size:11px;color:rgba(255,255,255,.3);margin-bottom:7px;font-style:italic}
.info-grade{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(212,175,55,.07);border:1px solid rgba(212,175,55,.2);
  border-radius:6px;padding:4px 10px;
  font-size:10px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,215,0,.55);
}
.grade-dot{
  width:5px;height:5px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,#ffd700,#ff9f1c);
  box-shadow:0 0 6px rgba(255,215,0,.6);
}

.pts{text-align:right;flex-shrink:0}
.pts-num{
  font-size:52px;font-weight:900;line-height:1;letter-spacing:-.05em;
  font-variant-numeric:tabular-nums;
  background:linear-gradient(170deg,#fff 0%,#ffd700 40%,#ff9f1c 75%,#ffd700 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  filter:drop-shadow(0 0 18px rgba(255,215,0,.45)) drop-shadow(0 2px 6px rgba(0,0,0,.6));
}
.pts-lbl{
  font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-top:3px;
  color:#ffd700;text-shadow:0 0 12px rgba(255,215,0,.35);
}

/* ── DIVIDER BOTTOM ── */
.divider-bot{
  height:1px;
  background:linear-gradient(90deg,transparent,rgba(212,175,55,.25) 20%,rgba(212,175,55,.18) 80%,transparent);
}

/* ── MATRICULE ── */
.mat{
  font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:500;
  color:rgba(212,175,55,.18);letter-spacing:.22em;text-transform:uppercase;
}

/* ── BOTTOM ── */
.bot{display:flex;align-items:flex-end;justify-content:space-between}

.since-lbl{font-size:9px;font-weight:600;color:rgba(212,175,55,.32);letter-spacing:.12em;text-transform:uppercase;margin-bottom:3px}
.since-val{font-size:13px;font-weight:600;color:rgba(255,255,255,.42);margin-bottom:3px}
.since-url{font-family:'JetBrains Mono',monospace;font-size:10px;color:rgba(255,215,0,.35)}

.qr{
  width:72px;height:72px;border-radius:8px;overflow:hidden;background:#fff;flex-shrink:0;
  border:1.5px solid rgba(212,175,55,.4);
  box-shadow:0 4px 16px rgba(0,0,0,.6),0 0 16px rgba(212,175,55,.15);
}
.qr svg{width:72px!important;height:72px!important;display:block}
</style>
</head>
<body>
<div class="card">
  <div class="topbar"></div>
  <div class="botbar"></div>
  <div class="aura"></div>
  <div class="orb"></div>
  <div class="orb2"></div>
  <div class="dots"></div>
  <div class="inner">

    <div class="top">
      <div class="chip"><div class="chip-v"></div></div>
      <div class="brand">
        <div class="brand-name">LARAVEL<b>CI</b></div>
        <div class="level-pill">★★★ Maître Artisan</div>
      </div>
    </div>

    <div class="divider-top"></div>

    <div class="mid">
      <div class="avatar">
        @if($card->resolvedAvatar())
          <img src="{{ $card->resolvedAvatar() }}" alt="{{ $card->user->name }}"/>
        @else
          <div class="avatar-init">{{ mb_strtoupper(mb_substr($card->user->name, 0, 1)) }}</div>
        @endif
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

    <div class="divider-bot"></div>
    <div class="mat">{{ $card->user->matricule ?? 'LARAVELCI-••-••-••••-••••' }}</div>

    <div class="bot">
      <div class="since">
        <div class="since-lbl">Membre depuis</div>
        <div class="since-val">{{ $card->user->created_at->translatedFormat('F Y') }}</div>
        <div class="since-url">laravel.ci/{{ $card->user->github_username }}</div>
      </div>
      @if($card->qr_code_svg)
        <div class="qr">{!! $card->qr_code_svg !!}</div>
      @endif
    </div>

  </div>
</div>
</body>
</html>
