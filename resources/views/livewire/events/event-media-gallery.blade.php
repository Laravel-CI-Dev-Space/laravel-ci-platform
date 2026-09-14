{{-- Galerie de médias récapitulatif — Grille R2 avec sélection + téléchargement ZIP --}}
<div class="event-media-gallery">

    {{-- ── Barre de contrôle ── --}}
    <div class="media-toolbar">

        {{-- Filtres type --}}
        <div class="media-filters">
            <button
                wire:click="setFilter('all')"
                class="filter-btn {{ $filter === 'all' ? 'active' : '' }}"
            >
                Tous <span class="count">{{ $mediaCounts['all'] }}</span>
            </button>
            <button
                wire:click="setFilter('photo')"
                class="filter-btn {{ $filter === 'photo' ? 'active' : '' }}"
            >
                <i class="fa-solid fa-image"></i> Photos <span class="count">{{ $mediaCounts['photo'] }}</span>
            </button>
            <button
                wire:click="setFilter('video')"
                class="filter-btn {{ $filter === 'video' ? 'active' : '' }}"
            >
                <i class="fa-solid fa-video"></i> Vidéos <span class="count">{{ $mediaCounts['video'] }}</span>
            </button>
        </div>

        {{-- Actions sélection --}}
        <div class="media-actions">
            @if(count($selectedIds) > 0)
                <span class="selection-label">{{ count($selectedIds) }} sélectionné(s)</span>

                <form
                    method="POST"
                    action="{{ route('events.media.zip', $event) }}"
                    id="zip-form"
                    style="display:inline"
                >
                    @csrf
                    @foreach($selectedIds as $id)
                        <input type="hidden" name="ids[]" value="{{ $id }}">
                    @endforeach
                    <button type="submit" class="btn-download">
                        <i class="fa-solid fa-file-zipper"></i> Télécharger ({{ count($selectedIds) }})
                    </button>
                </form>

                <button wire:click="clearSelection" class="btn-clear">
                    Effacer la sélection
                </button>
            @else
                <button wire:click="selectAll" class="btn-select-all">
                    <i class="fa-solid fa-check-double"></i> Tout sélectionner
                </button>
            @endif

            @if($mediaCounts['all'] > 0)
                <a
                    href="{{ route('events.media.zip.all', $event) }}"
                    class="btn-download-all"
                    title="Télécharger tous les médias en ZIP"
                >
                    <i class="fa-solid fa-download"></i> Tout télécharger
                </a>
            @endif
        </div>
    </div>

    {{-- ── Grille ── --}}
    @if($media->isEmpty())
        <div class="media-empty">
            <i class="fa-regular fa-images fa-2x"></i>
            <p>Aucun média disponible pour le moment.</p>
        </div>
    @else
        <div class="media-grid">
            @foreach($media as $item)
                <div
                    class="media-card {{ $this->isSelected($item->id) ? 'selected' : '' }}"
                    wire:key="media-{{ $item->id }}"
                >
                    {{-- Checkbox de sélection --}}
                    <button
                        class="media-select-btn"
                        wire:click="toggleSelect({{ $item->id }})"
                        title="{{ $this->isSelected($item->id) ? 'Désélectionner' : 'Sélectionner' }}"
                    >
                        @if($this->isSelected($item->id))
                            <i class="fa-solid fa-circle-check"></i>
                        @else
                            <i class="fa-regular fa-circle"></i>
                        @endif
                    </button>

                    {{-- Miniature --}}
                    @if($item->isPhoto())
                        <a href="{{ $item->url() }}" target="_blank" class="media-thumb">
                            <img
                                src="{{ $item->thumbnailUrl() }}"
                                alt="{{ $item->caption ?? $item->original_name }}"
                                loading="lazy"
                            >
                        </a>
                    @else
                        <a href="{{ $item->url() }}" target="_blank" class="media-thumb media-thumb--video">
                            <div class="video-placeholder">
                                <i class="fa-solid fa-play-circle"></i>
                                <span class="video-label">MP4</span>
                            </div>
                        </a>
                    @endif

                    {{-- Footer carte --}}
                    <div class="media-card-footer">
                        @if($item->caption)
                            <p class="media-caption">{{ $item->caption }}</p>
                        @endif
                        <div class="media-meta">
                            <span class="media-size">{{ $item->humanSize() }}</span>
                            <a
                                href="{{ route('events.media.single', [$event, $item]) }}"
                                class="media-dl-btn"
                                title="Télécharger"
                            >
                                <i class="fa-solid fa-arrow-down-to-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($media->hasPages())
            <div class="media-pagination">
                {{ $media->links() }}
            </div>
        @endif
    @endif

</div>

<style>
.event-media-gallery { width: 100%; }

/* Toolbar */
.media-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--surface, #f8fafc);
    border-radius: .75rem;
    border: 1px solid var(--border, #e2e8f0);
}

.media-filters { display: flex; gap: .5rem; flex-wrap: wrap; }

.filter-btn {
    display: flex; align-items: center; gap: .35rem;
    padding: .45rem .9rem;
    border-radius: 9999px;
    border: 1.5px solid var(--border, #e2e8f0);
    background: transparent;
    font-size: .875rem;
    font-weight: 500;
    cursor: pointer;
    color: var(--text-muted, #64748b);
    transition: all .15s;
}
.filter-btn.active, .filter-btn:hover {
    background: #e7222c;
    border-color: #e7222c;
    color: #fff;
}
.filter-btn .count {
    font-size: .75rem;
    background: rgba(255,255,255,.25);
    border-radius: 9999px;
    padding: 0 .4rem;
}
.filter-btn:not(.active) .count { background: var(--border, #e2e8f0); color: var(--text-muted, #64748b); }

.media-actions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }

.selection-label { font-size: .875rem; font-weight: 600; color: #e7222c; }

.btn-download, .btn-download-all {
    display: flex; align-items: center; gap: .35rem;
    padding: .45rem .9rem;
    border-radius: .5rem;
    background: #e7222c;
    color: #fff;
    border: none;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: opacity .15s;
}
.btn-download:hover, .btn-download-all:hover { opacity: .88; }
.btn-download-all { background: #1e293b; }

.btn-select-all, .btn-clear {
    padding: .4rem .85rem;
    border-radius: .5rem;
    border: 1.5px solid var(--border, #e2e8f0);
    background: transparent;
    font-size: .875rem;
    cursor: pointer;
    color: var(--text-muted, #64748b);
    transition: all .15s;
}
.btn-select-all:hover, .btn-clear:hover { border-color: #e7222c; color: #e7222c; }

/* Grille */
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: .75rem;
}

.media-card {
    position: relative;
    border-radius: .625rem;
    border: 2px solid var(--border, #e2e8f0);
    overflow: hidden;
    background: var(--surface, #f8fafc);
    transition: border-color .15s, box-shadow .15s;
}
.media-card.selected { border-color: #e7222c; box-shadow: 0 0 0 3px rgba(231,34,44,.15); }
.media-card:hover { border-color: #94a3b8; }

.media-select-btn {
    position: absolute;
    top: .5rem; left: .5rem;
    z-index: 2;
    background: rgba(255,255,255,.9);
    border: none;
    border-radius: 9999px;
    width: 1.75rem; height: 1.75rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    color: var(--text-muted, #64748b);
    transition: color .15s;
    padding: 0;
}
.media-card.selected .media-select-btn { color: #e7222c; }
.media-select-btn:hover { color: #e7222c; }

.media-thumb {
    display: block;
    aspect-ratio: 4/3;
    overflow: hidden;
}
.media-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .2s;
}
.media-thumb:hover img { transform: scale(1.04); }

.media-thumb--video {
    background: #0f172a;
    display: flex; align-items: center; justify-content: center;
}
.video-placeholder {
    display: flex; flex-direction: column; align-items: center; gap: .35rem;
    color: #94a3b8;
}
.video-placeholder i { font-size: 2.5rem; color: #e7222c; }
.video-label { font-size: .75rem; font-weight: 700; letter-spacing: .05em; }

.media-card-footer {
    padding: .5rem .6rem;
    border-top: 1px solid var(--border, #e2e8f0);
}
.media-caption {
    font-size: .75rem;
    color: var(--text-muted, #64748b);
    margin-bottom: .3rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.media-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.media-size { font-size: .7rem; color: var(--text-muted, #94a3b8); }
.media-dl-btn {
    color: var(--text-muted, #94a3b8);
    font-size: .875rem;
    text-decoration: none;
    transition: color .15s;
}
.media-dl-btn:hover { color: #e7222c; }

/* Empty */
.media-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted, #94a3b8);
}
.media-empty p { margin-top: .75rem; font-size: .95rem; }

/* Pagination */
.media-pagination { margin-top: 1.5rem; display: flex; justify-content: center; }

/* Responsive */
@media (max-width: 600px) {
    .media-grid { grid-template-columns: repeat(2, 1fr); }
    .media-toolbar { flex-direction: column; align-items: flex-start; }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) {
        --surface: #1e293b;
        --border: #334155;
        --text-muted: #94a3b8;
    }
}
:root[data-theme="dark"] {
    --surface: #1e293b;
    --border: #334155;
    --text-muted: #94a3b8;
}
</style>
