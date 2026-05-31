@props([
    'avatar'      => '',
    'avatarAlt'   => '',
    'title'       => '',
    'subtitle'    => '',
    'timeAgo'     => '',
    'badge'       => null,
    'badgeColor'  => 'success',
])

<li class="list-group-item d-flex align-items-center gap-3">
  @if($avatar)
    <img src="{{ $avatar }}" alt="{{ $avatarAlt }}" class="rounded" width="48">
  @endif
  <div class="flex-grow-1">
    <p class="mb-1">{{ $title }}</p>
    @if($subtitle)
      <div class="d-flex align-items-center gap-2 text-muted">
        <small>{{ $subtitle }}</small>
        @if($timeAgo)
          <small>•</small>
          <small>{{ $timeAgo }}</small>
        @endif
      </div>
    @endif
  </div>
  @if($badge)
    <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }}">{{ $badge }}</span>
  @endif
</li>
