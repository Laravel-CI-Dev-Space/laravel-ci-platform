<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2">
      <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI" width="32" style="object-fit:contain">
      <span class="logo-text fw-bold" style="font-size:1rem;letter-spacing:-.3px">Laravel CI</span>
    </a>
  </div>

  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Main</small></li>

    @if(auth()->check() && auth()->user()->hasRole('moderator'))
      {{-- Moderator nav items --}}
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.overview') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.overview') }}">
          <i class="ti ti-home"></i><span class="nav-text">Tableau de bord</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.reports') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.reports') }}">
          <i class="ti ti-receipt"></i><span class="nav-text">Signalements</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.questions') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.questions') }}">
          <i class="ti ti-message-circle-question"></i><span class="nav-text">Questions</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.articles') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.articles') }}">
          <i class="ti ti-file-text"></i><span class="nav-text">Articles</span>
        </a>
      </li>
    @else
      {{-- Member nav items --}}
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.overview') ? 'active' : '' }}"
           href="{{ route('dashboard.member.overview') }}">
          <i class="ti ti-home"></i><span class="nav-text">Tableau de bord</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.questions') ? 'active' : '' }}"
           href="{{ route('dashboard.member.questions') }}">
          <i class="ti ti-message-circle-question"></i><span class="nav-text">Mes questions</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.articles') ? 'active' : '' }}"
           href="{{ route('dashboard.member.articles') }}">
          <i class="ti ti-file-text"></i><span class="nav-text">Mes articles</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.events') ? 'active' : '' }}"
           href="{{ route('dashboard.member.events') }}">
          <i class="ti ti-calendar-event"></i><span class="nav-text">Mes événements</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.applications') ? 'active' : '' }}"
           href="{{ route('dashboard.member.applications') }}">
          <i class="ti ti-briefcase"></i><span class="nav-text">Candidatures</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.favorites') ? 'active' : '' }}"
           href="{{ route('dashboard.member.favorites') }}">
          <i class="ti ti-heart"></i><span class="nav-text">Offres sauvegardées</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.profile') ? 'active' : '' }}"
           href="{{ route('dashboard.member.profile') }}">
          <i class="ti ti-user-circle"></i><span class="nav-text">Profil</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.assistant') ? 'active' : '' }}"
           href="{{ route('dashboard.member.assistant') }}">
          <i class="ti ti-robot"></i><span class="nav-text">Assistant IA</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.mentions') ? 'active' : '' }}"
           href="{{ route('dashboard.member.mentions') }}">
          <i class="ti ti-hash"></i><span class="nav-text">Mes mentions</span>
        </a>
      </li>
    @endif

    <li class="px-4 pt-4 pb-2"><small class="nav-text">Compte</small></li>
    <li>
      <a class="nav-link" href="{{ route('home') }}">
        <i class="ti ti-home"></i><span class="nav-text">Retour au site</span>
      </a>
    </li>
    <li>
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
          <i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span>
        </button>
      </form>
    </li>
  </ul>
</aside>
