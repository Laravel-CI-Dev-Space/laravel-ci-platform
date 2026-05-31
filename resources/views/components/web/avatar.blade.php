@props(['src' => '', 'name' => '', 'size' => 'md', 'avatarClass' => 'av-1'])

@php
  $sizeClass = match($size) {
    'sm' => 'avatar-sm',
    'lg' => 'avatar-lg',
    'xl' => 'avatar-xl',
    default => '',
  };
  $initials = $name ? collect(explode(' ', $name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') : '';
@endphp

@if($src)
  <img src="{{ $src }}" alt="{{ $name }}" class="avatar {{ $sizeClass }} rounded-circle" />
@else
  <span class="avatar {{ $sizeClass }} {{ $avatarClass }}">{{ $initials }}</span>
@endif
