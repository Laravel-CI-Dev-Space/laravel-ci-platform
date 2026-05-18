@props(['label', 'color' => 'red'])

@php
$colors = [
    'red'    => 'bg-red-50 text-red-700 ring-red-600/10',
    'green'  => 'bg-green-50 text-green-700 ring-green-600/10',
    'blue'   => 'bg-blue-50 text-blue-700 ring-blue-600/10',
    'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10',
    'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/10',
    'orange' => 'bg-orange-50 text-orange-700 ring-orange-600/10',
    'gray'   => 'bg-gray-50 text-gray-700 ring-gray-600/10',
];
$classes = $colors[$color] ?? $colors['gray'];
@endphp

<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $classes }}">
    {{ $label }}
</span>
