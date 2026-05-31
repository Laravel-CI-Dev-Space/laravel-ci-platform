@extends('layouts.dashboard')

@section('title', 'Saved Jobs')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'Saved Jobs']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Saved Jobs</h1>
          <p class="mb-0 text-muted">Jobs you have bookmarked for later.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="ti ti-briefcase me-1"></i> Browse Jobs</a>
      </div>
    </div>
  </div>

  <div class="row g-3">
    @forelse($favorites ?? [] as $job)
      <div class="col-12">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex gap-3 align-items-start">
              <div class="company-logo {{ $job->logo_class ?? 'cl-1' }}">{{ $job->logo_text ?? 'CO' }}</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between gap-2">
                  <div>
                    <h3 class="mb-1" style="font-size:1.1rem">
                      <a href="{{ route('jobs.show', $job) }}" class="text-navy">{{ $job->title }}</a>
                    </h3>
                    <div class="text-secondary small">
                      <strong>{{ $job->company }}</strong> · {{ $job->location }}
                      @if($job->remote) · <span class="badge bg-primary-subtle text-primary">Remote</span> @endif
                    </div>
                  </div>
                  <form method="POST" action="{{ route('jobs.unsave', $job) }}" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-link link-danger p-0" aria-label="Remove from saved">
                      <i class="ti ti-heart-off"></i>
                    </button>
                  </form>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <span class="badge-pill badge-soft">{{ $job->contract_type }}</span>
                  <span class="badge-pill badge-soft">{{ $job->level }}</span>
                  @foreach(($job->tags ?? []) as $tag)
                    <span class="tag">{{ $tag }}</span>
                  @endforeach
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <span class="salary">{{ $job->salary_range }}</span>
                  <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm">Apply <i class="ti ti-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-heart fs-1 d-block mb-3"></i>
            <h3 class="h5">No saved jobs yet</h3>
            <p class="mb-3">Save jobs you're interested in by clicking the heart icon.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-primary">Browse Jobs</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

@endsection
