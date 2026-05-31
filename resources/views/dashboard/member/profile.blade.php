@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('content')

@php
  /** @var \App\Models\User $me */
  $me      = auth()->user();
  $profile = $me->profile;
  $avatar  = $profile?->avatarUrl($me->avatar) ?? $me->avatar;
@endphp

  <x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'My Profile']
  ]" />

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
      <h1 class="fs-4 fw-bold mb-1">My Profile</h1>
      <p class="text-secondary mb-0">Your public profile visible to the community.</p>
    </div>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
      <i class="ti ti-pencil me-1"></i> Edit Profile
    </a>
  </div>

  {{-- LIVEWIRE: @livewire('dashboard.edit-profile') --}}

  <div class="row g-4">

    {{-- ── Left col: Identity card ─────────────────────────── --}}
    <div class="col-lg-4">

      {{-- Profile card --}}
      <div class="card mb-4">
        <div class="card-body text-center py-4">
          <div class="position-relative d-inline-block mb-3">
            <img src="{{ $avatar }}"
                 alt="{{ $me->name }}"
                 class="rounded-circle border border-3"
                 style="width:90px;height:90px;object-fit:cover;border-color:var(--lci-primary) !important">
            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white"
                  style="width:14px;height:14px"></span>
          </div>

          <h4 class="fw-bold mb-0 fs-5">{{ $me->name }}</h4>
          <p class="text-secondary small mb-2">
            <i class="ti ti-brand-github me-1"></i>{{ '@' . $me->github_username }}
          </p>

          <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-3">
            member
          </span>

          @if($profile)
            {{-- Completion progress --}}
            <div class="mt-4 text-start">
              <div class="d-flex justify-content-between mb-1">
                <span class="small text-secondary">Profile completion</span>
                <span class="small fw-semibold" style="color:var(--lci-primary)">
                  {{ $profile->completionRate() }}%
                </span>
              </div>
              <div class="progress" style="height:6px">
                <div class="progress-bar bg-primary" style="width:{{ $profile->completionRate() }}%"></div>
              </div>
            </div>

            {{-- Location --}}
            @if($profile->city || $profile->country)
              <p class="text-secondary small mt-3 mb-0">
                <i class="ti ti-map-pin me-1"></i>
                {{ implode(', ', array_filter([$profile->city, $profile->district, $profile->country])) }}
              </p>
            @endif

            {{-- Portfolio --}}
            @if($profile->portfolio_url)
              <a href="{{ $profile->portfolio_url }}" target="_blank"
                 class="d-block small mt-1 text-primary text-decoration-none">
                <i class="ti ti-world me-1"></i>{{ $profile->portfolio_url }}
              </a>
            @endif

            {{-- CV --}}
            @if($profile->cvUrl())
              <a href="{{ $profile->cvUrl() }}" target="_blank"
                 class="d-inline-flex align-items-center gap-1 mt-2 small text-secondary text-decoration-none">
                <i class="ti ti-file-type-pdf" style="color:var(--lci-danger)"></i> View CV
              </a>
            @endif
          @endif
        </div>
      </div>

      {{-- Tech stack --}}
      @if($profile && count($profile->tech_stack ?? []) > 0)
        <div class="card mb-4">
          <div class="card-header fw-semibold small">
            <i class="ti ti-code me-2 text-primary"></i>Tech Stack
          </div>
          <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
              @foreach($profile->tech_stack as $tech)
                <span class="badge rounded-pill fw-normal px-3 py-1"
                      style="background:rgba(230,98,57,.1);color:var(--lci-primary);font-size:.78rem">
                  {{ $tech }}
                </span>
              @endforeach
            </div>
          </div>
        </div>
      @endif

    </div>{{-- /col-lg-4 --}}

    {{-- ── Right col: Details ───────────────────────────────── --}}
    <div class="col-lg-8">

      @if(!$profile)
        {{-- No profile yet --}}
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="ti ti-user-off d-block mb-3" style="font-size:2.5rem;color:var(--lci-gray-300)"></i>
            <h5 class="fw-semibold mb-2">Profile not completed yet</h5>
            <p class="text-secondary mb-4">Complete your profile to appear in the community directory.</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4">
              <i class="ti ti-pencil me-2"></i>Complete my profile
            </a>
          </div>
        </div>
      @else

        {{-- Bio --}}
        <div class="card mb-4">
          <div class="card-header fw-semibold small">
            <i class="ti ti-pencil me-2 text-primary"></i>About
          </div>
          <div class="card-body py-3">
            @if($profile->bio)
              <p class="mb-0" style="line-height:1.7">{{ $profile->bio }}</p>
            @else
              <p class="text-secondary mb-0 fst-italic small">No bio yet.</p>
            @endif
          </div>
        </div>

        {{-- Technical profile --}}
        <div class="card mb-4">
          <div class="card-header fw-semibold small">
            <i class="ti ti-code me-2 text-primary"></i>Technical Profile
          </div>
          <div class="card-body py-3">
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="small text-secondary text-uppercase fw-semibold mb-1"
                     style="font-size:.68rem;letter-spacing:.06em">Laravel Level</div>
                <div class="fw-semibold">
                  {{ $profile->laravelLevelLabel() }}
                </div>
              </div>
              <div class="col-sm-6">
                <div class="small text-secondary text-uppercase fw-semibold mb-1"
                     style="font-size:.68rem;letter-spacing:.06em">Experience</div>
                <div class="fw-semibold">
                  {{ $profile->yearsExperienceLabel() }}
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Academic & career --}}
        <div class="card mb-4">
          <div class="card-header fw-semibold small">
            <i class="ti ti-school me-2 text-primary"></i>Academic &amp; Career
          </div>
          <div class="card-body py-3">
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="small text-secondary text-uppercase fw-semibold mb-1"
                     style="font-size:.68rem;letter-spacing:.06em">Academic Level</div>
                <div class="fw-semibold">{{ $profile->academicLevelLabel() }}</div>
              </div>
              <div class="col-sm-6">
                <div class="small text-secondary text-uppercase fw-semibold mb-1"
                     style="font-size:.68rem;letter-spacing:.06em">Job Status</div>
                <div class="fw-semibold">{{ $profile->jobStatusLabel() }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Missing fields (if profile incomplete) --}}
        @if($profile->completionRate() < 100 && count($profile->missingFields()) > 0)
          <div class="card border-warning-subtle">
            <div class="card-body py-3">
              <div class="d-flex align-items-start gap-3">
                <i class="ti ti-info-circle text-warning" style="font-size:1.2rem;flex-shrink:0;margin-top:2px"></i>
                <div>
                  <p class="small fw-semibold mb-1">Complete your profile to be fully visible</p>
                  <div class="d-flex flex-wrap gap-1">
                    @foreach($profile->missingFields() as $field)
                      <span class="badge fw-normal rounded-pill"
                            style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:.7rem">
                        {{ $field }}
                      </span>
                    @endforeach
                  </div>
                  <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-warning mt-2">
                    <i class="ti ti-pencil me-1"></i>Complete now
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endif

      @endif
    </div>{{-- /col-lg-8 --}}

  </div>{{-- /row --}}

@endsection
