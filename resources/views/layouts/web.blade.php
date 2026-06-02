<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Laravel CI — The Laravel Community of Côte d\'Ivoire')</title>
  <meta name="description" content="@yield('description', 'Join 500+ Ivorian Laravel & PHP developers. Share knowledge, find jobs, attend events, and grow together.')" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome 6 Free -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <!-- Shared styles -->
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}" />

  @stack('styles')
  @livewireStyles
</head>
<body>

  <x-web.header />

  @if(session('success') || session('error'))
    <div class="container pt-3">
      @if(session('success'))
        <div class="alert alert-success d-flex align-items-start gap-2 mb-0" role="alert">
          <i class="fa-solid fa-circle-check mt-1"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-0" role="alert">
          <i class="fa-solid fa-triangle-exclamation mt-1"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif
    </div>
  @endif

  <main>
    @yield('content')
  </main>

  <x-web.footer />

  <button class="scroll-top" id="scrollTop" aria-label="Scroll to top"><i class="fa-solid fa-arrow-up"></i></button>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/web/js/main.js') }}"></script>

  @stack('scripts')
  @livewireScripts
</body>
</html>
