@props(['label' => '', 'href' => '#'])

@if($href && $href !== '#')
  <a href="{{ $href }}" class="tag">{{ $label }}</a>
@else
  <span class="tag">{{ $label }}</span>
@endif
