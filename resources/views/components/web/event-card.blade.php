@props([
    'event'         => null,
    'reveal'        => true,
    'type'          => 'meetup',
    'typeLabel'     => 'Meetup',
    'cover'         => null,
    'title'         => '',
    'month'         => '',
    'day'           => '',
    'time'          => '',
    'location'      => '',
    'spotsUsed'     => 0,
    'spotsTotal'    => 0,
    'href'          => '#',
    'registerHref'  => '#',
    'past'          => false,
    'attendedCount' => null,
    'delay'         => '',
])

@if($event)
    @php
        $card = $event->toWebCardProps();
        $type = $card['type'];
        $typeLabel = $card['typeLabel'];
        $cover = $card['cover'] ?? null;
        $title = $card['title'];
        $month = $card['month'];
        $day = $card['day'];
        $time = $card['time'];
        $location = $card['location'];
        $spotsUsed = $card['spotsUsed'];
        $spotsTotal = $card['spotsTotal'];
        $href = $card['href'];
        $registerHref = $card['registerHref'];
        $past = $card['past'];
        $attendedCount = $card['attendedCount'] ?? null;
        $delay = $card['delay'] ?? '';
    @endphp
@endif

@php
  $bannerClass = match(strtolower($type)) {
    'webinar'    => 'ev-webinar',
    'hackathon'  => 'ev-hackathon',
    default      => 'ev-meetup',
  };
  $badgeStyle = match(strtolower($type)) {
    'webinar'   => 'background:#e7ebff;color:#4361ee',
    'hackathon' => 'background:#f1e7ff;color:#7209b7',
    default     => '',
  };
  $badgeClass = strtolower($type) === 'meetup' ? 'badge-orange' : '';
  $icon = match(strtolower($type)) {
    'webinar'   => 'fa-solid fa-video',
    'hackathon' => 'fa-solid fa-laptop-code',
    default     => 'fa-solid fa-people-roof',
  };
  $pct = $spotsTotal > 0 ? round(($spotsUsed / $spotsTotal) * 100) : 0;
  $spotsLeft = $spotsTotal - $spotsUsed;
@endphp

<article @class([
    'card-soft event-card',
    'past' => $past,
    'reveal' => $reveal,
]) @if($reveal && $delay) data-delay="{{ $delay }}" @endif>
    @if($past)
      <span class="past-badge badge-pill badge-soft"><i class="fa-solid fa-clock-rotate-left"></i> Événement passé</span>
    @endif
    <div class="{{ $past ? 'card-soft' : '' }}">
      @if($cover)
        <a href="{{ $href }}" class="event-cover event-cover--card">
          <img src="{{ $cover }}" alt="" loading="lazy" width="640" height="360" />
          <span class="event-cover-accent {{ $bannerClass }}" aria-hidden="true"></span>
        </a>
      @else
        <div class="event-banner {{ $bannerClass }}"></div>
      @endif
      <div class="card-pad">
        <div class="d-flex gap-3 mb-3">
          <div class="event-date-chip"><div class="m">{{ $month }}</div><div class="d">{{ $day }}</div></div>
          <div>
            <span class="badge-pill {{ $badgeClass }}" @if($badgeStyle) style="{{ $badgeStyle }}" @endif>
              <i class="{{ $icon }}"></i> {{ $typeLabel }}
            </span>
            <h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ $href }}" class="text-navy">{{ $title }}</a></h3>
          </div>
        </div>
        @if(!$past)
          <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
            @if($time)
              <span><i class="fa-regular fa-clock text-orange me-2"></i> {{ $time }}</span>
            @endif
            @if($location)
              <span><i class="fa-solid fa-location-dot text-orange me-2"></i> {{ $location }}</span>
            @endif
          </div>
          @if($spotsTotal > 0)
            <div class="spots-label"><span>{{ $spotsUsed }} / {{ $spotsTotal }} inscrits</span><span>{{ $spotsLeft }} places restantes</span></div>
            <div class="progress-spots mb-3"><div class="bar" style="width:{{ $pct }}%"></div></div>
          @else
            <p class="text-muted-2 small mb-3">Places illimitées</p>
          @endif
          <div class="d-flex gap-2">
            <a href="{{ $registerHref }}" class="btn btn-brand flex-grow-1"><i class="fa-solid fa-ticket"></i> S'inscrire</a>
            <a href="{{ $href }}" class="btn btn-ghost" aria-label="Voir les détails"><i class="fa-solid fa-arrow-right"></i></a>
          </div>
        @else
          <div class="d-flex flex-column gap-2 mb-3" style="font-size:.86rem;color:var(--muted)">
            @if($location)
              <span><i class="fa-solid fa-location-dot me-2"></i> {{ $location }}</span>
            @endif
            @if($attendedCount)
              <span><i class="fa-solid fa-users me-2"></i> {{ $attendedCount }} participant{{ $attendedCount > 1 ? 's' : '' }}</span>
            @endif
          </div>
          <a href="{{ route('blog.index') }}" class="btn btn-ghost w-100"><i class="fa-solid fa-circle-play"></i> Voir le replay</a>
        @endif
      </div>
    </div>
</article>
