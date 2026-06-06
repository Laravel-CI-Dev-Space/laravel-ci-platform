<x-filament-panels::page>

    {{-- Chart.js loaded synchronously so Alpine x-init can always access it --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <div class="space-y-6 pb-8">

        {{-- ─── Header ─────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    Bienvenue, {{ auth()->user()->name }}
                </p>
                <p class="mt-0.5 text-sm text-gray-400">
                    {{ now()->translatedFormat('l d F Y') }} · {{ $periodLabel }}
                </p>
            </div>

            {{-- Period selector --}}
            <div class="flex items-center gap-1 rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                @foreach (['today' => "Aujourd'hui", '7d' => '7 jours', '30d' => '30 jours', '90d' => '3 mois'] as $key => $label)
                    <button
                        wire:click="$set('period', '{{ $key }}')"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-150
                            {{ $period === $key
                                ? 'bg-emerald-500 text-white shadow-sm'
                                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ─── KPI Cards ───────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            @php
            $kpiColorMap = [
                'bg-emerald-500' => '#10b981',
                'bg-blue-500'    => '#3b82f6',
                'bg-amber-500'   => '#f59e0b',
                'bg-violet-500'  => '#8b5cf6',
            ];
            @endphp

            @foreach ($kpis as $kpi)
            @php $hexColor = $kpiColorMap[$kpi['bg']] ?? '#10b981'; @endphp

            <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">

                {{-- Decorative accent circle --}}
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full opacity-[0.08] {{ $kpi['bg'] }}"></div>

                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                            {{ $kpi['label'] }}
                        </p>
                        <p class="mt-2 text-[2rem] font-extrabold leading-none text-gray-900 dark:text-white">
                            {{ number_format($kpi['value']) }}
                        </p>
                        <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                            @if ($kpi['trend'] !== null)
                                <span class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-xs font-bold
                                    {{ $kpi['trend'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                    {{ $kpi['trend'] >= 0 ? '↑' : '↓' }}&nbsp;{{ abs($kpi['trend']) }}%
                                </span>
                                <span class="text-xs text-gray-400">vs période préc.</span>
                            @endif
                        </div>
                        <p class="mt-1 truncate text-xs text-gray-400">{{ $kpi['sub'] }}</p>
                    </div>

                    <div class="shrink-0 rounded-xl {{ $kpi['bg'] }} p-3">
                        @switch($kpi['icon'])
                            @case('eye')
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                @break
                            @case('users')
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                @break
                            @case('calendar')
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @break
                            @case('briefcase')
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                @break
                        @endswitch
                    </div>
                </div>

                {{-- Sparkline: data stored on the wrapper div, chart rendered on inner canvas --}}
                <div
                    class="mt-4 h-12"
                    wire:key="sparkline-{{ $kpi['key'] }}-{{ $period }}"
                    data-type="{{ $kpi['chart_type'] }}"
                    data-color="{{ $hexColor }}"
                    data-labels='@json($kpi['sparkline']['labels'])'
                    data-values='@json($kpi['sparkline']['data'])'
                    x-data="{
                        chart: null,
                        chartInit() {
                            const wrap   = this.$el;
                            const canvas = wrap.querySelector('canvas');
                            const type   = wrap.dataset.type;
                            const color  = wrap.dataset.color;
                            const labels = JSON.parse(wrap.dataset.labels);
                            const values = JSON.parse(wrap.dataset.values);

                            const ds = type === 'line'
                                ? [{ data: values, borderColor: color, backgroundColor: color + '22',
                                     fill: true, tension: 0.4, pointRadius: 0, borderWidth: 2 }]
                                : [{ data: values, backgroundColor: color + 'bb',
                                     borderRadius: 3, barPercentage: 0.65 }];

                            this.chart = new Chart(canvas, {
                                type: type,
                                data: { labels: labels, datasets: ds },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 400 },
                                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                                    scales: { x: { display: false }, y: { display: false, beginAtZero: true } }
                                }
                            });
                        },
                        destroy() { this.chart?.destroy(); }
                    }"
                    x-init="chartInit()"
                >
                    <canvas class="h-full w-full"></canvas>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ─── Main Row : Visits chart + Top pages ─────────────────── --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Visits Bar Chart (2/3) --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800 lg:col-span-2">
                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Trafic quotidien</h3>
                        <p class="text-xs text-gray-400">{{ $periodLabel }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ number_format($kpis[0]['value']) }} vues
                    </span>
                </div>

                <div
                    class="relative h-72"
                    wire:key="chart-visits-{{ $period }}"
                    data-labels='@json($visitsChart['labels'])'
                    data-values='@json($visitsChart['data'])'
                    x-data="{
                        chart: null,
                        chartInit() {
                            const wrap   = this.$el;
                            const canvas = wrap.querySelector('canvas');
                            const labels = JSON.parse(wrap.dataset.labels);
                            const values = JSON.parse(wrap.dataset.values);

                            this.chart = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Pages vues',
                                        data: values,
                                        backgroundColor: function(ctx) {
                                            var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                                            g.addColorStop(0, 'rgba(16,185,129,0.9)');
                                            g.addColorStop(1, 'rgba(16,185,129,0.25)');
                                            return g;
                                        },
                                        borderRadius: 5,
                                        borderSkipped: false
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: '#1f2937',
                                            titleColor: '#9ca3af',
                                            bodyColor: '#f9fafb',
                                            padding: 10,
                                            cornerRadius: 8
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: 'rgba(0,0,0,0.04)' },
                                            ticks: { color: '#9ca3af', font: { size: 11 } }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: '#9ca3af', font: { size: 11 }, maxTicksLimit: 12 }
                                        }
                                    }
                                }
                            });
                        },
                        destroy() { this.chart?.destroy(); }
                    }"
                    x-init="chartInit()"
                >
                    <canvas class="absolute inset-0 h-full w-full"></canvas>
                </div>
            </div>

            {{-- Top Pages list (1/3) --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Pages populaires</h3>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        Top 10
                    </span>
                </div>

                @php $maxViews = $topPages->max('views') ?: 1; @endphp

                <div class="space-y-3.5">
                    @forelse ($topPages->take(9) as $i => $page)
                    <div class="flex items-center gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md text-xs font-extrabold
                            {{ $i < 3 ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300"
                               title="{{ $page->path }}">
                                {{ $page->path }}
                            </p>
                            <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1 rounded-full bg-emerald-400 transition-all duration-700"
                                     style="width: {{ round($page->views / $maxViews * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="shrink-0 text-xs font-bold text-gray-600 dark:text-gray-300">
                            {{ number_format($page->views) }}
                        </span>
                    </div>
                    @empty
                    <div class="flex h-48 flex-col items-center justify-center gap-2 text-gray-300">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-sm font-medium">Aucune visite enregistrée</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ─── Bottom Row : Events chart + Device + Summary ────────── --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Stacked Events Chart (2/3) --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800 lg:col-span-2">
                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Interactions par type</h3>
                        <p class="text-xs text-gray-400">{{ $periodLabel }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                        @foreach ($eventsChart['datasets'] as $ds)
                        <div class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-full"
                                  style="background-color: {{ $ds['backgroundColor'] }}"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ds['label'] }}</span>
                        </div>
                        @endforeach
                        @if (empty($eventsChart['datasets']))
                        <span class="text-xs text-gray-400">Aucune interaction</span>
                        @endif
                    </div>
                </div>

                @if (! empty($eventsChart['datasets']))
                <div
                    class="relative h-72"
                    wire:key="chart-events-{{ $period }}"
                    data-chart='@json($eventsChart)'
                    x-data="{
                        chart: null,
                        chartInit() {
                            const wrap   = this.$el;
                            const canvas = wrap.querySelector('canvas');
                            const d      = JSON.parse(wrap.dataset.chart);

                            this.chart = new Chart(canvas, {
                                type: 'bar',
                                data: { labels: d.labels, datasets: d.datasets },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: '#1f2937',
                                            padding: 10,
                                            cornerRadius: 8
                                        }
                                    },
                                    scales: {
                                        x: {
                                            stacked: true,
                                            grid: { display: false },
                                            ticks: { color: '#9ca3af', font: { size: 11 }, maxTicksLimit: 12 }
                                        },
                                        y: {
                                            stacked: true,
                                            beginAtZero: true,
                                            grid: { color: 'rgba(0,0,0,0.04)' },
                                            ticks: { color: '#9ca3af', font: { size: 11 } }
                                        }
                                    }
                                }
                            });
                        },
                        destroy() { this.chart?.destroy(); }
                    }"
                    x-init="chartInit()"
                >
                    <canvas class="absolute inset-0 h-full w-full"></canvas>
                </div>
                @else
                <div class="flex h-72 flex-col items-center justify-center gap-2 text-gray-300">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    <p class="text-sm font-medium">Aucune interaction sur cette période</p>
                </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="flex flex-col gap-5">

                {{-- Device Donut --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                    <h3 class="mb-5 text-base font-bold text-gray-900 dark:text-white">Appareils</h3>

                    <div class="flex items-center gap-5">
                        <div
                            class="relative h-32 w-32 shrink-0"
                            wire:key="chart-devices-{{ $period }}"
                            data-chart='@json($deviceChart)'
                            x-data="{
                                chart: null,
                                chartInit() {
                                    const wrap   = this.$el;
                                    const canvas = wrap.querySelector('canvas');
                                    const d      = JSON.parse(wrap.dataset.chart);

                                    this.chart = new Chart(canvas, {
                                        type: 'doughnut',
                                        data: {
                                            labels: d.labels,
                                            datasets: [{
                                                data: d.data,
                                                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                                                borderWidth: 0,
                                                hoverOffset: 4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            cutout: '72%',
                                            plugins: {
                                                legend: { display: false },
                                                tooltip: {
                                                    backgroundColor: '#1f2937',
                                                    padding: 8,
                                                    cornerRadius: 8
                                                }
                                            }
                                        }
                                    });
                                },
                                destroy() { this.chart?.destroy(); }
                            }"
                            x-init="chartInit()"
                        >
                            <canvas class="h-full w-full"></canvas>
                        </div>

                        <div class="flex-1 space-y-3">
                            @foreach ([
                                ['label' => 'Desktop',  'pct' => $deviceChart['desktop_pct'], 'color' => 'bg-emerald-500'],
                                ['label' => 'Mobile',   'pct' => $deviceChart['mobile_pct'],  'color' => 'bg-blue-500'],
                                ['label' => 'Tablette', 'pct' => $deviceChart['tablet_pct'],  'color' => 'bg-amber-500'],
                            ] as $device)
                            <div>
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                        <span class="inline-block h-2 w-2 rounded-full {{ $device['color'] }}"></span>
                                        {{ $device['label'] }}
                                    </span>
                                    <span class="text-xs font-extrabold text-gray-900 dark:text-white">
                                        {{ $device['pct'] }}%
                                    </span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-1.5 rounded-full {{ $device['color'] }} transition-all duration-700"
                                         style="width: {{ $device['pct'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Event Summary --}}
                <div class="flex-1 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Résumé activité</h3>
                        <span class="text-xs text-gray-400">{{ $periodLabel }}</span>
                    </div>

                    @php
                    $summaryColorMap = [
                        'emerald' => ['bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700'],
                        'blue'    => ['bar' => 'bg-blue-500',    'badge' => 'bg-blue-50 text-blue-700'],
                        'orange'  => ['bar' => 'bg-orange-500',  'badge' => 'bg-orange-50 text-orange-700'],
                        'violet'  => ['bar' => 'bg-violet-500',  'badge' => 'bg-violet-50 text-violet-700'],
                        'red'     => ['bar' => 'bg-red-500',     'badge' => 'bg-red-50 text-red-700'],
                    ];
                    $maxEvCount = max(array_column($eventSummary, 'count') ?: [1]);
                    @endphp

                    <div class="space-y-3.5">
                        @foreach ($eventSummary as $ev)
                        @php $sc = $summaryColorMap[$ev['color']] ?? $summaryColorMap['emerald']; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-2">
                                <span class="min-w-0 truncate text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $ev['label'] }}
                                </span>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-bold {{ $sc['badge'] }}">
                                    {{ number_format($ev['count']) }}
                                </span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full {{ $sc['bar'] }} transition-all duration-700"
                                     style="width: {{ $maxEvCount > 0 ? round($ev['count'] / $maxEvCount * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-filament-panels::page>
