@props([
    'level'          => 'beginner',
    'levelLabel'     => 'Beginner',
    'title'          => '',
    'excerpt'        => '',
    'tags'           => [],
    'authorInitials' => '',
    'authorName'     => '',
    'authorClass'    => 'av-1',
    'readTime'       => '',
    'href'           => '#',
    'delay'          => '',
])

@php
  $levelClass = match(strtolower($level)) {
    'intermediate' => 'lv-intermediate',
    'advanced'     => 'lv-advanced',
    default        => 'lv-beginner',
  };
  $badgeClass = match(strtolower($level)) {
    'intermediate' => 'badge-orange',
    'advanced'     => '',
    default        => 'badge-green',
  };
  $badgeStyle = strtolower($level) === 'advanced'
    ? 'background:#fdeaec;color:var(--level-advanced)'
    : '';
  $dotBg = match(strtolower($level)) {
    'intermediate' => 'var(--level-intermediate)',
    'advanced'     => 'var(--level-advanced)',
    default        => 'var(--green)',
  };
@endphp

<article class="card-soft article-card reveal" @if($delay) data-delay="{{ $delay }}" @endif>
  <div class="level-banner {{ $levelClass }}"></div>
  <div class="card-pad">
    <span class="badge-pill {{ $badgeClass }}" @if($badgeStyle) style="{{ $badgeStyle }}" @endif>
      <span class="lv-dot" style="background:{{ $dotBg }}"></span> {{ $levelLabel }}
    </span>
    <h3 class="art-title"><a href="{{ $href }}">{{ $title }}</a></h3>
    @if($excerpt)
      <p class="art-excerpt">{{ $excerpt }}</p>
    @endif
    @if(count($tags))
      <div class="q-tags">
        @foreach($tags as $tag)
          <span class="tag">{{ $tag }}</span>
        @endforeach
      </div>
    @endif
    <div class="art-foot">
      <div class="author-row">
        <span class="avatar avatar-sm {{ $authorClass }}">{{ $authorInitials }}</span>
        <div class="meta"><div class="name">{{ $authorName }}</div></div>
      </div>
      <span class="read-time"><i class="fa-regular fa-clock"></i> {{ $readTime }}</span>
    </div>
  </div>
</article>
