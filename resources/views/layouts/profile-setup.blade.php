<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <title>Complete your profile — Laravel CI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/dashboard/images/favicon_io/favicon-32x32.png') }}">

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

  <style>
    :root {
      --lci-primary: #f97316;
      --lci-navy:    #1C1C2E;
    }
    body { background-color: #f4f6f9; font-family: system-ui, -apple-system, sans-serif; }

    /* Topbar */
    .setup-topbar {
      background: var(--lci-navy);
      padding: .75rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1030;
    }
    .setup-topbar .brand { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
    .setup-topbar .brand img { width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--lci-primary); object-fit: cover; }
    .setup-topbar .brand span { font-weight: 800; font-size: 1.1rem; color: #fff; }
    .setup-topbar .brand span em { color: var(--lci-primary); font-style: normal; }
    .setup-topbar .user-chip { display: flex; align-items: center; gap: .5rem; }
    .setup-topbar .user-chip img { width: 34px; height: 34px; border-radius: 50%; border: 2px solid var(--lci-primary); object-fit: cover; }
    .setup-topbar .user-chip .name { font-size: .85rem; font-weight: 600; color: #fff; }
    .setup-topbar .user-chip .handle { font-size: .75rem; color: #94a3b8; }

    /* Cards */
    .card { border: 1px solid #e5e7eb; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: .75rem .75rem 0 0 !important; padding: 1rem 1.25rem; }
    .card-header .section-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: .5rem; background: rgba(249,115,22,.12); color: var(--lci-primary); margin-right: .6rem; flex-shrink: 0; }
    .card-header h5 { font-size: .9rem; font-weight: 700; margin-bottom: 0; color: #111827; }
    .card-body { background: #fff; border-radius: 0 0 .75rem .75rem; }

    /* Nav sections */
    .section-nav .nav-link { font-size: .82rem; color: #6b7280; padding: .45rem .75rem; border-radius: .5rem; display: flex; align-items: center; gap: .5rem; }
    .section-nav .nav-link:hover,
    .section-nav .nav-link.active { background: rgba(249,115,22,.08); color: var(--lci-primary); }
    .section-nav .nav-link i { width: 1rem; text-align: center; font-size: .8rem; }

    /* Save btn */
    .btn-save {
      background: var(--lci-primary);
      border: none;
      color: #fff;
      font-weight: 700;
      border-radius: .6rem;
      padding: .7rem 1.5rem;
      width: 100%;
      transition: background .2s;
    }
    .btn-save:hover { background: #ea6a0a; color: #fff; }
    .btn-save:disabled { background: #e5e7eb; color: #9ca3af; }

    /* Progress section */
    .progress { height: 8px; border-radius: 4px; }
    .progress-bar { background: var(--lci-primary); }

    /* Stack chips */
    .stack-chip {
      display: inline-flex; align-items: center;
      padding: .25rem .65rem;
      border: 1.5px solid #e5e7eb;
      border-radius: 9999px;
      font-size: .78rem; font-weight: 500;
      cursor: pointer; transition: all .15s;
      background: #f9fafb; color: #6b7280;
    }
    .stack-chip:hover { border-color: var(--lci-primary); color: var(--lci-primary); }
    .stack-chip.selected { background: var(--lci-primary); border-color: var(--lci-primary); color: #fff; }

    /* Alert strips */
    .alert-strip { border-left: 4px solid; border-radius: .4rem; padding: .7rem 1rem; font-size: .875rem; }
    .alert-strip-info    { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }
    .alert-strip-success { border-color: #22c55e; background: #f0fdf4; color: #15803d; }

    /* Avatar preview */
    .avatar-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--lci-primary); }
    .avatar-wrap { position: relative; display: inline-block; }
    .avatar-online { position: absolute; bottom: 3px; right: 3px; width: 14px; height: 14px; border-radius: 50%; background: #22c55e; border: 2px solid #fff; }

    /* Profile card sidebar */
    .profile-card { text-align: center; padding: 1.5rem 1rem; }
    .profile-card .user-name { font-weight: 700; font-size: .9rem; color: #111827; margin-top: .5rem; }
    .profile-card .user-handle { font-size: .78rem; color: #9ca3af; }
    .role-badge { display: inline-flex; align-items: center; gap: .3rem; background: #dcfce7; color: #15803d; font-size: .72rem; font-weight: 600; padding: .2rem .65rem; border-radius: 9999px; margin-top: .4rem; }

    /* Missing fields */
    .missing-badge { display: inline-flex; background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-size: .7rem; font-weight: 600; padding: .15rem .5rem; border-radius: 9999px; }

    /* File input */
    .form-control-file-custom { border: 2px dashed #e5e7eb; border-radius: .5rem; background: #f9fafb; padding: .6rem 1rem; font-size: .82rem; cursor: pointer; transition: border-color .2s; }
    .form-control-file-custom:hover { border-color: var(--lci-primary); }

    @media (max-width: 991px) {
      .sidebar-profile { display: none; }
    }
  </style>

  @livewireStyles
  @stack('styles')
</head>
<body>

  {{-- ── Topbar ── --}}
  @auth
  <nav class="setup-topbar">
    <a class="brand" href="{{ route('home') }}">
      <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI" />
      <span>Laravel <em>CI</em></span>
    </a>
    <div class="user-chip">
      <img src="{{ auth()->user()->avatar ?? asset('assets/dashboard/images/avatar/avatar-1.jpg') }}"
           alt="{{ auth()->user()->name }}" />
      <div>
        <div class="name">{{ auth()->user()->name }}</div>
        <div class="handle">@{{ auth()->user()->github_username }}</div>
      </div>
    </div>
  </nav>
  @endauth

  {{-- ── Content ── --}}
  <div class="container-lg py-4 px-3 px-md-4">
    {{ $slot }}
  </div>

  <footer class="text-center py-3 mt-2 text-secondary small">
    © {{ date('Y') }} Laravel Côte d'Ivoire
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @livewireScripts
  @stack('scripts')
</body>
</html>
