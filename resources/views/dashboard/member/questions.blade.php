@extends('layouts.dashboard')

@section('title', 'My Questions')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'My Questions']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">My Questions</h1>
          <p class="mb-0 text-muted">All questions you have asked on the forum.</p>
        </div>
        <a href="{{ route('forum.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Ask a Question</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="d-flex gap-2 mb-3 flex-wrap justify-content-between">
        <input type="text" class="form-control" placeholder="Search your questions..." style="max-width:250px;">
        <div class="d-flex gap-2">
          <select class="form-select" style="width:auto">
            <option>All</option>
            <option>Solved</option>
            <option>Open</option>
          </select>
        </div>
      </div>

      <div class="card table-responsive">
        <table class="table mb-0 text-nowrap table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Question</th>
              <th>Tags</th>
              <th>Votes</th>
              <th>Answers</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($questions ?? [] as $question)
              <tr class="align-middle">
                <td style="max-width:300px"><a href="{{ route('forum.show', $question) }}" class="text-navy">{{ Str::limit($question->title, 60) }}</a></td>
                <td>
                  <div class="d-flex gap-1 flex-wrap">
                    @foreach($question->tags->take(2) as $tag)
                      <span class="tag">{{ $tag->name }}</span>
                    @endforeach
                  </div>
                </td>
                <td>{{ $question->votes_count }}</td>
                <td>{{ $question->answers_count }}</td>
                <td><span class="badge {{ $question->is_solved ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">{{ $question->is_solved ? 'Solved' : 'Open' }}</span></td>
                <td>{{ $question->created_at->format('M d, Y') }}</td>
                <td>
                  <a href="{{ route('forum.show', $question) }}" class="me-2"><i class="ti ti-eye"></i></a>
                  <a href="{{ route('forum.edit', $question) }}" class="me-2"><i class="ti ti-edit"></i></a>
                  <form method="POST" action="{{ route('forum.destroy', $question) }}" class="d-inline" onsubmit="return confirm('Delete this question?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 link-danger"><i class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="ti ti-message-circle-question fs-1 d-block mb-2"></i>
                  No questions yet. <a href="{{ route('forum.create') }}">Ask your first question!</a>
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <td class="border-bottom-0" colspan="7">
                @if(isset($questions) && $questions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                  {{ $questions->links() }}
                @endif
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

@endsection
