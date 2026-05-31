@props(['level' => 'Beginner'])

@php
  $lower = strtolower($level);
  $badgeClass = match($lower) {
    'intermediate' => 'badge-orange',
    'advanced'     => '',
    default        => 'badge-green',
  };
  $badgeStyle = $lower === 'advanced' ? 'background:#fdeaec;color:var(--level-advanced)' : '';
  $dotBg = match($lower) {
    'intermediate' => 'var(--level-intermediate)',
    'advanced'     => 'var(--level-advanced)',
    default        => 'var(--green)',
  };
@endphp

<span class="badge-pill {{ $badgeClass }}" @if($badgeStyle) style="{{ $badgeStyle }}" @endif>
  <span class="lv-dot" style="background:{{ $dotBg }}"></span> {{ $level }}
</span>
