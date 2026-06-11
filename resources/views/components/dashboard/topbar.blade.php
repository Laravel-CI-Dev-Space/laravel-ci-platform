<style>
  .dashboard-notification-bell .notification-bell-btn { color: #697a8d; font-size: 1.25rem; }
</style>

<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
  <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>

  <!-- MOBILE -->
  <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>

  <div>
    <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">

      <!-- Bell icon -->
      <li class="dashboard-notification-bell">
        @livewire('notifications.notification-bell')
      </li>

      <!-- User dropdown -->
      <li class="ms-3 dropdown">
        @php
          $topbarAvatar = auth()->check()
              ? (auth()->user()->profile?->avatarUrl(auth()->user()->avatar) ?? auth()->user()->avatar ?? asset('assets/dashboard/images/avatar/avatar-1.jpg'))
              : asset('assets/dashboard/images/avatar/avatar-1.jpg');
        @endphp
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ $topbarAvatar }}" alt="{{ auth()->user()?->name }}" class="avatar avatar-sm rounded-circle" />
        </a>
        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 200px;">
          <div>
            <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-3 py-3">
              @auth
                <img src="{{ $topbarAvatar }}" alt="" class="avatar avatar-md rounded-circle" />
                <div>
                  <h4 class="mb-0 small">{{ auth()->user()->name }}</h4>
                  <p class="mb-0 small">{{ '@' . (auth()->user()->github_username ?? auth()->user()->email) }}</p>
                </div>
              @endauth
            </div>
            <div class="p-3 d-flex flex-column gap-1 small lh-lg">
              <a href="{{ route('dashboard') }}"><span>Dashboard</span></a>
              @auth
              <a href="{{ route('dashboard.member.profile') }}"><span>Profile Settings</span></a>
              @endauth
              <a href="{{ route('home') }}"><span>Back to Site</span></a>
            </div>
            <div class="border-top p-3">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                  <i class="ti ti-logout me-1"></i> Log out
                </button>
              </form>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>
