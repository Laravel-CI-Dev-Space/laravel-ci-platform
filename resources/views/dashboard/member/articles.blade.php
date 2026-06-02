@extends('layouts.dashboard')

@section('title', 'Mes articles — Laravel CI')

@section('content')

  <x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes articles'],
  ]" />

  {{-- En-tête --}}
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h1 class="fs-3 fw-bold mb-1">Mes articles</h1>
        <p class="mb-0 text-muted">Gérez vos articles du blog et soumettez-les pour validation.</p>
      </div>
      <a href="{{ route('blog.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Rédiger un article
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Légende des statuts --}}
  <div class="d-flex gap-2 flex-wrap mb-3">
    <span class="badge bg-secondary-subtle text-secondary">Brouillon</span>
    <span class="badge bg-warning-subtle text-warning">En attente de validation</span>
    <span class="badge bg-success-subtle text-success">Publié</span>
    <span class="badge bg-danger-subtle text-danger">Rejeté</span>
  </div>

  {{-- Tableau --}}
  <div class="row">
    <div class="col-12">
      <div class="card table-responsive">
        <table class="table mb-0 table-hover align-middle">
          <thead class="table-light border-light">
            <tr>
              <th style="min-width:260px">Titre</th>
              <th>Niveau</th>
              <th>Statut</th>
              <th>Lecture</th>
              <th>Vues</th>
              <th>Date</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($articles as $article)
              @php
                /* Temps de lecture estimé à 200 mots / min */
                $wordCount   = str_word_count(strip_tags($article->body ?? ''));
                $readingTime = max(1, (int) round($wordCount / 200));

                /* Config badge statut */
                $statusBadge = match ($article->status) {
                    'published' => ['bg-success-subtle text-success', 'Publié',              'ti-circle-check'],
                    'pending'   => ['bg-warning-subtle text-warning', 'En attente',          'ti-clock'],
                    'rejected'  => ['bg-danger-subtle text-danger',   'Rejeté',              'ti-x'],
                    default     => ['bg-secondary-subtle text-secondary', 'Brouillon',       'ti-file'],
                };

                /* Config badge niveau */
                $levelBadge  = match ($article->level ?? 'beginner') {
                    'intermediate' => ['badge-orange', 'Intermédiaire'],
                    'advanced'     => ['',             'Avancé'],
                    default        => ['badge-green',  'Débutant'],
                };
                $levelStyle  = ($article->level === 'advanced')
                    ? 'background:#fdeaec;color:var(--level-advanced)'
                    : '';
              @endphp
              <tr>
                {{-- Titre --}}
                <td>
                  <div class="fw-semibold" style="max-width:280px">
                    @if ($article->status === 'published')
                      <a href="{{ route('blog.show', $article->slug) }}"
                         class="text-navy text-decoration-none">
                        {{ Str::limit($article->title, 55) }}
                      </a>
                    @else
                      <span class="text-dark">{{ Str::limit($article->title, 55) }}</span>
                    @endif
                  </div>
                  @if ($article->tags->isNotEmpty())
                    <div class="mt-1">
                      @foreach ($article->tags->take(3) as $tag)
                        <span class="badge bg-light text-secondary border" style="font-size:.72rem">
                          {{ $tag->name }}
                        </span>
                      @endforeach
                    </div>
                  @endif
                </td>

                {{-- Niveau --}}
                <td>
                  <span class="badge-pill {{ $levelBadge[0] }}" style="{{ $levelStyle }}">
                    {{ $levelBadge[1] }}
                  </span>
                </td>

                {{-- Statut --}}
                <td>
                  <span class="badge {{ $statusBadge[0] }}">
                    <i class="ti {{ $statusBadge[2] }} me-1"></i>{{ $statusBadge[1] }}
                  </span>
                  @if ($article->status === 'rejected' && $article->rejection_reason)
                    <div class="text-danger mt-1" style="font-size:.75rem; max-width:200px"
                         title="{{ $article->rejection_reason }}">
                      <i class="ti ti-alert-triangle me-1"></i>
                      {{ Str::limit($article->rejection_reason, 50) }}
                    </div>
                  @endif
                </td>

                {{-- Temps de lecture --}}
                <td class="text-nowrap">
                  <i class="ti ti-clock text-muted me-1"></i>{{ $readingTime }} min
                </td>

                {{-- Vues --}}
                <td class="text-nowrap">
                  <i class="ti ti-eye text-muted me-1"></i>
                  {{ number_format($article->views_count ?? 0) }}
                </td>

                {{-- Date --}}
                <td class="text-nowrap text-muted" style="font-size:.875rem">
                  {{ $article->created_at->format('d M Y') }}
                </td>

                {{-- Actions --}}
                <td class="text-center text-nowrap">
                  {{-- Voir (publié seulement) --}}
                  @if ($article->status === 'published')
                    <a href="{{ route('blog.show', $article->slug) }}"
                       class="btn btn-link btn-sm p-1 text-muted"
                       title="Voir l'article publié" target="_blank">
                      <i class="ti ti-eye fs-5"></i>
                    </a>
                  @endif

                  {{-- Soumettre (brouillon ou rejeté) --}}
                  @if (in_array($article->status, ['draft', 'rejected']))
                    <form method="POST"
                          action="{{ route('blog.articles.submit', $article) }}"
                          class="d-inline">
                      @csrf
                      <button type="submit"
                              class="btn btn-link btn-sm p-1 text-warning"
                              title="Soumettre pour validation"
                              onclick="return confirm('Soumettre cet article pour validation ?')">
                        <i class="ti ti-send fs-5"></i>
                      </button>
                    </form>
                  @endif

                  {{-- En attente → indicateur non cliquable --}}
                  @if ($article->status === 'pending')
                    <span class="text-warning" title="En attente de validation">
                      <i class="ti ti-hourglass fs-5"></i>
                    </span>
                  @endif

                  {{-- Supprimer (toujours disponible sauf si publié) --}}
                  @if ($article->status !== 'published')
                    <form method="POST"
                          action="{{ route('blog.articles.destroy', $article) }}"
                          class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit"
                              class="btn btn-link btn-sm p-1 link-danger"
                              title="Supprimer"
                              onclick="return confirm('Supprimer cet article définitivement ?')">
                        <i class="ti ti-trash fs-5"></i>
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="ti ti-file-text d-block mb-2" style="font-size:2.5rem"></i>
                  <p class="mb-3">Vous n'avez pas encore d'article.</p>
                  <a href="{{ route('blog.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> Rédiger votre premier article
                  </a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if ($articles->hasPages())
        <div class="mt-3">{{ $articles->links() }}</div>
      @endif
    </div>
  </div>

@endsection
