<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion — Laravel CI</title>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="icon" href="{{ asset('assets/web/img/mascot.png') }}" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    * { font-family: 'Outfit', system-ui, sans-serif; }
    body { background: #f8f9fa; }

    /* ── Panneau droit ── */
    .auth-panel-right {
      background: linear-gradient(145deg, #e8590c 0%, #c44508 60%, #a33a06 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 2rem;
    }

    /* ── Panneau gauche ── */
    .auth-panel-left {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.5rem;
      background: #fff;
    }

    /* ── Card form ── */
    .auth-form-box { width: 100%; max-width: 420px; }
    .brand-logo { display: flex; align-items: center; gap: .6rem; margin-bottom: 2rem; text-decoration: none; }
    .brand-logo img { width: 40px; height: 40px; border-radius: 10px; }
    .brand-logo span { font-weight: 800; font-size: 1.25rem; color: #0f1b35; }

    /* ── Bouton GitHub ── */
    .btn-github {
      display: flex; align-items: center; justify-content: center; gap: .75rem;
      width: 100%; padding: .85rem 1.5rem;
      background: #24292e; color: #fff;
      border: none; border-radius: 12px;
      font-size: 1rem; font-weight: 600; text-decoration: none;
      transition: all .2s; letter-spacing: .01em;
    }
    .btn-github:hover { background: #000; color: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.2); }
    .btn-github i { font-size: 1.2rem; }

    /* ── Separateur entreprise ── */
    .company-strip {
      border-top: 1px solid #eef0f4;
      padding-top: 1.25rem; margin-top: 1.75rem;
    }

    /* ── Panneau droit — contenu ── */
    .right-inner { max-width: 440px; text-align: center; }
    .mascot-wrap img { width: 150px; height: 150px; border-radius: 50%; border: 4px solid rgba(255,255,255,.3); object-fit: contain; background: rgba(255,255,255,.15); padding: 1rem; margin-bottom: 2rem; }
    .right-inner h2 { color: #fff; font-size: 1.9rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem; }
    .right-inner p { color: rgba(255,255,255,.88); font-size: 1rem; line-height: 1.65; }
    .auth-stats { display: flex; gap: 2rem; justify-content: center; margin: 2rem 0; }
    .auth-stats .n { font-size: 2rem; font-weight: 800; color: #fff; line-height: 1; }
    .auth-stats .l { font-size: .78rem; color: rgba(255,255,255,.75); margin-top: .2rem; text-transform: uppercase; letter-spacing: .08em; }
    .testimonial-box { background: rgba(255,255,255,.12); border-radius: 16px; padding: 1.5rem; margin-top: .5rem; text-align: left; backdrop-filter: blur(8px); }
    .testimonial-box p { color: rgba(255,255,255,.92); font-size: .92rem; font-style: italic; line-height: 1.6; margin-bottom: 1rem; }
    .testi-author { display: flex; align-items: center; gap: .75rem; }
    .testi-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: .9rem; flex-shrink: 0; border: 2px solid rgba(255,255,255,.35); }
    .testi-name { color: #fff; font-weight: 600; font-size: .88rem; }
    .testi-role { color: rgba(255,255,255,.7); font-size: .78rem; }

    /* ── Bandeau mascotte (mobile uniquement, occupe le haut de l'écran) ── */
    .mobile-mascot-banner {
      background: linear-gradient(145deg, rgba(232,89,12,.78) 0%, rgba(196,69,8,.78) 60%, rgba(163,58,6,.78) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 44vh;
      padding: 2rem;
      overflow: hidden;
    }
    .mascot-stage { position: relative; width: 290px; height: 240px; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
    .mascot-stage img {
      width: 200px; height: 200px;
      object-fit: contain;
      filter: drop-shadow(0 18px 36px rgba(0,0,0,.3));
      animation: mascot-float 3.5s ease-in-out infinite;
      position: relative;
      z-index: 1;
    }
    .code-chip {
      position: absolute;
      background: rgba(255,255,255,.16);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      border: 1px solid rgba(255,255,255,.3);
      color: #fff;
      font-family: 'Courier New', monospace;
      font-size: .66rem;
      font-weight: 600;
      letter-spacing: .01em;
      padding: .35rem .6rem;
      border-radius: .5rem;
      white-space: nowrap;
      box-shadow: 0 8px 20px rgba(0,0,0,.15);
      animation: chip-float 4s ease-in-out infinite;
    }
    .code-chip i { margin-right: .3rem; opacity: .8; }
    .chip-1 { top: 0;    left: 0;    animation-delay: 0s; }
    .chip-2 { top: 42%;  right: -6px; animation-delay: 1.3s; }
    .chip-3 { bottom: 4px; left: 6px; animation-delay: 2.6s; }
    @keyframes mascot-float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }
    @keyframes chip-float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-9px); }
    }

    @media (max-width: 991.98px) {
      .auth-panel-left {
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        min-height: 100vh;
        padding: 0;
      }
      .auth-form-box { padding: 2rem 1.25rem; margin: 0 auto; }
      body { background: #fff; }
    }
  </style>
</head>
<body>

<div class="container-fluid p-0">
  <div class="row g-0">

    {{-- ─── Panneau gauche : formulaire ─── --}}
    <div class="col-12 col-lg-5 auth-panel-left">
      {{-- Bandeau mascotte (mobile uniquement) --}}
      <div class="mobile-mascot-banner d-lg-none">
        <div class="mascot-stage">
          <span class="code-chip chip-1"><i class="fa-solid fa-terminal"></i>artisan serve</span>
          <span class="code-chip chip-2"><i class="fa-brands fa-github"></i>git push</span>
          <span class="code-chip chip-3"><i class="fa-solid fa-box"></i>composer require</span>
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
        </div>
      </div>

      <div class="auth-form-box">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="brand-logo">
          <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI" />
          <span>Laravel CI</span>
        </a>

        <h1 style="font-size:1.8rem; font-weight:800; color:#0f1b35; margin-bottom:.5rem">Bon retour 👋</h1>
        <p style="color:#6c757d; margin-bottom:2rem; font-size:.95rem">Connectez-vous à la communauté Laravel CI.</p>

        @if (session('error'))
          <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
        @endif
        @if (session('status'))
          <div class="alert alert-success rounded-3 mb-3">{{ session('status') }}</div>
        @endif
        @if (session('warning'))
          <div class="alert alert-warning rounded-3 mb-3">{{ session('warning') }}</div>
        @endif

        {{-- Bouton GitHub --}}
        <a href="{{ route('auth.github.redirect') }}" class="btn-github mb-3" id="githubLoginBtn">
          <i class="fa-brands fa-github"></i>
          Continuer avec GitHub
        </a>

        <p style="font-size:.78rem; color:#adb5bd; text-align:center; margin-bottom:1.5rem;">
          Accès uniquement via GitHub OAuth. Nous n'accédons qu'à votre profil public.
        </p>

        <p style="text-align:center; font-size:.9rem; color:#6c757d; margin-bottom:0">
          Nouveau sur Laravel CI ?
          <a href="{{ route('about') }}" style="color:#e8590c; font-weight:600; text-decoration:none">
            Rejoindre la communauté
          </a>
        </p>

        {{-- Section entreprise --}}
        <div class="company-strip">
          <p style="font-size:.82rem; color:#6c757d; margin-bottom:.85rem;">
            <i class="fa-solid fa-building me-1" style="color:#e8590c"></i>
            <strong style="color:#0f1b35">Vous recrutez ?</strong>
            Accédez à votre espace entreprise ou faites une demande.
          </p>
          <div class="d-flex gap-2">
            <a href="{{ route('company.login') }}"
               style="flex:1; text-align:center; padding:.6rem; border:1.5px solid #dee2e6; border-radius:10px; font-size:.82rem; font-weight:600; color:#0f1b35; text-decoration:none; transition:.15s"
               onmouseover="this.style.borderColor='#e8590c';this.style.color='#e8590c'"
               onmouseout="this.style.borderColor='#dee2e6';this.style.color='#0f1b35'">
              Se connecter
            </a>
            <a href="{{ route('company.register') }}"
               style="flex:1; text-align:center; padding:.6rem; background:#e8590c; border:1.5px solid #e8590c; border-radius:10px; font-size:.82rem; font-weight:600; color:#fff; text-decoration:none; transition:.15s"
               onmouseover="this.style.background='#c44508'"
               onmouseout="this.style.background='#e8590c'">
              Faire une demande
            </a>
          </div>
        </div>

      </div>
    </div>

    {{-- ─── Panneau droit : marketing (desktop uniquement) ─── --}}
    <div class="col-lg-7 d-none d-lg-flex auth-panel-right">
      <div class="right-inner">

        <div class="mascot-wrap">
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
        </div>

        <h2>Construis avec la communauté Laravel ivoirienne</h2>
        <p>500+ développeurs partageant connaissances, opportunités et amitié — à Abidjan et dans la diaspora.</p>

        <div class="auth-stats">
          <div><div class="n">500+</div><div class="l">Membres</div></div>
          <div><div class="n">1.2k+</div><div class="l">Questions</div></div>
          <div><div class="n">24+</div><div class="l">Événements</div></div>
        </div>

        <div class="testimonial-box">
          <p>« Laravel CI m'a permis de trouver mon mentor, mon premier emploi remote et une communauté qui me soutient vraiment. C'est ce dont on avait besoin. »</p>
          <div class="testi-author">
            <div class="testi-avatar">YT</div>
            <div>
              <div class="testi-name">Yao Térence</div>
              <div class="testi-role">Développeur backend, Abidjan</div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
</body>
</html>
