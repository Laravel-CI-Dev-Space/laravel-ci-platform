@extends('layouts.dashboard')

@section('title', 'Moderator Dashboard')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Moderator Dashboard', 'href' => route('dashboard.moderator.overview')], ['label' => 'Overview']]" />

  <div class="row">
    <div class="col-12">
      <div class="mb-6">
        <h1 class="fs-3 mb-1">Moderator Dashboard</h1>
        <p class="text-muted">Community health overview and moderation queue.</p>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-3">
    <div class="col-lg-3 col-12">
      <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2">
        <div class="d-flex gap-3">
          <div class="icon-shape icon-md bg-primary text-white rounded-2"><i class="ti ti-users fs-4"></i></div>
          <div>
            <h2 class="mb-3 fs-6">Total Members</h2>
            <h3 class="fw-bold mb-0">{{ number_format($stats['members'] ?? 500) }}</h3>
            <p class="text-primary mb-0 small">+5% this month</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">
        <div class="d-flex gap-3">
          <div class="icon-shape icon-md bg-success text-white rounded-2"><i class="ti ti-message-circle-question fs-4"></i></div>
          <div>
            <h2 class="mb-3 fs-6">Total Questions</h2>
            <h3 class="fw-bold mb-0">{{ number_format($stats['questions'] ?? 1204) }}</h3>
            <p class="text-success mb-0 small">+22 this week</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2">
        <div class="d-flex gap-3">
          <div class="icon-shape icon-md bg-warning text-white rounded-2"><i class="ti ti-alert-triangle fs-4"></i></div>
          <div>
            <h2 class="mb-3 fs-6">Pending Reports</h2>
            <h3 class="fw-bold mb-0">{{ $stats['pending_reports'] ?? 0 }}</h3>
            <p class="text-warning mb-0 small">Needs review</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-2">
        <div class="d-flex gap-3">
          <div class="icon-shape icon-md bg-danger text-white rounded-2"><i class="ti ti-user-off fs-4"></i></div>
          <div>
            <h2 class="mb-3 fs-6">Banned Members</h2>
            <h3 class="fw-bold mb-0">{{ $stats['banned_members'] ?? 0 }}</h3>
            <p class="text-danger mb-0 small">Active bans</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row g-3 mb-3">
    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
            <div>
              <h3 class="fw-bold h4">{{ number_format($stats['articles'] ?? 80) }}</h3>
              <span>Total Articles</span>
            </div>
            <div><i class="ti ti-file-text fs-1 text-primary"></i></div>
          </div>
          <div class="d-flex justify-content-between align-items-center small">
            <div class="text-muted"><span class="text-success">+8</span> this month</div>
            <div><a href="{{ route('dashboard.moderator.articles') }}" class="link-primary text-decoration-underline">View</a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
            <div>
              <h3 class="fw-bold h4">{{ number_format($stats['events'] ?? 24) }}</h3>
              <span>Events Organised</span>
            </div>
            <div><i class="ti ti-calendar-event fs-1 text-success"></i></div>
          </div>
          <div class="d-flex justify-content-between align-items-center small">
            <div class="text-muted"><span class="text-success">+3</span> upcoming</div>
            <div><a href="{{ route('events.index') }}" class="link-primary text-decoration-underline">View</a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
            <div>
              <h3 class="fw-bold h4">{{ number_format($stats['jobs'] ?? 39) }}</h3>
              <span>Active Job Listings</span>
            </div>
            <div><i class="ti ti-briefcase fs-1 text-warning"></i></div>
          </div>
          <div class="d-flex justify-content-between align-items-center small">
            <div class="text-muted"><span class="text-warning">5</span> pending approval</div>
            <div><a href="{{ route('jobs.index') }}" class="link-primary text-decoration-underline">View</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent reports -->
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h4 class="mb-0 h5">Recent Reports</h4>
          <a href="{{ route('dashboard.moderator.reports') }}" class="small text-primary text-decoration-underline">View all</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentReports ?? [] as $report)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <p class="mb-1 fw-semibold">{{ $report->reason }}</p>
                <div class="text-secondary small">{{ $report->reportable_type }} · {{ $report->created_at->diffForHumans() }}</div>
              </div>
              <a href="{{ route('dashboard.moderator.reports') }}" class="btn btn-sm btn-warning">Review</a>
            </li>
          @empty
            <li class="list-group-item px-4 py-4 text-center text-secondary">
              <i class="ti ti-circle-check fs-3 d-block mb-2 text-success"></i>
              No pending reports. Community is healthy!
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h4 class="mb-0 h5">Newest Members</h4>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($newMembers ?? [] as $member)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              @if($member->avatar)
                <img src="{{ $member->avatar }}" alt="" class="avatar avatar-sm rounded-circle">
              @else
                <span class="avatar avatar-sm av-1">{{ substr($member->name, 0, 2) }}</span>
              @endif
              <div class="flex-grow-1">
                <p class="mb-0 fw-semibold">{{ $member->name }}</p>
                <div class="text-secondary small">@{{ $member->github_username ?? 'member' }} · joined {{ $member->created_at->diffForHumans() }}</div>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-4 text-center text-secondary">
              No recent members.
            </li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

@endsection
