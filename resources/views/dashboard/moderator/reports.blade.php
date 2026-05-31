@extends('layouts.dashboard')

@section('title', 'Reports')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Moderator', 'href' => route('dashboard.moderator.overview')], ['label' => 'Reports']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Reports</h1>
          <p class="mb-0 text-muted">Review and manage community reports.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- LIVEWIRE: @livewire('dashboard.report-list') --}}

  <!-- Stats -->
  <div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="card h-100">
        <div class="card-body p-4">
          <h6 class="mb-4">Pending</h6>
          <h3 class="mb-1 fw-bold">{{ $stats['pending'] ?? 0 }}</h3>
          <p class="mb-0 text-warning small"><i class="ti ti-alert-triangle"></i> Awaiting review</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="card h-100">
        <div class="card-body p-4">
          <h6 class="mb-4">Resolved Today</h6>
          <h3 class="mb-1 fw-bold">{{ $stats['resolved_today'] ?? 0 }}</h3>
          <p class="mb-0 text-success small"><i class="ti ti-check"></i> Handled</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="card h-100">
        <div class="card-body p-4">
          <h6 class="mb-4">Dismissed</h6>
          <h3 class="mb-1 fw-bold">{{ $stats['dismissed'] ?? 0 }}</h3>
          <p class="mb-0 text-secondary small"><i class="ti ti-x"></i> Not actionable</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="card h-100">
        <div class="card-body p-4">
          <h6 class="mb-4">Total Reports</h6>
          <h3 class="mb-1 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
          <p class="mb-0 text-secondary small"><i class="ti ti-flag"></i> All time</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Reports list -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body p-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-3 gap-2">
            <h2 class="mb-0 fs-5">Report Queue</h2>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm">
                <option>All reports</option>
                <option>Pending</option>
                <option>Resolved</option>
                <option>Dismissed</option>
              </select>
              <select class="form-select form-select-sm">
                <option>All types</option>
                <option>Question</option>
                <option>Answer</option>
                <option>Article</option>
                <option>Comment</option>
              </select>
            </div>
          </div>

          <div class="list-group list-group-flush">
            @forelse($reports ?? [] as $report)
              <div class="list-group-item p-4 d-flex align-items-start gap-3">
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                      <span class="badge bg-{{ match($report->status ?? 'pending') { 'pending' => 'warning', 'resolved' => 'success', 'dismissed' => 'secondary', default => 'secondary' } }}-subtle text-{{ match($report->status ?? 'pending') { 'pending' => 'warning', 'resolved' => 'success', 'dismissed' => 'secondary', default => 'secondary' } }}">{{ ucfirst($report->status ?? 'Pending') }}</span>
                      <span class="badge bg-light text-dark ms-1">{{ class_basename($report->reportable_type ?? 'Question') }}</span>
                    </div>
                    <small class="text-secondary">{{ $report->created_at?->diffForHumans() ?? '2 hours ago' }}</small>
                  </div>
                  <h6 class="mb-1">Reason: {{ $report->reason ?? 'Spam' }}</h6>
                  @if($report->description ?? null)
                    <p class="mb-2 text-secondary small">{{ $report->description }}</p>
                  @endif
                  <div class="d-flex gap-2 mt-3">
                    @if(($report->status ?? 'pending') === 'pending')
                      <form method="POST" action="{{ route('dashboard.moderator.reports.resolve', $report->id ?? 1) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="ti ti-check me-1"></i> Resolve</button>
                      </form>
                      <form method="POST" action="{{ route('dashboard.moderator.reports.dismiss', $report->id ?? 1) }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-x me-1"></i> Dismiss</button>
                      </form>
                    @endif
                    @if($report->reportable_type ?? null)
                      <a href="#" class="btn btn-outline-primary btn-sm"><i class="ti ti-eye me-1"></i> View Content</a>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              <div class="list-group-item p-5 text-center text-secondary">
                <i class="ti ti-circle-check fs-1 d-block mb-3 text-success"></i>
                <h3 class="h5">All clear!</h3>
                <p class="mb-0">No reports pending review. The community is healthy.</p>
              </div>
            @endforelse
          </div>

          @if(isset($reports) && $reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-3">{{ $reports->links() }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection
