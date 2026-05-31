@props([
    'votes'     => 0,
    'answers'   => 0,
    'accepted'  => false,
    'pinned'    => false,
    'title'     => '',
    'excerpt'   => '',
    'tags'      => [],
    'authorInitials' => '',
    'authorName' => '',
    'authorClass' => 'av-1',
    'timeAgo'   => '',
    'href'      => '#',
])

<div class="q-card {{ $pinned ? 'pinned' : '' }} reveal">
  <div class="q-stats">
    <div class="q-vote"><span>{{ $votes }}</span><small>votes</small></div>
    <div class="q-answers {{ $accepted ? 'accepted' : '' }}"><strong>{{ $answers }}</strong>answers</div>
  </div>
  <div class="q-body">
    @if($pinned)
      <div class="pin-flag"><i class="fa-solid fa-thumbtack"></i> Pinned by moderators</div>
    @endif
    <h3 class="q-title"><a href="{{ $href }}">{{ $title }}</a></h3>
    @if($excerpt)
      <p class="q-excerpt">{{ $excerpt }}</p>
    @endif
    <div class="q-tags">
      @foreach($tags as $tag)
        <span class="tag">{{ $tag }}</span>
      @endforeach
    </div>
    <div class="q-foot">
      <div class="author-row">
        <span class="avatar avatar-sm {{ $authorClass }}">{{ $authorInitials }}</span>
        <div class="meta"><div class="name">{{ $authorName }}</div></div>
      </div>
      <span class="read-time"><i class="fa-regular fa-clock"></i> {{ $timeAgo }}</span>
    </div>
  </div>
</div>
