@props(['job'])

@php
    $data = $job instanceof \App\Models\JobOffer
        ? $job->toCardData()
        : $job;

    $contractColors = [
        'CDI' => 'green',
        'CDD' => 'blue',
        'Freelance' => 'orange',
        'Stage' => 'purple',
    ];

    $contractClass = $contractColors[$data['contract']] ?? 'gray';
@endphp

<div class="bg-white rounded-2xl p-5 flex flex-col gap-4 border border-gray-100 shadow-sm">
    <div class="flex items-center gap-3">
        @if (!empty($data['logo']))
            <img src="{{ $data['logo'] }}" alt="{{ $data['company'] }}"
                class="w-12 h-12 rounded-xl object-contain bg-gray-50 p-1 border border-gray-100 shrink-0">
        @else
            <div
                class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-[#E3342F] font-bold text-lg shrink-0">
                {{ strtoupper(substr($data['company'], 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <div class="text-gray-900 font-semibold text-sm truncate font-[Nunito]">{{ $data['title'] }}</div>
            <div class="text-xs text-gray-500">{{ $data['company'] }}</div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <x-badge-rounded :label="$data['contract']" :color="$contractClass" />

        @if ($data['remote'])
            <x-badge-rounded label="Remote" color="gray" />
        @endif

        <x-badge-rounded :label="$data['location']" color="gray">
            <x-slot:icon>
                <svg class="size-3.5" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"></path>
                </svg>
            </x-slot:icon>
        </x-badge-rounded>
    </div>

    <div class="flex flex-wrap gap-1.5">
        @foreach ($data['stack'] as $tech)
            <x-badge :label="$tech" />
        @endforeach
    </div>

    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
        <div class="text-xs text-gray-500">
            @if (!empty($data['salary']))
                <span class="text-gray-900 font-semibold">{{ $data['salary'] }}</span>
            @else
                Salaire non précisé
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">{{ $data['posted_at'] }}</span>
            <a href="{{ $data['url'] }}"
                class="text-xs bg-[#E3342F] hover:bg-[#C0392B] text-white font-semibold px-3 py-1.5 rounded-lg transition-colors">
                Postuler
            </a>
        </div>
    </div>
</div>
