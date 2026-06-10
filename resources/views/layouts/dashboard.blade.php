<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Dashboard') — Laravel CI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/dashboard/images/favicon_io/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/dashboard/images/favicon_io/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/dashboard/images/favicon_io/favicon-16x16.png') }}">

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <!-- Dashboard custom styles (compiled from SCSS) -->
  <link rel="stylesheet" href="{{ asset('assets/dashboard/css/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/dashboard/css/dashboard-events.css') }}" />

  @livewireStyles
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

  <div id="dash-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1080"></div>

  @livewireScripts
  <script>
    function initDashTooltips() {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const existing = bootstrap.Tooltip.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Tooltip(el);
      });
    }

    function showDashToast(message, type = 'success') {
      const container = document.getElementById('dash-toast-container');
      const id = 'toast-' + Date.now();
      const bg = type === 'error' ? 'text-bg-danger' : 'text-bg-success';
      container.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center ${bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
          </div>
        </div>`);
      const toastEl = document.getElementById(id);
      bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
      toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    document.addEventListener('DOMContentLoaded', initDashTooltips);

    document.addEventListener('livewire:init', () => {
      Livewire.hook('morph.updated', () => initDashTooltips());
      Livewire.on('dash-toast', ({ message, type = 'success' }) => showDashToast(message, type));
    });
  </script>

  @stack('scripts')
</body>
</html>
