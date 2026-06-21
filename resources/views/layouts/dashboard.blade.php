<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Dashboard') — Laravel CI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  @php
    $favicon = \App\Models\SiteSetting::get('identity_favicon');
    $faviconUrl = $favicon ? asset('assets/' . $favicon) : asset('assets/web/img/mascot.png');
  @endphp
  <link rel="icon" href="{{ $faviconUrl }}" />

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <!-- Dashboard custom styles (compiled from SCSS) -->
  <link rel="stylesheet" href="{{ asset('assets/dashboard/css/dashboard.css') }}" />

  @stack('styles')
</head>
<body>

  <div id="overlay" class="overlay"></div>

  <x-dashboard.topbar />

  <x-dashboard.sidebar />

  <main id="content" class="content py-4">
    <div class="container-fluid px-4">
      @yield('content')

      <footer class="text-center py-3 mt-4 text-secondary small border-top">
        © {{ date('Y') }} Laravel Côte d'Ivoire
      </footer>
    </div>
  </main>

  <!-- Bootstrap JS (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Sidebar toggle — standalone, no module imports -->
  <script src="{{ asset('assets/dashboard/js/sidebar.js') }}"></script>

  @stack('scripts')
</body>
</html>
