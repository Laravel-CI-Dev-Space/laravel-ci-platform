{{--  CARTE MEMBRE — Niveau 2 : Bâtisseur  --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{width:800px;height:450px;background:#111117;font-family:'Outfit',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

.card{
  width:800px;height:450px;position:relative;border-radius:22px;overflow:hidden;
  background:linear-gradient(140deg,#111117 0%,#1a1a2e 45%,#111117 100%);
  box-shadow:0 40px 80px rgba(0,0,0,.75),0 0 0 1px rgba(255,107,53,.14);
}

/* Noise */
.card::before{
  content:'';position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.05'/%3E%3C/svg%3E");
  pointer-events:none;z-index:1;
}
/* Purple-to-coral shimmer */
.card::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(125deg,transparent 30%,rgba(120,60,200,.04) 38%,rgba(232,89,12,.06) 48%,rgba(120,60,200,.04) 58%,transparent 66%);
  pointer-events:none;z-index:2;
}

/* Glow orb top-right */
.orb{
  position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;
  background:radial-gradient(circle,rgba(232,89,12,.14) 0%,transparent 70%);
  pointer-events:none;z-index:3;
}
/* Glow orb bottom-left */
.orb2{
  position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;
  background:radial-gradient(circle,rgba(100,40,180,.1) 0%,transparent 70%);
  pointer-events:none;z-index:3;
}

/* Top orange bar */
.topbar{
  position:absolute;top:0;left:0;right:0;height:4px;
  background:linear-gradient(90deg,transparent 0%,#e8590c 30%,#ff9f1c 60%,#e8590c 80%,transparent 100%);
  z-index:5;
}

.inner{position:relative;z-index:10;padding:30px 40px 28px 40px;height:100%;display:flex;flex-direction:column;justify-content:space-between}

/* ── TOP ── */
.top{display:flex;align-items:flex-start;justify-content:space-between}

.chip{
  width:52px;height:40px;border-radius:6px;
  background:linear-gradient(145deg,#b8891a 0%,#e8c050 22%,#d4941e 42%,#f9e090 62%,#c9a227 82%,#f5d76e 100%);
  position:relative;flex-shrink:0;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.45),inset 0 -1px 0 rgba(0,0,0,.3),0 3px 8px rgba(0,0,0,.5);
}
.chip::before{content:'';position:absolute;inset:6px;border:1.5px solid rgba(140,90,8,.55);border-radius:3px}
.chip::after{content:'';position:absolute;top:50%;left:6px;right:6px;height:1.5px;background:rgba(140,90,8,.4);transform:translateY(-50%)}
.chip-v{position:absolute;left:50%;top:6px;bottom:6px;width:1.5px;background:rgba(140,90,8,.4);transform:translateX(-50%)}

.brand{text-align:right}
.brand-name{font-size:11px;font-weight:800;color:rgba(255,255,255,.45);letter-spacing:.22em;text-transform:uppercase;margin-bottom:7px}
.brand-name b{color:#ff8c42;font-weight:900}
.level-pill{
  display:inline-block;
  background:linear-gradient(135deg,rgba(232,89,12,.22),rgba(100,40,180,.18));
  border:1px solid rgba(232,89,12,.38);
  border-radius:30px;padding:4px 14px;font-size:10px;font-weight:700;
  color:#ff8c42;letter-spacing:.12em;text-transform:uppercase;
}

/* ── MID ── */
.mid{display:flex;align-items:center;gap:20px;justify-content:space-between}

.avatar{
  width:72px;height:72px;border-radius:50%;flex-shrink:0;overflow:hidden;
  border:2.5px solid rgba(232,89,12,.55);
  box-shadow:0 0 0 5px rgba(232,89,12,.08),0 0 20px rgba(232,89,12,.2),0 4px 18px rgba(0,0,0,.6);
}
.avatar img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-init{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;
  font-size:26px;font-weight:800;color:#ff8c42;
  background:linear-gradient(135deg,#1a1a2e,#222236);
}

.info{flex:1;min-width:0}
.info-name{font-size:21px;font-weight:700;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:5px;text-shadow:0 2px 14px rgba(0,0,0,.7);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.info-gh{font-family:'JetBrains Mono',monospace;font-size:12px;color:#ff8c42;letter-spacing:.02em;margin-bottom:8px}
.info-poste{font-size:11px;color:rgba(255,255,255,.35);margin-bottom:7px;font-style:italic}
.info-grade{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.09);
  border-radius:6px;padding:4px 10px;
  font-size:10px;font-weight:600;color:rgba(255,255,255,.5);
  letter-spacing:.08em;text-transform:uppercase;
}
.grade-dot{width:5px;height:5px;border-radius:50%;background:linear-gradient(135deg,#e8590c,#9b3fcc);flex-shrink:0}

.pts{text-align:right;flex-shrink:0}
.pts-num{
  font-size:48px;font-weight:900;color:#fff;line-height:1;letter-spacing:-.05em;
  font-variant-numeric:tabular-nums;
  text-shadow:0 0 35px rgba(232,89,12,.6),0 0 60px rgba(232,89,12,.25),0 2px 8px rgba(0,0,0,.7);
}
.pts-lbl{font-size:10px;font-weight:700;color:#ff8c42;letter-spacing:.15em;text-transform:uppercase;margin-top:3px}

/* ── DIVIDER ── */
.divider{height:1px;background:linear-gradient(90deg,transparent,rgba(232,89,12,.35) 30%,rgba(100,40,180,.3) 70%,transparent)}

/* ── MATRICULE ── */
.mat{
  font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:500;
  color:rgba(255,255,255,.16);letter-spacing:.2em;text-transform:uppercase;
}

/* ── BOTTOM ── */
.bot{display:flex;align-items:flex-end;justify-content:space-between}

.since{}
.since-lbl{font-size:9px;font-weight:600;color:rgba(255,255,255,.25);letter-spacing:.12em;text-transform:uppercase;margin-bottom:3px}
.since-val{font-size:13px;font-weight:600;color:rgba(255,255,255,.45);margin-bottom:3px}
.since-url{font-family:'JetBrains Mono',monospace;font-size:10px;color:rgba(232,89,12,.4)}

.qr{
  width:72px;height:72px;border-radius:8px;overflow:hidden;background:#fff;flex-shrink:0;
  border:1.5px solid rgba(232,89,12,.28);
  box-shadow:0 4px 16px rgba(0,0,0,.5),0 0 12px rgba(232,89,12,.1);
}
.qr svg{width:72px!important;height:72px!important;display:block}
</style>
</head>
<body>
<div class="card">
  <div class="topbar"></div>
  <div class="orb"></div>
  <div class="orb2"></div>
  <div class="inner">

    <div class="top">
      <div class="chip"><div class="chip-v"></div></div>
      <div class="brand">
        <div class="brand-name">LARAVEL<b>CI</b></div>
        <div class="level-pill">★★ Bâtisseur</div>
      </div>
    </div>

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

    <div class="divider"></div>
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
