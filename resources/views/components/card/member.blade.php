@props(['member', 'rank' => null])

@php
$rankBadges = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
$rankBadge  = isset($rankBadges[$rank]) ? $rankBadges[$rank] : null;
@endphp

<div class="bg-white rounded-2xl p-4 flex items-center gap-4 border border-gray-100 shadow-sm">
    {{-- Avatar --}}
    <div class="relative shrink-0">
        @if(!empty($member['avatar']))
            <img src="{{ $member['avatar'] }}" alt="{{ $member['username'] }}"
                 class="w-12 h-12 rounded-full object-cover">
        @else
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-[#E3342F] font-bold text-lg">
                {{ strtoupper(substr($member['username'], 0, 1)) }}
            </div>
        @endif
        @if($rankBadge)
            <span class="absolute -top-1 -right-1 text-lg leading-none">{{ $rankBadge }}</span>
        @endif
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <a href="{{ $member['url'] }}"
           class="text-gray-900 font-semibold text-sm hover:text-[#E3342F] transition-colors truncate block font-[Nunito]">
            {{ $member['username'] }}
        </a>
        <div class="flex items-center gap-1 mt-0.5">
            <span class="material-icons text-sm text-[#F97316]">stars</span>
            <span class="text-xs text-gray-500">{{ number_format($member['points']) }} pts</span>
        </div>
    </div>

    @if($rank)
        <span class="text-2xl font-bold text-gray-200 tabular-nums">#{{ $rank }}</span>
    @endif
</div>
