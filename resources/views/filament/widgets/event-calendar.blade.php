<x-filament-widgets::widget>
<div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-950 dark:text-white">
            Calendrier des événements
        </h2>
        <div class="flex items-center gap-3">
            <button wire:click="prevMonth" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <x-heroicon-m-chevron-left class="w-4 h-4" />
            </button>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 min-w-[140px] text-center">
                {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
            </span>
            <button wire:click="nextMonth" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <x-heroicon-m-chevron-right class="w-4 h-4" />
            </button>
        </div>
    </div>

    {{-- ── Day headers ── --}}
    <div class="grid grid-cols-7 mb-1">
        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dow)
            <div class="py-1 text-center text-xs font-medium text-gray-400 dark:text-gray-500">{{ $dow }}</div>
        @endforeach
    </div>

    {{-- ── Calendar grid ── --}}
    <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
        @foreach($this->calendarDays as $cell)
            @if($cell['day'] === null)
                <div class="bg-gray-50 dark:bg-gray-900/50 min-h-[64px]"></div>
            @else
                <button
                    wire:click="selectDate('{{ $cell['date'] }}')"
                    @class([
                        'bg-white dark:bg-gray-900 min-h-[64px] p-1.5 text-left transition-colors w-full text-left',
                        'hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer' => !empty($cell['events']),
                        'cursor-default' => empty($cell['events']),
                        'ring-2 ring-inset ring-primary-500' => $selectedDate === $cell['date'] && $showModal,
                    ])
                >
                    <span @class([
                        'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium mb-1',
                        'bg-primary-600 text-white font-bold' => $cell['today'],
                        'text-gray-700 dark:text-gray-300' => !$cell['today'],
                    ])>
                        {{ $cell['day'] }}
                    </span>

                    @foreach(array_slice($cell['events'], 0, 3) as $ev)
                        <div class="truncate text-xs rounded px-1 py-0.5 mb-0.5 font-medium
                            {{ match($ev['type']) {
                                'workshop'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                'conference'=> 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                'hackathon' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                default     => 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300',
                            } }}"
                            title="{{ $ev['title'] }}">
                            {{ Str::limit($ev['title'], 18) }}
                        </div>
                    @endforeach

                    @if(count($cell['events']) > 3)
                        <div class="text-xs text-gray-400 pl-1">+{{ count($cell['events']) - 3 }}</div>
                    @endif
                </button>
            @endif
        @endforeach
    </div>

    {{-- ── Modal ── --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-on:click="show = false; $wire.closeModal()"></div>

        <div
            class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-gray-950/10 dark:ring-white/10 overflow-hidden"
            x-on:click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/10">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d F Y') : '' }}
                    </p>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ count($selectedEvents) }} événement{{ count($selectedEvents) > 1 ? 's' : '' }}
                    </h3>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg p-1 transition-colors">
                    <x-heroicon-m-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal body --}}
            <div class="divide-y divide-gray-100 dark:divide-white/5 max-h-[60vh] overflow-y-auto">
                @foreach($selectedEvents as $ev)
                    <div class="px-5 py-4">
                        {{-- Type + Status badges --}}
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ match($ev['type']) {
                                    'workshop'   => 'bg-blue-100 text-blue-700',
                                    'conference' => 'bg-purple-100 text-purple-700',
                                    'hackathon'  => 'bg-green-100 text-green-700',
                                    default      => 'bg-orange-100 text-orange-700',
                                } }}">
                                {{ ucfirst($ev['type']) }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ match($ev['status']) {
                                    'published' => 'bg-green-100 text-green-700',
                                    'draft'     => 'bg-gray-100 text-gray-600',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ match($ev['status']) { 'published' => 'Publié', 'draft' => 'Brouillon', 'cancelled' => 'Annulé', default => $ev['status'] } }}
                            </span>
                            @if($ev['is_paid'])
                                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2.5 py-0.5 text-xs font-medium">
                                    Payant · {{ number_format($ev['price'] ?? 0, 0, ',', ' ') }} {{ $ev['currency'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-0.5 text-xs font-medium">Gratuit</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">{{ $ev['title'] }}</h4>

                        {{-- Meta --}}
                        <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-m-clock class="w-3.5 h-3.5 flex-shrink-0" />
                                {{ $ev['starts_at'] }}{{ $ev['ends_at'] ? ' — ' . $ev['ends_at'] : '' }}
                            </div>
                            @if($ev['location'])
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-m-map-pin class="w-3.5 h-3.5 flex-shrink-0" />
                                    {{ $ev['location'] }}
                                </div>
                            @endif
                            @if($ev['capacity'])
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-m-user-group class="w-3.5 h-3.5 flex-shrink-0" />
                                    {{ $ev['registered'] }} / {{ $ev['capacity'] }} inscrits
                                    @php $pct = $ev['capacity'] > 0 ? round(($ev['registered'] / $ev['capacity']) * 100) : 0; @endphp
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 ml-1 max-w-[80px]">
                                        <div class="h-1.5 rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-green-500') }}"
                                             style="width:{{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="{{ $pct >= 90 ? 'text-red-500' : 'text-gray-400' }}">{{ $pct }}%</span>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ $ev['view_url'] }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                <x-heroicon-m-eye class="w-3.5 h-3.5" /> Voir
                            </a>
                            <a href="{{ $ev['edit_url'] }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700 transition-colors">
                                <x-heroicon-m-pencil-square class="w-3.5 h-3.5" /> Modifier
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</x-filament-widgets::widget>
