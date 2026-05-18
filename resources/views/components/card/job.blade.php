@props(['job'])

@php
$contractColors = [
    'CDI'       => 'bg-green-50 text-green-700 border border-green-200',
    'CDD'       => 'bg-blue-50 text-blue-700 border border-blue-200',
    'Freelance' => 'bg-orange-50 text-orange-700 border border-orange-200',
    'Stage'     => 'bg-purple-50 text-purple-700 border border-purple-200',
];
$contractClass = $contractColors[$job['contract']] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
@endphp

<div class="bg-white rounded-2xl p-5 flex flex-col gap-4 border border-gray-100 shadow-sm">
    {{-- Entreprise --}}
    <div class="flex items-center gap-3">
        @if(!empty($job['logo']))
            <img src="{{ $job['logo'] }}" alt="{{ $job['company'] }}"
                 class="w-12 h-12 rounded-xl object-contain bg-gray-50 p-1 border border-gray-100 shrink-0">
        @else
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-[#E3342F] font-bold text-lg shrink-0">
                {{ strtoupper(substr($job['company'], 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <div class="text-gray-900 font-semibold text-sm truncate font-[Nunito]">{{ $job['title'] }}</div>
            <div class="text-xs text-gray-500">{{ $job['company'] }}</div>
        </div>
    </div>

    {{-- Badges contrat / remote / localisation --}}
    <div class="flex flex-wrap gap-2">
        <span class="text-xs font-medium {{ $contractClass }} px-2.5 py-1 rounded-full">
            {{ $job['contract'] }}
        </span>
        @if($job['remote'])
            <span class="text-xs font-medium bg-teal-50 text-teal-700 border border-teal-200 px-2.5 py-1 rounded-full">
                Remote
            </span>
        @endif
        <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full flex items-center gap-1">
            <span class="material-icons text-sm">location_on</span>
            {{ $job['location'] }}
        </span>
    </div>

    {{-- Stack --}}
    <div class="flex flex-wrap gap-1.5">
        @foreach($job['stack'] as $tech)
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono">{{ $tech }}</span>
        @endforeach
    </div>

    {{-- Footer : salaire + date + CTA --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
        <div class="text-xs text-gray-500">
            @if(!empty($job['salary']))
                <span class="text-gray-900 font-semibold">{{ $job['salary'] }}</span>
            @else
                Salaire non précisé
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">{{ $job['posted_at'] }}</span>
            <a href="{{ $job['url'] }}"
               class="text-xs bg-[#E3342F] hover:bg-[#C0392B] text-white font-semibold px-3 py-1.5 rounded-lg transition-colors">
                Postuler
            </a>
        </div>
    </div>
</div>
