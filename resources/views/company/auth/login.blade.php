<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Espace Entreprise — Laravel CI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    * { font-family: 'Outfit', system-ui, sans-serif; }
    body { background: #fff; }

    .auth-panel-right {
      background: linear-gradient(145deg, #e8590c 0%, #c44508 60%, #a33a06 100%);
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 3rem 2rem;
    }
    .auth-panel-left {
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 2.5rem 1.5rem;
      background: #fff;
    }
    .auth-form-box { width: 100%; max-width: 420px; }
    .brand-logo { display: flex; align-items: center; gap: .6rem; margin-bottom: 2rem; text-decoration: none; }
    .brand-logo img { width: 40px; height: 40px; border-radius: 10px; }
    .brand-logo span { font-weight: 800; font-size: 1.2rem; color: #0f1b35; }
    .brand-logo small { font-size: .72rem; color: #e8590c; font-weight: 700; background: rgba(232,89,12,.1); padding: .15rem .5rem; border-radius: 2rem; margin-left: .25rem; }

    .form-control {
      border-radius: 10px; border: 1.5px solid #dee2e6;
      padding: .75rem 1rem; font-size: .95rem;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus { border-color: #e8590c; box-shadow: 0 0 0 3px rgba(232,89,12,.12); outline: none; }
    .form-label { font-weight: 600; font-size: .88rem; color: #0f1b35; margin-bottom: .4rem; }

    .btn-login {
      width: 100%; padding: .85rem; border-radius: 12px;
      background: #e8590c; border: none; color: #fff;
      font-size: 1rem; font-weight: 700; cursor: pointer; transition: all .2s;
    }
    .btn-login:hover { background: #c44508; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(232,89,12,.3); }

    /* Panneau droit */
    .right-inner { max-width: 420px; text-align: center; }
    .right-icon { width: 80px; height: 80px; background: rgba(255,255,255,.18); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2.2rem; color: #fff; }
    .right-inner h2 { color: #fff; font-size: 1.8rem; font-weight: 800; margin-bottom: 1rem; }
    .right-inner p { color: rgba(255,255,255,.85); font-size: .95rem; line-height: 1.65; margin-bottom: 2rem; }
    .feature-list { text-align: left; list-style: none; padding: 0; }
    .feature-list li { color: rgba(255,255,255,.9); font-size: .9rem; padding: .55rem 0; border-bottom: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; gap: .75rem; }
    .feature-list li:last-child { border-bottom: none; }
    .feature-list li i { width: 32px; height: 32px; background: rgba(255,255,255,.18); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }

    @media (max-width: 991.98px) {
      .auth-panel-left { min-height: 100vh; }
    }
  </style>
</head>
<body>

<div class="container-fluid p-0">
  <div class="row g-0">

    {{-- ─── Panneau gauche : connexion ─── --}}
    <div class="col-12 col-lg-5 auth-panel-left">
      <div class="auth-form-box">

        <a href="{{ route('home') }}" class="brand-logo">
          <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI" />
          <span>Laravel CI <small>Entreprise</small></span>
        </a>

        <h1 style="font-size:1.7rem; font-weight:800; color:#0f1b35; margin-bottom:.4rem">Espace Entreprise</h1>
        <p style="color:#6c757d; font-size:.92rem; margin-bottom:2rem">Gérez vos offres d'emploi et vos candidatures.</p>

        @if (session('error'))
          <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
        @endif
        @if (session('warning'))
          <div class="alert alert-warning rounded-3 mb-3">{{ session('warning') }}</div>
        @endif
        @if (session('success'))
          <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('company.login.submit') }}" novalidate>
          @csrf

          <div class="mb-3">
            <label class="form-label">Adresse email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="contact@votreentreprise.ci"
                   autocomplete="email" autofocus />
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label mb-0">Mot de passe</label>
            </div>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••••"
                   autocomplete="current-password" />
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex align-items-center gap-2 mb-4">
            <input type="checkbox" name="remember" id="remember" class="form-check-input mt-0" style="width:18px;height:18px;cursor:pointer" />
            <label for="remember" style="font-size:.88rem; color:#6c757d; cursor:pointer">Se souvenir de moi</label>
          </div>

          <button type="submit" class="btn-login">
            <i class="fa-solid fa-sign-in-alt me-2"></i>Se connecter
          </button>
        </form>

        <div style="margin-top:2rem; padding-top:1.25rem; border-top:1px solid #eef0f4; text-align:center">
          <p style="font-size:.85rem; color:#6c757d; margin-bottom:.75rem">
            Pas encore de compte entreprise ?
          </p>
          <a href="{{ route('company.register') }}"
             style="display:block; padding:.65rem; border:1.5px solid #e8590c; border-radius:10px; color:#e8590c; font-weight:600; font-size:.88rem; text-decoration:none; transition:.15s"
             onmouseover="this.style.background='#e8590c';this.style.color='#fff'"
             onmouseout="this.style.background='transparent';this.style.color='#e8590c'">
            <i class="fa-solid fa-paper-plane me-1"></i>Faire une demande d'accès
          </a>
          <p style="margin-top:1rem; font-size:.82rem; color:#adb5bd">
            Vous êtes développeur ?
            <a href="{{ route('login') }}" style="color:#0f1b35; font-weight:600; text-decoration:none">Connexion membre</a>
          </p>
        </div>

      </div>
    </div>

    {{-- ─── Panneau droit : présentation ─── --}}
    <div class="col-lg-7 d-none d-lg-flex auth-panel-right">
      <div class="right-inner">

        <div class="right-icon">
          <i class="fa-solid fa-briefcase"></i>
        </div>

        <h2>Recrutez les meilleurs développeurs Laravel ivoiriens</h2>
        <p>Publiez vos offres sur le Job Board dédié à la communauté Laravel CI et accédez à des profils qualifiés.</p>

        <ul class="feature-list">
          <li>
            <i class="fa-solid fa-bullhorn"></i>
            Publiez vos offres en quelques minutes
          </li>
          <li>
            <i class="fa-solid fa-users"></i>
            Accès à 500+ développeurs actifs
          </li>
          <li>
            <i class="fa-solid fa-file-arrow-down"></i>
            Recevez les CV directement dans votre tableau de bord
          </li>
          <li>
            <i class="fa-solid fa-shield-check"></i>
            Profils vérifiés, communauté de confiance
          </li>
          <li>
            <i class="fa-solid fa-chart-line"></i>
            Statistiques de vues et de candidatures en temps réel
          </li>
        </ul>

      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
