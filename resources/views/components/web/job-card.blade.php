@props([
    'logoClass'    => 'cl-1',
    'logoText'     => '',
    'title'        => '',
    'company'      => '',
    'location'     => '',
    'remote'       => false,
    'description'  => '',
    'contractType' => '',
    'level'        => '',
    'tags'         => [],
    'salary'       => '',
    'href'         => '#',
    'badge'        => null,
    'badgeStyle'   => '',
])

<div class="job-card">
  <div class="d-flex gap-3">
    <div class="company-logo {{ $logoClass }}">{{ $logoText }}</div>
    <div class="flex-grow-1 min-w-0">
      <div class="d-flex justify-content-between gap-2">
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h3 style="font-size:1.15rem;margin:0" class="text-navy">
              <a href="{{ $href }}" class="text-navy">{{ $title }}</a>
            </h3>
            @if($badge)
              <span class="badge-pill {{ $badgeStyle ?: 'badge-green' }}" @if($badgeStyle && str_starts_with($badgeStyle, 'background')) style="{{ $badgeStyle }}" @endif>{{ $badge }}</span>
            @endif
          </div>
          <div class="text-muted-2" style="font-size:.9rem">
            <strong>{{ $company }}</strong>
            @if($location)
              · <i class="fa-solid fa-location-dot"></i> {{ $location }}
            @endif
            @if($remote)
              <span class="badge-pill badge-navy ms-1">Remote OK</span>
            @endif
          </div>
        </div>
        <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
      </div>
      @if($description)
        <p class="my-2" style="font-size:.92rem;color:var(--muted)">{{ $description }}</p>
      @endif
      <div class="d-flex flex-wrap gap-2 mb-2">
        @if($contractType)
          <span class="badge-pill badge-soft">{{ $contractType }}</span>
        @endif
        @if($level)
          <span class="badge-pill badge-soft">{{ $level }}</span>
        @endif
        @foreach($tags as $tag)
          <span class="tag">{{ $tag }}</span>
        @endforeach
      </div>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="salary">{{ $salary }}</span>
        <a href="{{ $href }}" class="btn btn-brand">Apply <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</div>
