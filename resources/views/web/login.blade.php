<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion — Laravel CI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <!-- Styles web (contient .auth-wrap, .auth-card, etc.) -->
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}" />
</head>
<body>

  <div class="auth-wrap">

    {{-- ── Panneau gauche : formulaire ── --}}
    <div class="auth-left">
      <div class="auth-card">

        <div class="logo-row">
          <span class="brand-mark">
            <img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
          </span>
          <span style="font-weight:700;font-size:1.3rem;color:var(--navy)">Laravel CI</span>
        </div>

        <h1 class="mb-2" style="font-size:var(--fs-h2)">Bon retour</h1>
        <p class="lead mb-4">Connectez-vous à la communauté Laravel CI.</p>

        @if (session('error'))
          <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('status'))
          <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <a href="{{ route('auth.github.redirect') }}" class="btn-github-lg" id="githubLoginBtn">
          <i class="fa-brands fa-github"></i> Continuer avec GitHub
        </a>

        <p class="mt-3" style="font-size:.8rem;color:var(--muted)">
          En continuant, vous acceptez nos
          <a href="#">Conditions d'utilisation</a> et notre
          <a href="#">Code de conduite</a>.
          Nous accédons uniquement à votre profil GitHub public.
        </p>

        <p class="text-center mt-4 mb-0" style="font-size:.95rem">
          Nouveau sur Laravel CI ?
          <a href="{{ route('about') }}" style="font-weight:600">Rejoindre la communauté</a>
        </p>

        {{-- ── Espace Entreprise ── --}}
        <div style="margin-top:1.75rem; padding-top:1.25rem; border-top:1px solid var(--border,#eef0f4)">
          <p class="mb-2" style="font-size:.82rem; color:var(--muted)">
            <i class="fa-solid fa-building me-1 text-orange"></i>
            <strong>Vous recrutez ?</strong> — Accédez à votre espace entreprise.
          </p>
          <div style="display:flex; gap:.6rem;">
            <a href="{{ route('company.login') }}"
               class="btn btn-ghost btn-sm"
               style="font-size:.82rem; border:1px solid var(--border,#eef0f4); flex:1; text-align:center">
              Se connecter
            </a>
            <a href="{{ route('company.register') }}"
               class="btn btn-sm"
               style="background:var(--orange,#e8590c); color:#fff; font-size:.82rem; border-radius:2rem; flex:1; text-align:center">
              Faire une demande
            </a>
          </div>
        </div>

      </div>
    </div>

    {{-- ── Panneau droit : marketing ── --}}
    <div class="auth-right">
      <div class="inner">
        <div class="mascot-circle">
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
        </div>
        <h2 style="color:#fff;font-size:var(--fs-h2)">Construis avec la communauté Laravel ivoirienne</h2>
        <p style="color:rgba(255,255,255,.9);font-size:1.05rem">
          500+ développeurs partageant connaissances, opportunités et amitié — à Abidjan et dans la diaspora.
        </p>
        <div class="auth-stats">
          <div><div class="n">500+</div><div class="l">Membres</div></div>
          <div><div class="n">1.2k+</div><div class="l">Questions</div></div>
          <div><div class="n">24+</div><div class="l">Événements</div></div>
        </div>
        <div class="testimonial-card">
          <p class="mb-3" style="font-size:.98rem">
            « Laravel CI m'a permis de trouver mon mentor, mon premier emploi remote et une communauté qui me soutient vraiment. C'est ce dont on avait besoin. »
          </p>
          <div class="author-row">
            <span class="avatar av-2" style="border:2px solid rgba(255,255,255,.4)">YT</span>
            <div class="meta">
              <div class="name" style="color:#fff">Yao Térence</div>
              <div class="sub" style="color:rgba(255,255,255,.8)">Développeur backend, Abidjan</div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/web/js/main.js') }}"></script>

  @livewireScripts

</body>
</html>
