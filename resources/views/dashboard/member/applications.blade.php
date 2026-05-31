@extends('layouts.dashboard')

@section('title', 'My Applications')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'Applications']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">My Job Applications</h1>
          <p class="mb-0 text-muted">Track all your job applications.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="ti ti-briefcase me-1"></i> Browse Jobs</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-responsive">
        <table class="table mb-0 table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Position</th>
              <th>Company</th>
              <th>Contract</th>
              <th>Applied</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applications ?? [] as $application)
              <tr class="align-middle">
                <td><a href="{{ route('jobs.show', $application->job) }}" class="text-navy fw-semibold">{{ $application->job->title }}</a></td>
                <td>{{ $application->job->company }}</td>
                <td>{{ $application->job->contract_type }}</td>
                <td>{{ $application->created_at->format('M d, Y') }}</td>
                <td>
                  @php
                    $statusMap = [
                      'pending'  => ['class' => 'warning', 'label' => 'Pending'],
                      'viewed'   => ['class' => 'info', 'label' => 'Viewed'],
                      'accepted' => ['class' => 'success', 'label' => 'Accepted'],
                      'rejected' => ['class' => 'danger', 'label' => 'Rejected'],
                    ];
                    $status = $statusMap[$application->status ?? 'pending'] ?? ['class' => 'secondary', 'label' => ucfirst($application->status ?? 'Pending')];
                  @endphp
                  <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }}">{{ $status['label'] }}</span>
                </td>
                <td>
                  <a href="{{ route('jobs.show', $application->job) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-eye"></i> View Job
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-secondary">
                  <i class="ti ti-briefcase fs-1 d-block mb-2"></i>
                  No applications yet. <a href="{{ route('jobs.index') }}">Browse open positions!</a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($applications) && $applications instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3">{{ $applications->links() }}</div>
      @endif
    </div>
  </div>

@endsection
