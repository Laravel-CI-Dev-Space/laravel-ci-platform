@extends('layouts.dashboard')

@section('title', 'Moderate Questions')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Moderator', 'href' => route('dashboard.moderator.overview')], ['label' => 'Questions']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Moderate Questions</h1>
          <p class="mb-0 text-muted">Review, pin, close or delete forum questions.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="d-flex gap-2 mb-3 flex-wrap justify-content-between">
        <input type="text" class="form-control" placeholder="Search questions..." style="max-width:250px;">
        <div class="d-flex gap-2">
          <select class="form-select" style="width:auto">
            <option>All</option>
            <option>Open</option>
            <option>Solved</option>
            <option>Pinned</option>
            <option>Reported</option>
          </select>
          <button class="btn btn-outline-secondary">
            <i class="ti ti-filter"></i> Filter
          </button>
        </div>
      </div>

      <div class="card table-responsive">
        <table class="table mb-0 table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Question</th>
              <th>Author</th>
              <th>Votes</th>
              <th>Answers</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($questions ?? [] as $question)
              <tr class="align-middle">
                <td style="max-width:280px">
                  @if($question->is_pinned)
                    <i class="ti ti-pin text-orange me-1"></i>
                  @endif
                  <a href="{{ route('forum.show', $question) }}" class="text-navy">{{ Str::limit($question->title, 55) }}</a>
                </td>
                <td>{{ $question->author->name ?? 'Unknown' }}</td>
                <td>{{ $question->votes_count }}</td>
                <td>{{ $question->answers_count }}</td>
                <td>
                  <span class="badge {{ $question->is_solved ? 'bg-success-subtle text-success' : ($question->reports_count > 0 ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}">
                    {{ $question->is_solved ? 'Solved' : ($question->reports_count > 0 ? 'Reported' : 'Open') }}
                  </span>
                </td>
                <td>{{ $question->created_at->format('M d, Y') }}</td>
                <td class="text-nowrap">
                  <a href="{{ route('forum.show', $question) }}" class="me-1" title="View"><i class="ti ti-eye"></i></a>
                  <form method="POST" action="{{ route('dashboard.moderator.questions.pin', $question) }}" class="d-inline" title="{{ $question->is_pinned ? 'Unpin' : 'Pin' }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 me-1 {{ $question->is_pinned ? 'text-orange' : '' }}"><i class="ti ti-pin"></i></button>
                  </form>
                  <form method="POST" action="{{ route('forum.destroy', $question) }}" class="d-inline" onsubmit="return confirm('Delete this question?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 link-danger"><i class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">No questions found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($questions) && $questions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3">{{ $questions->links() }}</div>
      @endif
    </div>
  </div>

@endsection
