@props(['event'])

@php
    $data = $event instanceof \App\Models\Event
        ? $event->toCardData()
        : $event;

    $seatsLeft = max(0, $data['seats_total'] - $data['seats_taken']);
    $percent = $data['seats_total'] > 0
        ? min(100, ($data['seats_taken'] / $data['seats_total']) * 100)
        : 0;
@endphp

<div
    class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-2xs hover:border-primary/50 transition-colors">
    <div class="relative h-40 bg-gradient-to-br from-orange-50 to-gray-100 flex items-center justify-center">
        @if (! empty($data['image']))
            <img src="{{ $data['image'] }}" alt="{{ $data['title'] }}" class="w-full h-full object-cover">
        @else
            <i class="fa-regular fa-calendar-days text-5xl text-primary/20"></i>
        @endif

        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-center shadow-sm">
            <div class="text-primary font-bold text-lg leading-none">{{ $data['date_day'] ?? '—' }}</div>
            <div class="text-[10px] uppercase text-gray-500 font-bold">{{ $data['date_month'] ?? '' }}</div>
        </div>

        @if(! empty($data['type_label']))
            <span class="absolute top-4 right-4 bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">
                {{ $data['type_label'] }}
            </span>
        @endif
    </div>

    <div class="p-5 flex flex-col flex-1 gap-3">
        <h3 class="font-nunito text-base font-semibold text-gray-900 leading-snug">
            <a href="{{ $data['cta_url'] }}" class="hover:text-primary transition-colors">
                <span class="absolute inset-0"></span>
                {{ $data['title'] }}
            </a>
        </h3>

        <div class="flex flex-col gap-1.5 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-primary/70 w-4"></i>
                {{ $data['location'] }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fa-regular fa-clock text-primary/70 w-4"></i>
                {{ $data['time'] }}
            </span>
        </div>

        <p class="text-sm text-gray-500 line-clamp-2">{{ $data['description'] }}</p>

        <x-progress-bar
            label="{{ $data['seats_taken'] }} / {{ $data['seats_total'] }} inscrits"
            value="{{ $seatsLeft }} restante(s)"
            :percent="$percent"
        />

        <span class="mt-2 bg-primary group-hover:bg-orange-500 text-white text-sm font-semibold text-center py-2.5 rounded-xl transition-colors block pointer-events-none">
            {{ $data['cta_label'] }}
        </span>
    </div>
</div>
