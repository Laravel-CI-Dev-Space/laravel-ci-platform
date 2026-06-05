<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Demande d'accès Entreprise — Laravel CI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    * { font-family: 'Outfit', system-ui, sans-serif; }

    body { background: #f4f6f9; min-height: 100vh; }

    /* ── Topbar ── */
    .auth-topbar {
      background: #fff;
      border-bottom: 1px solid #eef0f4;
      padding: .9rem 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 100;
    }
    .brand-link { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
    .brand-link img { width: 36px; height: 36px; border-radius: 8px; }
    .brand-link span { font-weight: 800; font-size: 1.1rem; color: #0f1b35; }
    .brand-link small { font-size: .7rem; color: #e8590c; font-weight: 700; background: rgba(232,89,12,.1); padding: .1rem .45rem; border-radius: 2rem; }

    /* ── Hero intro ── */
    .page-intro {
      background: linear-gradient(135deg, #e8590c, #c44508);
      padding: 3rem 1.5rem 2.5rem;
      text-align: center; color: #fff;
    }
    .page-intro h1 { font-size: 2rem; font-weight: 800; margin-bottom: .6rem; }
    .page-intro p { opacity: .88; font-size: 1rem; max-width: 540px; margin: 0 auto; }

    /* ── Progress steps ── */
    .steps-bar {
      display: flex; justify-content: center; gap: 0; margin-top: 1.75rem;
    }
    .step {
      display: flex; flex-direction: column; align-items: center;
      gap: .3rem; flex: 0 0 auto; position: relative;
    }
    .step:not(:last-child)::after {
      content: ''; position: absolute; top: 16px; left: calc(50% + 16px);
      width: 60px; height: 2px; background: rgba(255,255,255,.3);
    }
    .step-num {
      width: 32px; height: 32px; border-radius: 50%;
      background: rgba(255,255,255,.25); color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .85rem;
    }
    .step-label { font-size: .72rem; color: rgba(255,255,255,.8); white-space: nowrap; }

    /* ── Card sections ── */
    .form-card {
      background: #fff;
      border-radius: 16px;
      border: 1px solid #eef0f4;
      margin-bottom: 1.25rem;
      overflow: hidden;
    }
    .form-card-header {
      display: flex; align-items: center; gap: .75rem;
      padding: 1.1rem 1.5rem;
      border-bottom: 1px solid #eef0f4;
      background: #fafbfc;
    }
    .form-card-header .icon {
      width: 36px; height: 36px; border-radius: 10px;
      background: rgba(232,89,12,.1); color: #e8590c;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; flex-shrink: 0;
    }
    .form-card-header h2 { font-size: 1rem; font-weight: 700; color: #0f1b35; margin: 0; }
    .form-card-header p  { font-size: .78rem; color: #6c757d; margin: .1rem 0 0; }
    .form-card-body { padding: 1.5rem; }

    /* ── Inputs ── */
    .form-label { font-weight: 600; font-size: .85rem; color: #0f1b35; margin-bottom: .35rem; }
    .form-label .badge-opt { font-size: .65rem; font-weight: 500; color: #adb5bd; background: #f0f2f5; padding: .1rem .4rem; border-radius: 2rem; margin-left: .35rem; }
    .form-control, .form-select {
      border-radius: 10px; border: 1.5px solid #dee2e6;
      padding: .7rem 1rem; font-size: .92rem;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus, .form-select:focus {
      border-color: #e8590c; box-shadow: 0 0 0 3px rgba(232,89,12,.1); outline: none;
    }
    .form-control.is-invalid { border-color: #dc3545; }
    .invalid-feedback { font-size: .8rem; }

    /* ── Logo upload ── */
    .logo-upload-zone {
      border: 2px dashed #dee2e6; border-radius: 12px;
      padding: 1.5rem; text-align: center; cursor: pointer;
      transition: border-color .2s, background .2s;
      position: relative;
    }
    .logo-upload-zone:hover { border-color: #e8590c; background: #fff8f5; }
    .logo-upload-zone input[type=file] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .logo-upload-zone .upload-icon { font-size: 2rem; color: #adb5bd; margin-bottom: .5rem; }
    .logo-upload-zone p { font-size: .85rem; color: #6c757d; margin: 0; }
    .logo-upload-zone small { font-size: .75rem; color: #adb5bd; }
    #logoPreview { display: none; width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 2px solid #dee2e6; margin: 0 auto .75rem; }

    /* ── Textarea ── */
    textarea.form-control { resize: vertical; min-height: 120px; }

    /* ── Submit bar ── */
    .submit-bar {
      background: #fff; border-top: 1px solid #eef0f4;
      padding: 1.25rem 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
      gap: 1rem; flex-wrap: wrap;
      position: sticky; bottom: 0; z-index: 50;
    }
    .btn-submit {
      padding: .75rem 2rem; border-radius: 12px;
      background: #e8590c; border: none; color: #fff;
      font-size: .95rem; font-weight: 700; cursor: pointer; transition: all .2s;
      display: inline-flex; align-items: center; gap: .5rem;
    }
    .btn-submit:hover { background: #c44508; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(232,89,12,.3); }
    .btn-back {
      padding: .7rem 1.5rem; border-radius: 12px;
      border: 1.5px solid #dee2e6; background: transparent;
      color: #6c757d; font-size: .92rem; font-weight: 600;
      text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
    }
    .btn-back:hover { border-color: #adb5bd; color: #0f1b35; }

    /* ── Alerts ── */
    .alert { border-radius: 12px; border: none; }
    .alert-success { background: #edfaf3; color: #1a7a45; }
    .alert-danger  { background: #fff0ef; color: #c0392b; }

    /* Responsive */
    @media (max-width: 575.98px) {
      .page-intro h1 { font-size: 1.5rem; }
      .steps-bar { display: none; }
      .submit-bar { flex-direction: column; }
      .btn-submit, .btn-back { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>

{{-- ── Topbar ── --}}
<div class="auth-topbar">
  <a href="{{ route('home') }}" class="brand-link">
    <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI" />
    <span>Laravel CI <small>Entreprise</small></span>
  </a>
  <a href="{{ route('company.login') }}" style="font-size:.85rem; color:#6c757d; text-decoration:none">
    <i class="fa-solid fa-arrow-left me-1"></i>Déjà un compte ? Se connecter
  </a>
</div>

{{-- ── Hero ── --}}
<div class="page-intro">
  <h1><i class="fa-solid fa-building me-2"></i>Demande d'accès Entreprise</h1>
  <p>Remplissez ce formulaire pour accéder au Job Board Laravel CI. Votre demande sera examinée sous 48h.</p>

  <div class="steps-bar mt-3">
    @foreach ([['fa-user', 'Responsable'], ['fa-building', 'Entreprise'], ['fa-envelope', 'Contact'], ['fa-paper-plane', 'Envoi']] as $i => [$ico, $lbl])
      <div class="step" style="width:80px">
        <div class="step-num"><i class="fa-solid {{ $ico }}"></i></div>
        <div class="step-label">{{ $lbl }}</div>
      </div>
    @endforeach
  </div>
</div>

{{-- ── Contenu ── --}}
<div class="container py-4" style="max-width:780px">

  @if (session('success'))
    <div class="alert alert-success mb-4 p-3">
      <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger mb-4 p-3">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>
      <strong>Veuillez corriger les erreurs suivantes :</strong>
      <ul class="mb-0 mt-1 ps-3">
        @foreach ($errors->all() as $error)
          <li style="font-size:.88rem">{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('company.register.submit') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- ──────────────────────────────────────── --}}
    {{-- 1. Informations personnelles             --}}
    {{-- ──────────────────────────────────────── --}}
    <div class="form-card">
      <div class="form-card-header">
        <div class="icon"><i class="fa-solid fa-user"></i></div>
        <div>
          <h2>Informations personnelles</h2>
          <p>Responsable du compte entreprise</p>
        </div>
      </div>
      <div class="form-card-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label">Prénom <span class="text-danger">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name') }}"
                   class="form-control @error('first_name') is-invalid @enderror"
                   placeholder="Jean" maxlength="100" />
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label">Nom <span class="text-danger">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name') }}"
                   class="form-control @error('last_name') is-invalid @enderror"
                   placeholder="Koné" maxlength="100" />
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label">Poste occupé <span class="text-danger">*</span></label>
            <input type="text" name="position" value="{{ old('position') }}"
                   class="form-control @error('position') is-invalid @enderror"
                   placeholder="Directeur RH, CEO, Recruteur…" maxlength="100" />
            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label">Téléphone <span class="badge-opt">optionnel</span></label>
            <input type="tel" name="phone" value="{{ old('phone') }}"
                   class="form-control @error('phone') is-invalid @enderror"
                   placeholder="+225 07 00 00 00 00" maxlength="30" />
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ──────────────────────────────────────── --}}
    {{-- 2. Informations entreprise              --}}
    {{-- ──────────────────────────────────────── --}}
    <div class="form-card">
      <div class="form-card-header">
        <div class="icon"><i class="fa-solid fa-building"></i></div>
        <div>
          <h2>Informations entreprise</h2>
          <p>Décrivez votre structure et activité</p>
        </div>
      </div>
      <div class="form-card-body">
        <div class="row g-3">

          {{-- Logo -- --}}
          <div class="col-12">
            <label class="form-label">
              Logo de l'entreprise <span class="badge-opt">optionnel — JPG, PNG, WebP · max 2 Mo</span>
            </label>
            <div class="logo-upload-zone" id="logoZone">
              <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png,image/webp,image/svg+xml"
                     onchange="previewLogo(this)" />
              <img id="logoPreview" src="" alt="Aperçu logo" />
              <div id="logoPlaceholder">
                <div class="upload-icon"><i class="fa-solid fa-image"></i></div>
                <p><strong>Cliquez pour uploader</strong> ou glissez votre logo ici</p>
                <small>JPG, PNG, WebP, SVG · max 2 Mo</small>
              </div>
            </div>
            @error('logo') <div class="text-danger mt-1" style="font-size:.8rem">{{ $message }}</div> @enderror
          </div>

          <div class="col-sm-8">
            <label class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
            <input type="text" name="company_name" value="{{ old('company_name') }}"
                   class="form-control @error('company_name') is-invalid @enderror"
                   placeholder="ACME Tech CI" maxlength="200" />
            @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-4">
            <label class="form-label">Secteur d'activité <span class="text-danger">*</span></label>
            <input type="text" name="business_domain" value="{{ old('business_domain') }}"
                   class="form-control @error('business_domain') is-invalid @enderror"
                   placeholder="Fintech, EdTech…" maxlength="150" />
            @error('business_domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label">Pays <span class="text-danger">*</span></label>
            <input type="text" name="country" value="{{ old('country', "Côte d'Ivoire") }}"
                   class="form-control @error('country') is-invalid @enderror"
                   maxlength="100" />
            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label">Ville <span class="badge-opt">optionnel</span></label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="form-control @error('city') is-invalid @enderror"
                   placeholder="Abidjan" maxlength="100" />
            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-12">
            <label class="form-label">Site web <span class="badge-opt">optionnel</span></label>
            <input type="url" name="website" value="{{ old('website') }}"
                   class="form-control @error('website') is-invalid @enderror"
                   placeholder="https://votreentreprise.ci" />
            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ──────────────────────────────────────── --}}
    {{-- 3. Contact & Message                    --}}
    {{-- ──────────────────────────────────────── --}}
    <div class="form-card">
      <div class="form-card-header">
        <div class="icon"><i class="fa-solid fa-envelope"></i></div>
        <div>
          <h2>Contact &amp; Message</h2>
          <p>Email d'accès et présentation de votre entreprise</p>
        </div>
      </div>
      <div class="form-card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Email professionnel <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="rh@votreentreprise.ci" />
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div style="font-size:.78rem; color:#adb5bd; margin-top:.4rem">
              <i class="fa-solid fa-info-circle me-1"></i>
              Cet email sera votre identifiant de connexion une fois la demande validée.
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">
              Message de présentation <span class="badge-opt">optionnel</span>
            </label>
            <textarea name="motivation" rows="4" maxlength="1000"
                      class="form-control @error('motivation') is-invalid @enderror"
                      placeholder="Présentez brièvement votre entreprise, vos besoins en recrutement et pourquoi vous souhaitez rejoindre la plateforme Laravel CI…">{{ old('motivation') }}</textarea>
            @error('motivation') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ── Barre de soumission ── --}}
    <div class="submit-bar rounded-3">
      <a href="{{ route('company.login') }}" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>Retour
      </a>
      <div style="font-size:.8rem; color:#adb5bd; text-align:center">
        <i class="fa-solid fa-shield-check me-1" style="color:#2ecc71"></i>
        Vos données sont traitées en toute confidentialité.
      </div>
      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-paper-plane"></i>
        Soumettre ma demande
      </button>
    </div>

  </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewLogo(input) {
  const preview     = document.getElementById('logoPreview');
  const placeholder = document.getElementById('logoPlaceholder');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src  = e.target.result;
      preview.style.display = 'block';
      placeholder.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>
