<div>
    {{-- ═══════════════════════════════════════════
         EN-TÊTE
         ═══════════════════════════════════════════ --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:4px;height:2rem;background:var(--orange,#e8590c);border-radius:2px;flex-shrink:0"></div>
        <h2 style="font-size:var(--fs-h3); margin:0">
            Commentaires
            <span class="text-muted-2" style="font-size:1rem; font-weight:400">
                ({{ $article->comments_count }})
            </span>
        </h2>
    </div>

    {{-- ═══════════════════════════════════════════
         FORMULAIRE — nouveau commentaire
         ═══════════════════════════════════════════ --}}
    @auth
        @php $me = auth()->user(); @endphp
        <div class="card-soft p-4 mb-5">
            <div class="d-flex gap-3">
                {{-- Avatar --}}
                @if ($me->avatar)
                    <img src="{{ $me->avatar }}" class="avatar avatar-sm flex-shrink-0" alt="{{ $me->name }}" />
                @else
                    <span class="avatar avatar-sm av-1 flex-shrink-0">
                        {{ strtoupper(substr($me->name, 0, 2)) }}
                    </span>
                @endif

                <div class="flex-grow-1">
                    <textarea
                        wire:model.live="body"
                        class="form-control mb-2 @error('body') is-invalid @enderror"
                        rows="3"
                        placeholder="Laissez un commentaire constructif…"
                        maxlength="500"
                        style="resize:none; border-radius:var(--radius,.75rem)"
                    ></textarea>
                    @error('body')
                        <div class="invalid-feedback d-block mb-1">{{ $message }}</div>
                    @enderror

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted-2" style="font-size:.78rem">
                            {{ strlen($body) }} / 500
                        </span>
                        <button
                            wire:click="addComment"
                            wire:loading.attr="disabled"
                            class="btn btn-brand btn-sm"
                        >
                            <span wire:loading wire:target="addComment">
                                <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                            </span>
                            <span wire:loading.remove wire:target="addComment">
                                <i class="fa-solid fa-paper-plane me-1"></i>
                            </span>
                            Commenter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card-soft p-4 text-center mb-5">
            <i class="fa-regular fa-comment fa-2x mb-3 text-muted-2"></i>
            <p class="mb-3">Connectez-vous pour laisser un commentaire.</p>
            <a href="{{ route('login') }}" class="btn btn-brand">
                <i class="fa-brands fa-github me-1"></i>Se connecter
            </a>
        </div>
    @endauth

    {{-- ═══════════════════════════════════════════
         LISTE DES COMMENTAIRES
         ═══════════════════════════════════════════ --}}
    @forelse ($comments as $comment)
        <div class="comment" id="comment-{{ $comment->id }}">
            {{-- Avatar --}}
            @if ($comment->user->avatar)
                <img src="{{ $comment->user->avatar }}"
                     class="avatar avatar-sm"
                     alt="{{ $comment->user->name }}" />
            @else
                <span class="avatar av-{{ ($comment->id % 6) + 1 }}">
                    {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                </span>
            @endif

            <div class="c-body">
                {{-- En-tête --}}
                <div class="c-head">
                    <span class="name" style="font-weight:600">{{ $comment->user->name }}</span>
                    <span class="sub" style="font-size:.78rem; color:var(--muted)">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Corps --}}
                <p class="mb-2">{{ $comment->body }}</p>

                {{-- Actions --}}
                <div class="d-flex align-items-center gap-3" style="font-size:.8rem">
                    @auth
                        {{-- Répondre --}}
                        @if ($replyingTo !== $comment->id)
                            <button
                                wire:click="startReply({{ $comment->id }})"
                                class="btn btn-ghost btn-sm py-0 px-1"
                                style="font-size:.8rem; color:var(--muted)"
                            >
                                <i class="fa-regular fa-comment me-1"></i>Répondre
                            </button>
                        @else
                            <button
                                wire:click="cancelReply"
                                class="btn btn-ghost btn-sm py-0 px-1"
                                style="font-size:.8rem; color:var(--orange)"
                            >
                                <i class="fa-solid fa-xmark me-1"></i>Annuler
                            </button>
                        @endif

                        {{-- Supprimer (auteur ou modérateur) --}}
                        @if (auth()->id() === $comment->user_id || auth()->user()->hasAnyRole(['admin','moderator','super-admin']))
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Supprimer ce commentaire ?"
                                class="btn btn-ghost btn-sm py-0 px-1"
                                style="font-size:.8rem; color:#e74c3c"
                            >
                                <i class="fa-solid fa-trash me-1"></i>Supprimer
                            </button>
                        @endif
                    @endauth
                </div>

                {{-- Formulaire de réponse --}}
                @if ($replyingTo === $comment->id)
                    <div class="mt-3 d-flex gap-2">
                        @auth
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}"
                                     class="avatar avatar-sm flex-shrink-0"
                                     alt="{{ auth()->user()->name }}" />
                            @else
                                <span class="avatar avatar-sm av-1 flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </span>
                            @endif
                        @endauth

                        <div class="flex-grow-1">
                            <textarea
                                wire:model.live="replyBody"
                                class="form-control mb-2 @error('replyBody') is-invalid @enderror"
                                rows="2"
                                placeholder="Votre réponse à {{ $comment->user->name }}…"
                                maxlength="500"
                                style="resize:none; border-radius:var(--radius,.75rem); font-size:.88rem"
                                autofocus
                            ></textarea>
                            @error('replyBody')
                                <div class="invalid-feedback d-block mb-1" style="font-size:.82rem">{{ $message }}</div>
                            @enderror

                            <div class="d-flex justify-content-end gap-2">
                                <button wire:click="cancelReply" class="btn btn-ghost btn-sm">
                                    Annuler
                                </button>
                                <button
                                    wire:click="addReply"
                                    wire:loading.attr="disabled"
                                    class="btn btn-brand btn-sm"
                                >
                                    <span wire:loading wire:target="addReply">
                                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="addReply">
                                        <i class="fa-solid fa-reply me-1"></i>
                                    </span>
                                    Répondre
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Réponses (1 niveau) ─────────────────── --}}
                @if ($comment->replies->isNotEmpty())
                    <div class="mt-3" style="border-left:2px solid var(--border,#eef0f4); padding-left:1rem">
                        @foreach ($comment->replies->where('is_hidden', false) as $reply)
                            <div class="comment" id="comment-{{ $reply->id }}" style="margin-bottom:.75rem">
                                @if ($reply->user->avatar)
                                    <img src="{{ $reply->user->avatar }}"
                                         class="avatar avatar-sm"
                                         alt="{{ $reply->user->name }}" />
                                @else
                                    <span class="avatar avatar-sm av-{{ ($reply->id % 6) + 1 }}">
                                        {{ strtoupper(substr($reply->user->name, 0, 2)) }}
                                    </span>
                                @endif

                                <div class="c-body">
                                    <div class="c-head">
                                        <span class="name" style="font-weight:600">{{ $reply->user->name }}</span>
                                        <span class="sub" style="font-size:.75rem; color:var(--muted)">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="mb-1" style="font-size:.9rem">{{ $reply->body }}</p>

                                    {{-- Supprimer la réponse --}}
                                    @auth
                                        @if (auth()->id() === $reply->user_id || auth()->user()->hasAnyRole(['admin','moderator','super-admin']))
                                            <button
                                                wire:click="deleteComment({{ $reply->id }})"
                                                wire:confirm="Supprimer cette réponse ?"
                                                class="btn btn-ghost btn-sm py-0 px-1 mt-1"
                                                style="font-size:.75rem; color:#e74c3c"
                                            >
                                                <i class="fa-solid fa-trash me-1"></i>Supprimer
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-4" style="color:var(--muted)">
            <i class="fa-regular fa-comments fa-2x mb-2 d-block"></i>
            <p class="mb-0" style="font-size:.9rem">
                Aucun commentaire pour le moment. Soyez le premier à réagir !
            </p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($comments->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $comments->links() }}
        </div>
    @endif
</div>
