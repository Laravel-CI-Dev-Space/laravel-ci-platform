@props(['thread'])

@php
$labelColors = [
    'AIDE'  => 'bg-blue-50 text-blue-700 border border-blue-200',
    'DÉBAT' => 'bg-purple-50 text-purple-700 border border-purple-200',
    'OFFRE' => 'bg-green-50 text-green-700 border border-green-200',
];
$labelClass = $labelColors[$thread['label']] ?? 'bg-gray-100 text-gray-600 border border-gray-200';

[$statusIcon, $statusClass] = match($thread['status']) {
    'resolved'   => ['check_circle', 'text-green-500'],
    'unanswered' => ['help_outline', 'text-gray-300'],
    default      => ['radio_button_unchecked', 'text-amber-400'],
};
@endphp

<div class="bg-white rounded-2xl p-5 flex flex-col gap-3 border border-gray-100 shadow-sm">
    {{-- Header : label + statut --}}
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold {{ $labelClass }} px-2.5 py-1 rounded-full uppercase tracking-wide">
            {{ $thread['label'] }}
        </span>
        <span class="material-icons text-xl {{ $statusClass }}" title="{{ $thread['status'] }}">{{ $statusIcon }}</span>
    </div>

    {{-- Titre + extrait --}}
    <div>
        <a href="{{ $thread['url'] }}"
           class="text-gray-900 font-semibold text-sm leading-snug hover:text-[#E3342F] transition-colors font-[Nunito]">
            {{ $thread['title'] }}
        </a>
        <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $thread['excerpt'] }}</p>
    </div>

    {{-- Tags --}}
    @if(!empty($thread['tags']))
        <div class="flex flex-wrap gap-1.5">
            @foreach($thread['tags'] as $tag)
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $tag }}</span>
            @endforeach
        </div>
    @endif

    {{-- Footer : votes, réponses, auteur --}}
    <div class="flex items-center gap-4 pt-3 border-t border-gray-100 text-xs text-gray-400">
        <span class="flex items-center gap-1">
            <span class="material-icons text-sm">thumb_up</span>
            {{ $thread['votes'] }}
        </span>
        <span class="flex items-center gap-1">
            <span class="material-icons text-sm">forum</span>
            {{ $thread['replies'] }}
        </span>

        <span class="flex items-center gap-1.5 ml-auto text-gray-500">
            @if(!empty($thread['author']['avatar']))
                <img src="{{ $thread['author']['avatar'] }}" class="w-5 h-5 rounded-full" alt="{{ $thread['author']['name'] }}">
            @else
                <div class="w-5 h-5 rounded-full bg-red-50 flex items-center justify-center text-[#E3342F] text-[10px] font-bold shrink-0">
                    {{ strtoupper(substr($thread['author']['name'], 0, 1)) }}
                </div>
            @endif
            <span>{{ $thread['author']['name'] }}</span>
            <span class="text-gray-300">·</span>
            <span>{{ $thread['posted_at'] }}</span>
        </span>
    </div>
</div>
