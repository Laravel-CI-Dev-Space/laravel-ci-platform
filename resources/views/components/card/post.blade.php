@props(['post'])

<div class="bg-white rounded-2xl overflow-hidden flex flex-col border border-gray-100 shadow-sm">
    {{-- Cover --}}
    <div class="h-44 bg-gradient-to-br from-red-50 to-gray-100 flex items-center justify-center">
        @if(!empty($post['image']))
            <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover">
        @else
            <span class="material-icons text-6xl text-[#E3342F]/20">article</span>
        @endif
    </div>

    <div class="p-5 flex flex-col flex-1 gap-3">
        {{-- Catégorie + temps de lecture --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold bg-red-50 text-[#E3342F] px-2.5 py-1 rounded-full">
                {{ $post['category'] }}
            </span>
            <span class="text-xs text-gray-400 flex items-center gap-1">
                <span class="material-icons text-sm">schedule</span>
                {{ $post['read_time'] }} min de lecture
            </span>
        </div>

        <h3 class="text-gray-900 font-semibold text-base leading-snug font-[Nunito]">{{ $post['title'] }}</h3>
        <p class="text-sm text-gray-500 line-clamp-3">{{ $post['excerpt'] }}</p>

        {{-- Auteur + date --}}
        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
            @if(!empty($post['author']['avatar']))
                <img src="{{ $post['author']['avatar'] }}"
                     class="w-7 h-7 rounded-full object-cover"
                     alt="{{ $post['author']['name'] }}">
            @else
                <div class="w-7 h-7 rounded-full bg-red-50 flex items-center justify-center text-[#E3342F] text-xs font-bold shrink-0">
                    {{ strtoupper(substr($post['author']['name'], 0, 1)) }}
                </div>
            @endif
            <span class="text-xs text-gray-600 truncate">{{ $post['author']['name'] }}</span>
            <span class="text-xs text-gray-400 ml-auto shrink-0">{{ $post['published_at'] }}</span>
        </div>
    </div>
</div>
