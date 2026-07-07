{{--  CARTE MEMBRE — Niveau 1 : Initié  --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{width:800px;height:450px;background:#0e1829;font-family:'Outfit',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

.card{
  width:800px;height:450px;position:relative;border-radius:22px;overflow:hidden;
  background:linear-gradient(150deg,#0d1726 0%,#162340 50%,#0d1726 100%);
  box-shadow:0 40px 80px rgba(0,0,0,.7),0 0 0 1px rgba(232,89,12,.18);
}

/* Noise */
.card::before{
  content:'';position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.045'/%3E%3C/svg%3E");
  pointer-events:none;z-index:1;
}
/* Diagonal shimmer */
.card::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(118deg,transparent 38%,rgba(232,89,12,.04) 43%,rgba(255,180,80,.07) 48%,rgba(232,89,12,.04) 53%,transparent 58%);
  pointer-events:none;z-index:2;
}

/* Left accent stripe */
.stripe{position:absolute;left:0;top:0;bottom:0;width:5px;background:linear-gradient(180deg,#e8590c,#ff9f1c 60%,#e8590c);z-index:5;border-radius:22px 0 0 22px}

/* Subtle dot pattern upper-right */
.dots{
  position:absolute;top:0;right:0;width:220px;height:220px;
  background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);
  background-size:18px 18px;
  opacity:.6;pointer-events:none;z-index:3;
}

.inner{position:relative;z-index:10;padding:32px 40px 28px 44px;height:100%;display:flex;flex-direction:column;justify-content:space-between}

/* ── TOP ── */
.top{display:flex;align-items:flex-start;justify-content:space-between}

/* Chip */
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
.brand-name{font-size:11px;font-weight:800;color:rgba(255,255,255,.5);letter-spacing:.22em;text-transform:uppercase;margin-bottom:7px}
.brand-name b{color:#e8590c;font-weight:900}
.level-pill{
  display:inline-block;background:rgba(232,89,12,.16);border:1px solid rgba(232,89,12,.4);
  border-radius:30px;padding:4px 13px;font-size:10px;font-weight:700;color:#ff8c42;
  letter-spacing:.12em;text-transform:uppercase;
}

/* ── MID ── */
.mid{display:flex;align-items:center;gap:20px;justify-content:space-between}

.avatar{
  width:72px;height:72px;border-radius:50%;flex-shrink:0;overflow:hidden;
  border:2.5px solid rgba(232,89,12,.65);
  box-shadow:0 0 0 5px rgba(232,89,12,.1),0 4px 18px rgba(0,0,0,.5);
}
.avatar img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-init{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;
  font-size:26px;font-weight:800;color:#e8590c;
  background:linear-gradient(135deg,#172240,#1e2d4a);
}

.info{flex:1;min-width:0}
.info-name{font-size:21px;font-weight:700;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:5px;text-shadow:0 2px 12px rgba(0,0,0,.6);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.info-gh{font-family:'JetBrains Mono',monospace;font-size:12px;color:#ff8c42;letter-spacing:.02em;margin-bottom:8px}
.info-poste{font-size:11px;color:rgba(255,255,255,.38);margin-bottom:7px;font-style:italic}
.info-grade{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);
  border-radius:6px;padding:4px 10px;
  font-size:10px;font-weight:600;color:rgba(255,255,255,.55);
  letter-spacing:.08em;text-transform:uppercase;
}
.grade-dot{width:5px;height:5px;border-radius:50%;background:#e8590c;flex-shrink:0}

.pts{text-align:right;flex-shrink:0}
.pts-num{font-size:48px;font-weight:900;color:#fff;line-height:1;letter-spacing:-.05em;text-shadow:0 0 32px rgba(232,89,12,.55),0 2px 8px rgba(0,0,0,.6);font-variant-numeric:tabular-nums}
.pts-lbl{font-size:10px;font-weight:700;color:#ff8c42;letter-spacing:.15em;text-transform:uppercase;margin-top:3px}

/* ── MATRICULE ── */
.mat{
  font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:500;
  color:rgba(255,255,255,.18);letter-spacing:.2em;text-transform:uppercase;
  text-shadow:0 1px 0 rgba(255,255,255,.06);
}

/* ── BOTTOM ── */
.bot{display:flex;align-items:flex-end;justify-content:space-between}

.since{}
.since-lbl{font-size:9px;font-weight:600;color:rgba(255,255,255,.28);letter-spacing:.12em;text-transform:uppercase;margin-bottom:3px}
.since-val{font-size:13px;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:3px}
.since-url{font-family:'JetBrains Mono',monospace;font-size:10px;color:rgba(232,89,12,.45)}

.qr{
  width:72px;height:72px;border-radius:8px;overflow:hidden;background:#fff;flex-shrink:0;
  border:1.5px solid rgba(232,89,12,.3);box-shadow:0 4px 16px rgba(0,0,0,.45);
}
.qr svg{width:72px!important;height:72px!important;display:block}
</style>
</head>
<body>
<div class="card">
  <div class="stripe"></div>
  <div class="dots"></div>
  <div class="inner">

    <div class="top">
      <div class="chip"><div class="chip-v"></div></div>
      <div class="brand">
        <div class="brand-name">LARAVEL<b>CI</b></div>
        <div class="level-pill">★ Initié</div>
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
