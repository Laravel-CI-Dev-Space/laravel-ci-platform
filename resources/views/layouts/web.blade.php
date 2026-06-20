<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @php
    $seoTitle       = trim($__env->yieldContent('title')) ?: null;
    $seoDescription = trim($__env->yieldContent('description')) ?: null;
    $seoImage       = trim($__env->yieldContent('og_image')) ?: null;
    $seoRobots      = trim($__env->yieldContent('robots')) ?: null;
  @endphp
  <x-web.seo
    :title="$seoTitle"
    :description="$seoDescription"
    :image="$seoImage"
    :robots="$seoRobots"
  />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  @php
    $favicon    = $globalSettings->get('identity_favicon')?->value;
    $faviconUrl = $favicon ? asset('assets/' . $favicon) : asset('assets/web/img/favicon.png');
  @endphp
  <link rel="icon" href="{{ $faviconUrl }}" />

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
  <style>[x-cloak]{display:none!important}</style>
</head>
<body>

  <x-web.viewing-as-banner />

  <x-web.header />

  <main>
    @yield('content')
  </main>

  <x-web.footer />

  <button class="scroll-top" id="scrollTop" aria-label="Scroll to top"><i class="fa-solid fa-arrow-up"></i></button>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"/>
  <script src="{{ asset('assets/web/js/main.js') }}"></script>

  @stack('scripts')
</body>
</html>
