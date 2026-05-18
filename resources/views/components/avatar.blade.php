@props([
    'src'      => null,
    'name',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'group block shrink-0']) }}>
    <div class="flex items-center">
        <div>
            @if ($src)
                <img src="{{ $src }}" alt="{{ $name }}"
                    class="inline-block size-8 rounded-full ring-1 ring-gray-900 ring-offset-1" />
            @else
                <div
                    class="inline-flex size-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-semibold ring-1 ring-gray-900 ring-offset-1">
                    {{ strtoupper(substr($name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="ml-2">
            <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                {{ $name }}
            </p>
            @if ($subtitle)
                <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>
</div>
