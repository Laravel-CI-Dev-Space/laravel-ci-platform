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
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4v16m8-8H4"/>
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

            {{-- Budget tokens --}}
            @if (auth()->check() && $budget)
                <div class="chat-budget">
                    <div class="d-flex justify-content-between" style="font-size:.72rem;opacity:.7;margin-bottom:3px">
                        <span>{{ number_format($budget['used']) }} tokens utilisés</span>
                        <span>{{ number_format($budget['remaining']) }} restants</span>
                    </div>
                    <div class="chat-budget-bar">
                        <div class="chat-budget-fill {{ $budget['percent'] > 80 ? 'chat-budget-fill--danger' : '' }}"
                             style="width:{{ min($budget['percent'], 100) }}%"></div>
                    </div>
                </div>
            @endif

            {{-- Corps (messages) --}}
            <div class="chat-body" id="chat-body"
                 x-data="{}"
                 x-on:chat-scroll-bottom.window="$el.scrollTop = $el.scrollHeight">

                {{-- Message de bienvenue --}}
                @if (empty($messages))
                    <div class="chat-bubble chat-bubble--assistant">
                        <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                             class="chat-avatar" style="width:24px;height:24px;object-fit:contain">
                        <div class="chat-bubble-content">
                            @auth
                                Bonjour <strong>{{ auth()->user()->name }}</strong> 👋<br>
                                Je suis l'assistant IA de Laravel CI. Je peux vous aider sur
                                <strong>Laravel, PHP</strong> et tout ce qui concerne <strong>cette plateforme</strong>.
                                Que puis-je faire pour vous ?
                            @else
                                Bonjour ! Je suis l'assistant Laravel CI. Pour utiliser le chat,
                                veuillez vous <a href="{{ route('login') }}" class="text-orange">connecter</a>.
                            @endauth
                        </div>
                    </div>
                @endif

                {{-- Historique --}}
                @foreach ($messages as $msg)
                    <div class="chat-bubble chat-bubble--{{ $msg['role'] }}">
                        @if ($msg['role'] === 'assistant')
                            <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                                 class="chat-avatar" style="width:24px;height:24px;object-fit:contain">
                        @endif
                        <div class="chat-bubble-content">
                            {!! nl2br(e($msg['content'])) !!}
                        </div>
                    </div>
                @endforeach

                {{-- Loading --}}
                @if ($loading)
                    <div class="chat-bubble chat-bubble--assistant">
                        <img src="{{ asset('assets/web/img/mascot.png') }}" alt=""
                             class="chat-avatar" style="width:24px;height:24px;object-fit:contain">
                        <div class="chat-bubble-content">
                            <span class="chat-typing">
                                <span></span><span></span><span></span>
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Erreur --}}
                @if ($error)
                    <div class="chat-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                             viewBox="0 0 16 16" style="flex-shrink:0">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        {{ $error }}
                    </div>
                @endif
            </div>

            {{-- Zone de saisie --}}
            <div class="chat-footer">
                @auth
                    <form wire:submit="sendMessage" class="chat-input-row">
                        <textarea
                            wire:model="input"
                            placeholder="Posez votre question Laravel..."
                            class="chat-input"
                            rows="1"
                            @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage() }"
                            @input="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,120)+'px'"
                            :disabled="$wire.loading"
                        ></textarea>
                        <button type="submit" class="chat-send-btn" :disabled="$wire.loading || !$wire.input.trim()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-brand w-100" style="font-size:.85rem">
                        <i class="fa-brands fa-github me-1"></i> Connectez-vous pour chatter
                    </a>
                @endauth
            </div>
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

/* ── Corps (messages) ────────────────────────────────── */
.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: .75rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
}

/* ── Bulles ──────────────────────────────────────────── */
.chat-bubble { display: flex; align-items: flex-end; gap: .4rem; }
.chat-bubble--user      { flex-direction: row-reverse; }
.chat-bubble--assistant { flex-direction: row; }
.chat-bubble-content {
    max-width: 78%;
    padding: .5rem .75rem;
    border-radius: 14px;
    font-size: .85rem;
    line-height: 1.5;
    word-break: break-word;
}
.chat-bubble--user      .chat-bubble-content { background: var(--orange,#e8580a); color:#fff; border-bottom-right-radius:4px; }
.chat-bubble--assistant .chat-bubble-content { background: #f3f4f6; color:#1a1a2e; border-bottom-left-radius:4px; }

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
                /* bloquer le click qui suit le drag */
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
