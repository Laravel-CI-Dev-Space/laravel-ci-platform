@props(['event'])

@php
    $seatsLeft = $event['seats_total'] - $event['seats_taken'];
    $progress = ($event['seats_taken'] / $event['seats_total']) * 100;
@endphp

<div
    class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-2xs hover:border-primary/50 transition-colors">
    <div class="relative h-40 bg-gradient-to-br from-red-50 to-gray-100 flex items-center justify-center">
        @if (!empty($event['image']))
            <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="w-full h-full object-cover">
        @else
            <span class="material-icons text-6xl text-[#E3342F]/20">event</span>
        @endif

        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-center">
            <div class="text-primary font-bold text-lg">15</div>
            <div class="text-[10px] uppercase text-on-surface">Oct.</div>
        </div>
    </div>

    <div class="p-5 flex flex-col flex-1 gap-3">
        <h3 class="font-nunito text-base font-semibold text-gray-900 leading-snug">
            <a href="#" class="hover:text-primary transition-colors">
                <span class="absolute inset-0"></span>
                {{ $event['title'] }}
            </a>
        </h3>

        <div class="flex flex-col gap-1.5 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <svg class="size-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"></path>
                </svg>
                {{ $event['location'] }}
            </span>

            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <svg class="size-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                </svg>
                {{ $event['time'] }}
            </span>
        </div>

        <p class="text-sm text-gray-500 line-clamp-2">{{ $event['description'] }}</p>

        <x-progress-bar label="{{ $event['seats_taken'] }} / {{ $event['seats_total'] }} inscrits"
            value="{{ $seatsLeft }} restante(s)" :percent="$progress" />

        <a href="{{ $event['cta_url'] }}"
            class="mt-2 bg-[#E3342F] hover:bg-[#C0392B] text-white text-sm font-semibold text-center py-2.5 rounded-xl transition-colors block">
            {{ $event['cta_label'] }}
        </a>
    </div>
</div>
