<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Définir votre mot de passe — Laravel CI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}" />
</head>
<body>

  <div class="auth-wrap">

    {{-- ── Panneau gauche ── --}}
    <div class="auth-left">
      <div class="auth-card">

        <div class="logo-row">
          <span class="brand-mark">
            <img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
          </span>
          <span style="font-weight:700;font-size:1.3rem;color:var(--navy)">Laravel CI</span>
        </div>

        {{-- Icône verrou --}}
        <div class="text-center mb-3">
          <span style="font-size:2.5rem; color:var(--orange,#e8590c)">
            <i class="fa-solid fa-lock"></i>
          </span>
        </div>

        <h1 class="mb-2" style="font-size:1.6rem">Définir votre mot de passe</h1>
        <p class="lead mb-4" style="font-size:.95rem">
          Un mot de passe temporaire vous a été attribué lors de la validation de votre compte.
          Veuillez le remplacer avant de continuer.
        </p>

        @if (session('warning'))
          <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('company.password.update') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">
              Nouveau mot de passe <span class="text-danger">*</span>
            </label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimum 10 caractères"
                   autocomplete="new-password" />
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">
              Confirmer le mot de passe <span class="text-danger">*</span>
            </label>
            <input type="password" name="password_confirmation"
                   class="form-control"
                   placeholder="Répétez le mot de passe"
                   autocomplete="new-password" />
          </div>

          <div class="alert" style="background:#fff5f0; border:1px solid #fde8d8; border-radius:var(--radius,.75rem); font-size:.85rem; color:#9a3412; padding:.85rem 1rem; margin-bottom:1.25rem;">
            <i class="fa-solid fa-circle-info me-1"></i>
            Choisissez un mot de passe fort d'au moins 10 caractères. Ne réutilisez pas le mot de passe temporaire.
          </div>

          <button type="submit" class="btn btn-brand w-100" style="min-height:48px;">
            <i class="fa-solid fa-key me-1"></i>Enregistrer mon mot de passe
          </button>
        </form>

        <div class="text-center mt-4">
          <form method="POST" action="{{ route('company.logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm" style="font-size:.8rem; color:var(--muted)">
              <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>Se déconnecter
            </button>
          </form>
        </div>

      </div>
    </div>

    {{-- ── Panneau droit ── --}}
    <div class="auth-right">
      <div class="inner">
        <div class="mascot-circle">
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
        </div>
        <h2 style="color:#fff;font-size:var(--fs-h2)">Votre espace entreprise vous attend</h2>
        <p style="color:rgba(255,255,255,.9);font-size:1.05rem">
          Publiez vos offres d'emploi et connectez-vous avec les meilleurs développeurs Laravel ivoiriens.
        </p>
        <div class="auth-stats">
          <div><div class="n">500+</div><div class="l">Développeurs</div></div>
          <div><div class="n">100+</div><div class="l">Offres publiées</div></div>
          <div><div class="n">48h</div><div class="l">Délai moyen</div></div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/web/js/main.js') }}"></script>

</body>
</html>
