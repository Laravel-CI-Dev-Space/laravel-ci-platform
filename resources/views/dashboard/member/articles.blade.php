@extends('layouts.dashboard')

@section('title', 'My Articles')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'My Articles']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">My Articles</h1>
          <p class="mb-0 text-muted">All articles you have written on the blog.</p>
        </div>
        <a href="{{ route('blog.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Write Article</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="d-flex gap-2 mb-3 flex-wrap justify-content-between">
        <input type="text" class="form-control" placeholder="Search your articles..." style="max-width:250px;">
        <div class="d-flex gap-2">
          <select class="form-select" style="width:auto">
            <option>All</option>
            <option>Published</option>
            <option>Draft</option>
          </select>
        </div>
      </div>

      <div class="card table-responsive">
        <table class="table mb-0 text-nowrap table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Title</th>
              <th>Level</th>
              <th>Status</th>
              <th>Read time</th>
              <th>Views</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($articles ?? [] as $article)
              <tr class="align-middle">
                <td style="max-width:300px"><a href="{{ route('blog.show', $article) }}" class="text-navy">{{ Str::limit($article->title, 60) }}</a></td>
                <td><span class="badge-pill {{ match(strtolower($article->level ?? '')) { 'intermediate' => 'badge-orange', 'advanced' => '' , default => 'badge-green' } }}">{{ ucfirst($article->level ?? 'Beginner') }}</span></td>
                <td><span class="badge {{ $article->is_published ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">{{ $article->is_published ? 'Published' : 'Draft' }}</span></td>
                <td>{{ $article->reading_time ?? '?' }} min</td>
                <td>{{ number_format($article->views_count ?? 0) }}</td>
                <td>{{ $article->created_at->format('M d, Y') }}</td>
                <td>
                  <a href="{{ route('blog.show', $article) }}" class="me-2"><i class="ti ti-eye"></i></a>
                  <a href="{{ route('blog.edit', $article) }}" class="me-2"><i class="ti ti-edit"></i></a>
                  <form method="POST" action="{{ route('blog.destroy', $article) }}" class="d-inline" onsubmit="return confirm('Delete this article?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 link-danger"><i class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="ti ti-file-text fs-1 d-block mb-2"></i>
                  No articles yet. <a href="{{ route('blog.create') }}">Write your first article!</a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($articles) && $articles instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3">{{ $articles->links() }}</div>
      @endif
    </div>
  </div>

@endsection
