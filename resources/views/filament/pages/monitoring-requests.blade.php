<x-filament-panels::page>

    {{-- ── Filter tabs ─────────────────────────────────── --}}
    <div class="flex gap-2 mb-6">
        @foreach(['slow' => 'Requêtes lentes (>500ms)', 'errors' => 'Erreurs (4xx/5xx)', 'routes' => 'Par route'] as $key => $label)
            <button
                wire:click="setFilter('{{ $key }}')"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors
                    {{ $filter === $key
                        ? 'bg-primary-600 text-white shadow-sm'
                        : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800'
                    }}">
                @if($key === 'slow')     <x-heroicon-m-clock class="w-4 h-4" />
                @elseif($key === 'errors') <x-heroicon-m-x-circle class="w-4 h-4" />
                @else                    <x-heroicon-m-map-pin class="w-4 h-4" />
                @endif
                {{ $label }}
            </button>
        @endforeach
        <span class="ml-auto text-xs text-gray-400 self-center">Dernières 24h · {{ count($this->rows) }} lignes</span>
    </div>

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">

        @if(count($this->rows) === 0)
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Aucune donnée pour cette période</p>

        @elseif($filter === 'routes')
            {{-- Route breakdown table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-4">Route / Path</th>
                            <th class="pb-3 pr-4 text-right">Requêtes</th>
                            <th class="pb-3 pr-4 text-right">Moy.</th>
                            <th class="pb-3 pr-4 text-right">Max</th>
                            <th class="pb-3 text-right">Erreurs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($this->rows as $row)
                            @php $avgMs = (int) $row->avg_ms; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2.5 pr-4 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $row->label }}</td>
                                <td class="py-2.5 pr-4 text-right text-gray-600 dark:text-gray-400">{{ number_format($row->hits) }}</td>
                                <td class="py-2.5 pr-4 text-right font-semibold {{ $avgMs > 1000 ? 'text-danger-600' : ($avgMs > 500 ? 'text-warning-600' : 'text-success-600') }}">
                                    {{ number_format($avgMs) }} ms
                                </td>
                                <td class="py-2.5 pr-4 text-right text-gray-500 text-xs">{{ number_format($row->max_ms) }} ms</td>
                                <td class="py-2.5 text-right">
                                    @if($row->errors > 0)
                                        <span class="inline-flex items-center rounded-full bg-danger-100 px-2 py-0.5 text-xs font-medium text-danger-700 dark:bg-danger-900 dark:text-danger-300">
                                            {{ $row->errors }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            {{-- Slow / Errors request list --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-3">Méthode</th>
                            <th class="pb-3 pr-4">Path</th>
                            <th class="pb-3 pr-4 text-center">Statut</th>
                            <th class="pb-3 pr-4 text-right">Durée</th>
                            <th class="pb-3">Quand</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($this->rows as $row)
                            @php
                                $ms = (int) $row->duration_ms;
                                $sc = (int) $row->status_code;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2.5 pr-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                        {{ match($row->method) {
                                            'GET'    => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            'POST'   => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                            'PUT','PATCH' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                            'DELETE' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                            default  => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ $row->method }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4 font-mono text-xs text-gray-700 dark:text-gray-300 max-w-xs truncate" title="{{ $row->path }}">
                                    {{ $row->path }}
                                    @if($row->route_name && $row->route_name !== $row->path)
                                        <span class="block text-gray-400">{{ $row->route_name }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 pr-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $sc >= 500 ? 'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300'
                                         : ($sc >= 400 ? 'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300'
                                         : 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300') }}">
                                        {{ $sc }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4 text-right font-semibold {{ $ms > 2000 ? 'text-danger-600' : ($ms > 800 ? 'text-warning-600' : 'text-gray-700 dark:text-gray-300') }}">
                                    {{ number_format($ms) }} ms
                                </td>
                                <td class="py-2.5 text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-filament-panels::page>
