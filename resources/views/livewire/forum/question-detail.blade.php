<div>
    {{-- QUESTION --}}
    <div class="answer-card">
        <div class="d-flex gap-4">
            @livewire('forum.vote-buttons', ['model' => $question], key('vote-q-'.$question->id))

            <div class="flex-grow-1">
                <div>{!! $question->body_html !!}</div>

                <div class="q-tags my-3">
                    @foreach ($question->tags as $tag)
                        <a href="{{ route('forum.index', ['tagId' => $tag->id]) }}" class="tag">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-2">
                    <div class="d-flex gap-3" style="font-size:.85rem">
                        <a href="#" class="text-muted-2">
                            <i class="fa-solid fa-share me-1"></i> Partager
                        </a>
                        @auth
                            <a href="#" class="text-muted-2">
                                <i class="fa-regular fa-bookmark me-1"></i> Sauvegarder
                            </a>
                        @endauth
                    </div>
                    <div class="author-row">
                        @if ($question->user->avatar)
                            <img
                                src="{{ $question->user->avatar }}"
                                class="avatar"
                                alt="{{ $question->user->name }}"
                            />
                        @else
                            <span class="avatar av-1">
                                {{ strtoupper(substr($question->user->name, 0, 2)) }}
                            </span>
                        @endif
                        <div class="meta">
                            <div class="name">{{ $question->user->name }}</div>
                            <div class="sub">{{ $question->created_at?->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ANSWERS --}}
    <h2 class="section-heading my-4" style="font-size:var(--fs-h3)">
        {{ $question->answers_count }} Réponse{{ $question->answers_count !== 1 ? 's' : '' }}
    </h2>

    @foreach ($question->answers->sortByDesc('is_accepted') as $answer)
        <div class="answer-card {{ $answer->is_accepted ? 'accepted' : '' }}">
            @if ($answer->is_accepted)
                <span class="badge-pill badge-green mb-3">
                    <i class="fa-solid fa-circle-check"></i> Solution acceptée
                </span>
            @endif

            <div class="d-flex gap-4">
                @livewire('forum.vote-buttons', ['model' => $answer], key('vote-a-'.$answer->id))

                <div class="flex-grow-1">
                    <div>{!! $answer->body_html !!}</div>

                    <div class="d-flex justify-content-between align-items-center gap-3 pt-2 mt-3"
                         style="border-top:1px solid var(--border)">
                        <div class="d-flex gap-3" style="font-size:.85rem">
                            <a href="#" class="text-muted-2">
                                <i class="fa-solid fa-share me-1"></i> Partager
                            </a>
                            @auth
                                @if (auth()->user()->id === $question->user_id)
                                    @if (! $answer->is_accepted && ! $question->hasAcceptedAnswer())
                                        <button
                                            wire:click="acceptAnswer({{ $answer->id }})"
                                            class="btn btn-ghost btn-sm text-green"
                                        >
                                            <i class="fa-solid fa-check me-1"></i> Accepter
                                        </button>
                                    @elseif ($answer->is_accepted)
                                        <button
                                            wire:click="unacceptAnswer({{ $answer->id }})"
                                            class="btn btn-ghost btn-sm text-muted-2"
                                        >
                                            <i class="fa-solid fa-xmark me-1"></i> Retirer
                                        </button>
                                    @endif
                                @endif
                            @endauth
                        </div>
                        <div class="author-row">
                            @if ($answer->user->avatar)
                                <img
                                    src="{{ $answer->user->avatar }}"
                                    class="avatar avatar-sm"
                                    alt="{{ $answer->user->name }}"
                                />
                            @else
                                <span class="avatar avatar-sm av-3">
                                    {{ strtoupper(substr($answer->user->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="meta">
                                <div class="name">{{ $answer->user->name }}</div>
                                <div class="sub">{{ $answer->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- ANSWER FORM --}}
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-heading mb-0" style="font-size:var(--fs-h3)">Votre réponse</h2>
            @auth
                <button wire:click="toggleAnswerForm" class="btn btn-ghost">
                    <i class="fa-solid fa-{{ $showAnswerForm ? 'chevron-up' : 'pen-to-square' }}"></i>
                    {{ $showAnswerForm ? 'Réduire' : 'Répondre' }}
                </button>
            @endauth
        </div>

        @auth
            @if ($showAnswerForm)
                <form
                    action="{{ route('forum.answers.store', $question) }}"
                    method="POST"
                    class="answer-editor"
                >
                    @csrf
                    <textarea
                        name="body"
                        class="form-control @error('body') is-invalid @enderror"
                        rows="8"
                        placeholder="Partagez votre solution. Markdown et blocs de code sont supportés…"
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-3">
                        <span class="text-muted-2" style="font-size:.82rem">
                            <i class="fa-brands fa-markdown me-1"></i> Markdown supporté · soyez bienveillant
                        </span>
                        <button type="submit" class="btn btn-brand">
                            <i class="fa-solid fa-paper-plane"></i> Publier la réponse
                        </button>
                    </div>
                </form>
            @endif
        @else
            <div class="card-soft p-4 text-center">
                <p class="mb-3">Vous devez être connecté pour répondre.</p>
                <a href="{{ route('login') }}" class="btn btn-brand">
                    <i class="fa-brands fa-github"></i> Se connecter avec GitHub
                </a>
            </div>
        @endauth
    </div>
</div>
