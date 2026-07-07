<div id="chat-widget-container"
     x-data="chatDrag()"
     x-init="init()"
     :style="`position:fixed; bottom:${bottom}px; right:${right}px; z-index:9997;`">

    {{-- ── Bouton flottant (drag handle) ──────────────────── --}}
    <button class="chat-fab {{ $isOpen ? 'chat-fab--open' : '' }}"
            aria-label="Assistant IA Laravel CI"
            @mousedown="startDrag($event)"
            @touchstart.passive="startDrag($event)"
            @click="if (!_moved) $wire.toggle()">
        @if ($isOpen)
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        @else
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Assistant"
                 style="width:32px;height:32px;object-fit:contain;border-radius:50%">
        @endif
    </button>

    {{-- ── Fenêtre de chat ─────────────────────────────────── --}}
    @if ($isOpen)
        <div class="chat-window" role="dialog" aria-label="Assistant IA Laravel CI">

            {{-- En-tête --}}
            <div class="chat-header">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                         style="width:28px;height:28px;object-fit:contain">
                    <div>
                        <div class="fw-semibold" style="font-size:.9rem;line-height:1.2">Assistant Laravel CI</div>
                        <div style="font-size:.72rem;opacity:.8">Laravel · PHP · Plateforme</div>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if ($sessionId)
                        <button wire:click="newSession" class="chat-icon-btn" title="Nouvelle conversation">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    @endif
                    <button wire:click="toggle" class="chat-icon-btn" title="Fermer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            @auth
                {{-- Barre de budget --}}
                @if ($budget)
                    <div class="chat-budget">
                        <div class="d-flex justify-content-between align-items-center mb-1"
                             style="font-size:.7rem;color:#888">
                            <span>{{ number_format($budget['used']) }} / {{ number_format($budget['limit']) }} tokens</span>
                            <span>{{ $budget['remaining'] > 0 ? number_format($budget['remaining']).' restants' : 'Épuisé' }}</span>
                        </div>
                        <div class="chat-budget-bar">
                            <div class="chat-budget-fill {{ $budget['percent'] >= 90 ? 'chat-budget-fill--danger' : '' }}"
                                 style="width:{{ min($budget['percent'], 100) }}%"></div>
                        </div>
                    </div>
                @endif

                {{-- Corps : messages --}}
                <div class="chat-body"
                     id="chat-body"
                     x-on:chat-scroll-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })">

                    {{-- Message de bienvenue si aucun échange --}}
                    @if (empty($messages))
                        <div class="chat-welcome">
                            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" class="chat-welcome__mascot">
                            <p class="chat-welcome__title">Bonjour 👋</p>
                            <p class="chat-welcome__sub">Je suis l'assistant de la plateforme.<br>Posez-moi une question sur Laravel, PHP ou la communauté.</p>
                        </div>
                    @endif

                    {{-- Bulles de messages --}}
                    @foreach ($messages as $msg)
                        <div class="chat-bubble chat-bubble--{{ $msg['role'] }}">
                            @if ($msg['role'] === 'assistant')
                                <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                                     class="chat-bubble-avatar">
                            @endif
                            <div class="chat-bubble-content" data-role="{{ $msg['role'] }}">
                                {!! chatMarkdown($msg['content']) !!}
                            </div>
                        </div>
                    @endforeach

                    {{-- Indicateur de frappe --}}
                    @if ($loading)
                        <div class="chat-bubble chat-bubble--assistant">
                            <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                                 class="chat-bubble-avatar">
                            <div class="chat-bubble-content">
                                <div class="chat-typing">
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Message d'erreur --}}
                    @if ($error)
                        <div class="chat-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            {{ $error }}
                        </div>
                    @endif
                </div>

                {{-- Footer : saisie --}}
                <div class="chat-footer">
                    <div class="chat-input-row">
                        <textarea
                            wire:model="input"
                            class="chat-input"
                            placeholder="Votre question…"
                            rows="1"
                            @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage() }"
                            @input="$el.style.height='auto'; $el.style.height=Math.min($el.scrollHeight,120)+'px'"
                            :disabled="{{ $loading ? 'true' : 'false' }}"
                            wire:loading.attr="disabled"
                        ></textarea>
                        <button class="chat-send-btn"
                                wire:click="sendMessage"
                                wire:loading.attr="disabled"
                                :disabled="{{ empty(trim($input)) || $loading ? 'true' : 'false' }}"
                                title="Envoyer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div style="text-align:center;font-size:.65rem;color:#bbb;margin-top:.3rem">
                        Entrée pour envoyer · Maj+Entrée pour sauter une ligne
                    </div>
                </div>

            @else
                {{-- Non connecté --}}
                <div class="chat-coming-soon">
                    <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" class="chat-coming-soon__mascot">
                    <p class="chat-coming-soon__title">Connectez-vous</p>
                    <p class="chat-coming-soon__sub">L'assistant est réservé aux membres de la communauté.</p>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-brand mt-2">Se connecter</a>
                </div>
            @endauth

        </div>
    @endif

</div>

<style>
/* ── Bouton flottant ─────────────────────────────────── */
.chat-fab {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: var(--orange, #e8580a);
    color: #fff;
    border: none;
    cursor: grab;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(232,88,10,.4);
    transition: transform .2s, box-shadow .2s;
    -webkit-user-select: none;
    user-select: none;
}
.chat-fab:active { cursor: grabbing; }
.chat-fab:hover  { transform: scale(1.08); box-shadow: 0 6px 20px rgba(232,88,10,.5); }
.chat-fab--open  { background: #555; box-shadow: 0 4px 16px rgba(0,0,0,.2); }

/* ── Fenêtre ─────────────────────────────────────────── */
.chat-window {
    position: absolute;
    bottom: 4.2rem;
    right: 0;
    width: 360px;
    max-height: 560px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: chatSlideUp .2s ease;
}
@keyframes chatSlideUp {
    from { opacity:0; transform: translateY(12px); }
    to   { opacity:1; transform: translateY(0); }
}
@media (max-width:480px) {
    .chat-window { width: calc(100vw - 2rem); right: 1rem; bottom: 4.5rem; }
}

/* ── En-tête ─────────────────────────────────────────── */
.chat-header {
    background: var(--orange, #e8580a);
    color: #fff;
    padding: .75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.chat-icon-btn {
    background: rgba(255,255,255,.2);
    border: none;
    color: #fff;
    border-radius: 6px;
    width: 28px;
    height: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chat-icon-btn:hover { background: rgba(255,255,255,.35); }

/* ── Budget ──────────────────────────────────────────── */
.chat-budget {
    padding: .4rem .75rem;
    background: #fafafa;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
}
.chat-budget-bar  { height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden; }
.chat-budget-fill { height: 100%; background: var(--orange,#e8580a); border-radius:2px; transition: width .3s; }
.chat-budget-fill--danger { background: #dc3545; }

/* ── Corps ───────────────────────────────────────────── */
.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: .75rem;
    display: flex;
    flex-direction: column;
    gap: .6rem;
    scroll-behavior: smooth;
}
.chat-body::-webkit-scrollbar { width: 4px; }
.chat-body::-webkit-scrollbar-track { background: transparent; }
.chat-body::-webkit-scrollbar-thumb { background: #ddd; border-radius: 2px; }

/* ── Bienvenue ───────────────────────────────────────── */
.chat-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem .5rem;
    gap: .5rem;
}
.chat-welcome__mascot { width: 48px; height: 48px; object-fit: contain; opacity: .7; }
.chat-welcome__title  { font-weight: 700; font-size: .9rem; color: #1a1a2e; margin: 0; }
.chat-welcome__sub    { font-size: .8rem; color: #6b7280; margin: 0; line-height: 1.5; }

/* ── En cours de conception ──────────────────────────── */
.chat-coming-soon {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    text-align: center;
    gap: .75rem;
}
.chat-coming-soon__mascot { width: 56px; height: 56px; object-fit: contain; opacity: .5; }
.chat-coming-soon__title  { font-weight: 700; font-size: .95rem; color: #1a1a2e; margin: 0; }
.chat-coming-soon__sub    { font-size: .82rem; color: #6b7280; margin: 0; line-height: 1.5; }

/* ── Bulles ──────────────────────────────────────────── */
.chat-bubble { display: flex; align-items: flex-end; gap: .4rem; }
.chat-bubble--user      { flex-direction: row-reverse; }
.chat-bubble--assistant { flex-direction: row; }

.chat-bubble-avatar {
    width: 24px;
    height: 24px;
    object-fit: contain;
    border-radius: 50%;
    flex-shrink: 0;
    margin-bottom: 2px;
}

.chat-bubble-content {
    max-width: 78%;
    padding: .5rem .75rem;
    border-radius: 14px;
    font-size: .85rem;
    line-height: 1.55;
    word-break: break-word;
}
.chat-bubble--user      .chat-bubble-content { background: var(--orange,#e8580a); color:#fff; border-bottom-right-radius:4px; }
.chat-bubble--assistant .chat-bubble-content { background: #f3f4f6; color:#1a1a2e; border-bottom-left-radius:4px; }

/* Markdown dans les bulles */
.chat-bubble-content pre {
    background: rgba(0,0,0,.08);
    border-radius: 6px;
    padding: .4rem .6rem;
    overflow-x: auto;
    font-size: .78rem;
    margin: .4rem 0 0;
}
.chat-bubble--user .chat-bubble-content pre { background: rgba(255,255,255,.15); }
.chat-bubble-content code {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: .78rem;
}
.chat-bubble-content p  { margin: 0 0 .3rem; }
.chat-bubble-content p:last-child { margin-bottom: 0; }
.chat-bubble-content strong { font-weight: 700; }

/* ── Typing animation ────────────────────────────────── */
.chat-typing { display:inline-flex; gap:3px; align-items:center; padding:.1rem 0; }
.chat-typing span {
    width:6px; height:6px; background:#999; border-radius:50%;
    animation: chatBounce 1.2s infinite ease-in-out both;
}
.chat-typing span:nth-child(2) { animation-delay:.16s; }
.chat-typing span:nth-child(3) { animation-delay:.32s; }
@keyframes chatBounce { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }

/* ── Erreur ──────────────────────────────────────────── */
.chat-error {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    color: #dc3545;
    background: #fff5f5;
    border: 1px solid #fcd0d0;
    padding: .4rem .6rem;
    border-radius: 8px;
}

/* ── Footer ──────────────────────────────────────────── */
.chat-footer {
    padding: .6rem;
    border-top: 1px solid #f0f0f0;
    flex-shrink: 0;
    background: #fff;
}
.chat-input-row { display:flex; gap:.4rem; align-items:flex-end; }
.chat-input {
    flex:1;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    padding: .45rem .7rem;
    font-size: .85rem;
    resize: none;
    outline: none;
    max-height: 120px;
    line-height: 1.4;
    transition: border-color .2s;
    font-family: inherit;
    overflow-y: auto;
}
.chat-input:focus { border-color: var(--orange,#e8580a); }
.chat-send-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--orange,#e8580a);
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .2s, opacity .2s;
}
.chat-send-btn:disabled { opacity: .45; cursor: not-allowed; }
.chat-send-btn:not(:disabled):hover { background: #c94a06; }
</style>

<script>
function chatDrag() {
    return {
        bottom: 24,
        right:  24,
        _dragging: false,
        _moved: false,
        _startX: 0,
        _startY: 0,
        _startRight: 0,
        _startBottom: 0,

        init() {
            const saved = localStorage.getItem('chat-widget-pos');
            if (saved) {
                try {
                    const p = JSON.parse(saved);
                    this.bottom = p.bottom ?? 24;
                    this.right  = p.right  ?? 24;
                } catch {}
            }
            this._clamp();

            const onMove = (e) => this._onMove(e);
            const onUp   = (e) => this._onUp(e);
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup',   onUp);
            window.addEventListener('touchmove', onMove, { passive: false });
            window.addEventListener('touchend',  onUp);
        },

        startDrag(e) {
            this._dragging = true;
            this._moved    = false;
            const touch = e.touches ? e.touches[0] : e;
            this._startX      = touch.clientX;
            this._startY      = touch.clientY;
            this._startRight  = this.right;
            this._startBottom = this.bottom;
        },

        _onMove(e) {
            if (!this._dragging) return;
            if (e.cancelable) e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            const dx = touch.clientX - this._startX;
            const dy = touch.clientY - this._startY;
            if (Math.abs(dx) > 3 || Math.abs(dy) > 3) this._moved = true;
            this.right  = this._startRight  - dx;
            this.bottom = this._startBottom - dy;
            this._clamp();
        },

        _onUp() {
            if (!this._dragging) return;
            this._dragging = false;
            if (this._moved) {
                localStorage.setItem('chat-widget-pos', JSON.stringify({ bottom: this.bottom, right: this.right }));
                setTimeout(() => { this._moved = false; }, 50);
            }
        },

        _clamp() {
            const W = window.innerWidth;
            const H = window.innerHeight;
            const FAB = 54;
            this.right  = Math.max(8, Math.min(this.right,  W - FAB - 8));
            this.bottom = Math.max(8, Math.min(this.bottom, H - FAB - 8));
        },
    };
}
</script>
