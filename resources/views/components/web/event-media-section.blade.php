@props([
    'event',
])

@php
    use App\Enums\Events\EventMediaType;

    $mediaItems = $event->media;
    $videos = $mediaItems->where('type', EventMediaType::VIDEO);
    $pdfs = $mediaItems->where('type', EventMediaType::PDF);
    $images = $mediaItems->where('type', EventMediaType::IMAGE);
@endphp

@if($mediaItems->isNotEmpty())
  <div class="event-media-section">
    <div class="prose">
      <h2>{{ $event->isPast() ? 'Replay & ressources' : 'Ressources' }}</h2>
    </div>

    @if($videos->isNotEmpty())
      <p class="event-media-label">Vidéos</p>

      @php
        $embedVideos = $videos->filter(fn ($media) => $media->youtubeEmbedUrl() !== null);
        $linkVideos = $videos->filter(fn ($media) => $media->youtubeEmbedUrl() === null && $media->resolvedUrl() !== null);
      @endphp

      @if($embedVideos->count() === 1 && $linkVideos->isEmpty())
        <div class="event-media-featured ratio ratio-16x9 mb-3">
          <iframe src="{{ $embedVideos->first()->youtubeEmbedUrl() }}"
                  title="Replay — {{ $event->title }}"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen></iframe>
        </div>
      @else
        <div class="row g-3 mb-3">
          @foreach($embedVideos as $index => $media)
            <div class="col-md-4">
              <div class="event-media-card">
                <div class="event-media-card__embed ratio ratio-16x9">
                  <iframe src="{{ $media->youtubeEmbedUrl() }}"
                          title="Vidéo {{ $index + 1 }} — {{ $event->title }}"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen></iframe>
                </div>
              </div>
            </div>
          @endforeach

          @foreach($linkVideos as $index => $media)
            <div class="col-md-4">
              <a href="{{ $media->resolvedUrl() }}" target="_blank" rel="noopener" class="event-media-card event-media-card--action">
                <span class="event-media-card__icon event-media-card__icon--video">
                  <i class="fa-solid fa-play" aria-hidden="true"></i>
                </span>
                <span class="event-media-card__text">
                  {{ $videos->count() > 1 ? 'Vidéo '.$index + 1 : 'Vidéo' }}
                </span>
                <span class="event-media-card__cta">Regarder</span>
              </a>
            </div>
          @endforeach
        </div>
      @endif
    @endif

    @if($pdfs->isNotEmpty())
      <p class="event-media-label">Documents</p>
      <div class="row g-3 mb-3">
        @foreach($pdfs as $index => $media)
          @if($media->resolvedUrl())
            <div class="col-md-4">
              <a href="{{ $media->resolvedUrl() }}" target="_blank" rel="noopener" class="event-media-card event-media-card--action">
                <span class="event-media-card__icon event-media-card__icon--pdf">
                  <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                </span>
                <span class="event-media-card__text">
                  {{ $pdfs->count() > 1 ? 'Document '.$index + 1 : 'Document PDF' }}
                </span>
                <span class="event-media-card__cta">Télécharger</span>
              </a>
            </div>
          @endif
        @endforeach
      </div>
    @endif

    @if($images->isNotEmpty())
      <p class="event-media-label">Photos</p>
      <div class="row g-3 event-media-photos">
        @foreach($images as $media)
          @if($media->resolvedUrl())
            <div class="col-md-4">
              <a href="{{ $media->resolvedUrl() }}" target="_blank" rel="noopener" class="event-media-card event-media-card--photo">
                <img src="{{ $media->resolvedUrl() }}" alt="Photo — {{ $event->title }}" loading="lazy" decoding="async">
              </a>
            </div>
          @endif
        @endforeach
      </div>
    @endif
  </div>
@endif
