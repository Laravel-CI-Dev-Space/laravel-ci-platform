<header class="site-header">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="brand-logo" href="{{ route('home') }}">
        <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" /></span>
        Laravel CI
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav main-nav mx-lg-auto mb-2 mb-lg-0">
          @foreach($navItems as $item)
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                {{ $item['label'] }}
              </a>
            </li>
          @endforeach
        </ul>
        @auth
          <a href="{{ route('dashboard') }}" class="btn btn-github">
            <i class="fa-solid fa-gauge"></i> Dashboard
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-github">
            <i class="fa-brands fa-github"></i> Sign in with GitHub
          </a>
        @endauth
      </div>
    </div>
  </nav>
</header>
