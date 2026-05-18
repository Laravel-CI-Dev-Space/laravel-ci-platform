@props(['value', 'label', 'accent' => 'bg-primary'])

<div class="relative bg-white p-4 sm:p-6 rounded-xl border border-primary/30 overflow-hidden shadow-sm">
    <div class="absolute top-0 left-0 w-full h-[6px] {{ $accent }}"></div>

    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $accent }}">
        {{ $icon }}
    </div>

    <h4 class="text-xl sm:text-2xl font-bold text-gray-900 mt-4">{{ $value }}</h4>
    <p class="text-base text-gray-500">{{ $label }}</p>
</div>
