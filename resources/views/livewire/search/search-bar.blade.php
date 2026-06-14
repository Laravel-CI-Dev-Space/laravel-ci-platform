<div class="search-bar-wrapper" x-data @click.outside="$wire.close()">
    <div class="search-input-wrapper">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            wire:keydown.enter="search"
            wire:keydown.escape="close"
            placeholder="Rechercher questions, articles, offres..."
            class="search-input"
            autocomplete="off"
        />
        @if ($isLoading)
            <span class="search-spinner">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
            </span>
        @elseif (strlen($query) > 0)
            <button wire:click="$set('query', '')" class="search-clear" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        @endif
    </div>

    @if ($isOpen && $query)
        <div class="search-dropdown">
            {{-- Questions section --}}
            @if (count($results['questions'] ?? []) > 0)
                <div class="search-section">
                    <div class="search-section-label">
                        <i class="fa-solid fa-comments"></i> Forum
                    </div>
                    @foreach ($results['questions'] as $question)
                        <button wire:click="selectResult('{{ route('forum.show', $question->slug) }}')"
                                class="search-result-item" type="button">
                            <div class="result-icon result-forum">
                                <i class="fa-solid fa-circle-question"></i>
                            </div>
                            <div class="result-content">
                                <div class="result-title">{{ Str::limit($question->title, 60) }}</div>
                                <div class="result-meta">
                                    {{ $question->answers_count }} réponses ·
                                    {{ $question->votes_score }} votes
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Articles section --}}
            @if (count($results['articles'] ?? []) > 0)
                <div class="search-section">
                    <div class="search-section-label">
                        <i class="fa-solid fa-book-open"></i> Blog
                    </div>
                    @foreach ($results['articles'] as $article)
                        <button wire:click="selectResult('{{ route('blog.show', $article->slug) }}')"
                                class="search-result-item" type="button">
                            <div class="result-icon result-blog">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div class="result-content">
                                <div class="result-title">{{ Str::limit($article->title, 60) }}</div>
                                <div class="result-meta">
                                    {{ $article->level->label() }} ·
                                    {{ $article->author?->name }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Jobs section --}}
            @if (count($results['jobs'] ?? []) > 0)
                <div class="search-section">
                    <div class="search-section-label">
                        <i class="fa-solid fa-briefcase"></i> Jobs
                    </div>
                    @foreach ($results['jobs'] as $job)
                        <button wire:click="selectResult('{{ route('jobs.show', $job->slug) }}')"
                                class="search-result-item" type="button">
                            <div class="result-icon result-job">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div class="result-content">
                                <div class="result-title">{{ Str::limit($job->title, 60) }}</div>
                                <div class="result-meta">
                                    {{ $job->company?->name }} ·
                                    {{ $job->contract_type->label() }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- No results --}}
            @if (($results['total'] ?? 0) === 0 && ! $isLoading)
                <div class="search-empty">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Aucun résultat pour "{{ $query }}"</span>
                </div>
            @endif

            {{-- Footer --}}
            @if (($results['total'] ?? 0) > 0)
                <div class="search-footer">
                    <button wire:click="search" class="search-see-all" type="button">
                        Voir tous les résultats pour "{{ $query }}"
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
