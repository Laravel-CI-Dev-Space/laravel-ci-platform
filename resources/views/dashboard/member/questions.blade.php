@extends('layouts.dashboard')

@section('title', 'Mes Questions')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'Mes Questions']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Mes Questions</h1>
          <p class="mb-0 text-muted">Toutes les questions que vous avez posées sur le forum.</p>
        </div>
        <a href="{{ route('forum.ask') }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i> Poser une question
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-responsive">
        <table class="table mb-0 text-nowrap table-hover">
          <thead class="table-light border-light">
            <tr>
              <th>Question</th>
              <th>Tags</th>
              <th>Votes</th>
              <th>Réponses</th>
              <th>Statut</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($questions as $question)
              <tr class="align-middle">
                <td style="max-width:300px">
                  <a href="{{ route('forum.show', $question) }}" class="text-navy">
                    {{ Str::limit($question->title, 60) }}
                  </a>
                </td>
                <td>
                  <div class="d-flex gap-1 flex-wrap">
                    @foreach($question->tags->take(2) as $tag)
                      <span class="badge bg-light text-dark border">{{ $tag->name }}</span>
                    @endforeach
                  </div>
                </td>
                <td>{{ $question->votes_score }}</td>
                <td>{{ $question->answers_count }}</td>
                <td>
                  @if($question->status === 'hidden')
                    <span class="badge bg-warning-subtle text-warning">Caché</span>
                  @elseif($question->hasAcceptedAnswer())
                    <span class="badge bg-success-subtle text-success">Résolu</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary">Ouvert</span>
                  @endif
                </td>
                <td>{{ $question->created_at->format('d/m/Y') }}</td>
                <td>
                  <a href="{{ route('forum.show', $question) }}" class="me-2" title="Voir">
                    <i class="ti ti-eye"></i>
                  </a>
                  <form
                    method="POST"
                    action="{{ route('forum.destroy', $question) }}"
                    class="d-inline"
                    onsubmit="return confirm('Supprimer cette question ?')"
                  >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 link-danger" title="Supprimer">
                      <i class="ti ti-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="ti ti-message-circle-question fs-1 d-block mb-2"></i>
                  Aucune question pour l'instant.
                  <a href="{{ route('forum.ask') }}">Posez votre première question !</a>
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              <td class="border-bottom-0" colspan="7">
                {{ $questions->links() }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

@endsection
