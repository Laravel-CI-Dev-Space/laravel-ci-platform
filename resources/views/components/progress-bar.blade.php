@props([
    'label',
    'value',
    'percent',
    'color' => 'bg-primary',
])

<div {{ $attributes->merge(['class' => 'mt-auto']) }}>
    <div class="flex justify-between text-xs text-gray-400 mb-1.5">
        <span>{{ $label }}</span>
        <span>{{ $value }}</span>
    </div>
    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full {{ $color }} rounded-full transition-all" style="width: {{ $percent }}%"></div>
    </div>
</div>
