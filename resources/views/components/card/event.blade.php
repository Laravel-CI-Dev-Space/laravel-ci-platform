@props(['event'])

@php
$typeColors = [
    'meetup'     => 'bg-blue-100 text-blue-700',
    'webinar'    => 'bg-purple-100 text-purple-700',
    'hackathon'  => 'bg-orange-100 text-orange-700',
    'networking' => 'bg-teal-100 text-teal-700',
    'conference' => 'bg-red-100 text-red-700',
];
$typeColor = $typeColors[$event['type']] ?? 'bg-gray-100 text-gray-700';
$seatsLeft = $event['seats_total'] - $event['seats_taken'];
$progress  = ($event['seats_taken'] / $event['seats_total']) * 100;
@endphp

<div class="bg-white rounded-2xl overflow-hidden flex flex-col border border-gray-100 shadow-sm">
    {{-- Cover --}}
    <div class="relative h-40 bg-gradient-to-br from-red-50 to-gray-100 flex items-center justify-center">
        @if(!empty($event['image']))
            <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="w-full h-full object-cover">
        @else
            <span class="material-icons text-6xl text-[#E3342F]/20">event</span>
        @endif

        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-bold px-2.5 py-1 rounded-lg shadow-sm">
            {{ $event['date'] }}
        </div>
        <div class="absolute top-3 right-3 {{ $typeColor }} text-xs font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">
            {{ $event['type'] }}
        </div>
    </div>

    <div class="p-5 flex flex-col flex-1 gap-3">
        <h3 class="text-gray-900 font-semibold text-base leading-snug font-[Nunito]">{{ $event['title'] }}</h3>

        <div class="flex flex-col gap-1.5 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="material-icons text-sm text-gray-400">location_on</span>
                {{ $event['location'] }}
            </span>
            <span class="flex items-center gap-1.5">
                <span class="material-icons text-sm text-gray-400">schedule</span>
                {{ $event['time'] }}
            </span>
        </div>

        <p class="text-sm text-gray-500 line-clamp-2">{{ $event['description'] }}</p>

        {{-- Places restantes --}}
        <div class="mt-auto">
            <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                <span>{{ $event['seats_taken'] }} / {{ $event['seats_total'] }} inscrits</span>
                <span>{{ $seatsLeft }} restante(s)</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-[#E3342F] rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <a href="{{ $event['cta_url'] }}"
           class="mt-2 bg-[#E3342F] hover:bg-[#C0392B] text-white text-sm font-semibold text-center py-2.5 rounded-xl transition-colors block">
            {{ $event['cta_label'] }}
        </a>
    </div>
</div>
