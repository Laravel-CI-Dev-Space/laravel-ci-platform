<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="{{ route('home') }}" class="d-inline-flex align-items-center">
      <img src="{{ asset('assets/dashboard/images/logo-icon.svg') }}" alt="" width="24">
      <span class="logo-text ms-2"><img src="{{ asset('assets/dashboard/images/logo.svg') }}" alt="Laravel CI"></span>
    </a>
  </div>

  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Main</small></li>

    @if(auth()->check() && auth()->user()->hasRole('moderator'))
      {{-- Moderator nav items --}}
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.overview') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.overview') }}">
          <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.moderator.reports') ? 'active' : '' }}"
           href="{{ route('dashboard.moderator.reports') }}">
          <i class="ti ti-receipt"></i><span class="nav-text">Reports</span>
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
          <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.questions') ? 'active' : '' }}"
           href="{{ route('dashboard.member.questions') }}">
          <i class="ti ti-message-circle-question"></i><span class="nav-text">My Questions</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.articles') ? 'active' : '' }}"
           href="{{ route('dashboard.member.articles') }}">
          <i class="ti ti-file-text"></i><span class="nav-text">My Articles</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.events') ? 'active' : '' }}"
           href="{{ route('dashboard.member.events') }}">
          <i class="ti ti-calendar-event"></i><span class="nav-text">My Events</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.applications') ? 'active' : '' }}"
           href="{{ route('dashboard.member.applications') }}">
          <i class="ti ti-briefcase"></i><span class="nav-text">Applications</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.favorites') ? 'active' : '' }}"
           href="{{ route('dashboard.member.favorites') }}">
          <i class="ti ti-heart"></i><span class="nav-text">Saved Jobs</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ request()->routeIs('dashboard.member.profile') ? 'active' : '' }}"
           href="{{ route('dashboard.member.profile') }}">
          <i class="ti ti-user-circle"></i><span class="nav-text">Profile</span>
        </a>
      </li>
    @endif

    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    <li>
      <a class="nav-link" href="{{ route('home') }}">
        <i class="ti ti-home"></i><span class="nav-text">Back to Site</span>
      </a>
    </li>
    <li>
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
          <i class="ti ti-logout"></i><span class="nav-text">Log out</span>
        </button>
      </form>
    </li>
  </ul>
</aside>
