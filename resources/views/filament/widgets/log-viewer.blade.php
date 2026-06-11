<x-filament-widgets::widget>
    <x-filament::section heading="Journal des logs (laravel.log)" description="Dernières lignes du fichier de logs, mises à jour automatiquement">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Lignes (fichier)</p>
                <p class="text-xl font-semibold text-gray-950 dark:text-white">{{ number_format($stats['totalLines']) }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Entrées de log</p>
                <p class="text-xl font-semibold text-gray-950 dark:text-white">{{ number_format($stats['totalEntries']) }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Taille du fichier</p>
                <p class="text-xl font-semibold text-gray-950 dark:text-white">{{ $stats['fileSize'] }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Erreurs</p>
                <p class="text-xl font-semibold {{ $stats['levelCounts']['error'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-950 dark:text-white' }}">
                    {{ number_format($stats['levelCounts']['error']) }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Avertissements</p>
                <p class="text-xl font-semibold {{ $stats['levelCounts']['warning'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-950 dark:text-white' }}">
                    {{ number_format($stats['levelCounts']['warning']) }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400">Dernière écriture</p>
                <p class="text-xl font-semibold text-gray-950 dark:text-white">
                    {{ $stats['lastModified']?->diffForHumans() ?? '—' }}
                </p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-end gap-3">
            <div class="min-w-50 flex-1">
                <label class="text-xs text-gray-500 dark:text-gray-400">Rechercher</label>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Message, canal, exception…"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                />
            </div>

            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Niveau</label>
                <select
                    wire:model.live="level"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
                    <option value="">Tous</option>
                    @foreach ($levels as $levelOption)
                        <option value="{{ $levelOption }}">{{ ucfirst($levelOption) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Afficher</label>
                <select
                    wire:model.live="limit"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
                    <option value="50">50 entrées</option>
                    <option value="100">100 entrées</option>
                    <option value="200">200 entrées</option>
                    <option value="500">500 entrées</option>
                </select>
            </div>

            <div>
                <x-filament::button color="gray" wire:click="resetFilters">
                    Réinitialiser
                </x-filament::button>
            </div>
        </div>

        @if (empty($entries))
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Aucune entrée de log trouvée.</p>
        @else
            <div class="mt-4 max-h-96 overflow-y-auto rounded-lg bg-gray-950 p-3 font-mono text-xs leading-relaxed">
                @foreach ($entries as $entry)
                    @php
                        $color = match ($entry['level']) {
                            'emergency', 'alert', 'critical', 'error' => 'text-danger-400',
                            'warning', 'notice' => 'text-warning-400',
                            'debug' => 'text-gray-500',
                            default => 'text-info-400',
                        };
                    @endphp
                    <div class="border-b border-white/5 py-1 text-gray-300 last:border-b-0">
                        <span class="text-gray-500">[{{ $entry['timestamp'] }}]</span>
                        <span class="font-semibold {{ $color }}">{{ strtoupper($entry['level']) }}</span>
                        <span class="text-gray-600">{{ $entry['channel'] }}:</span>
                        <span class="break-all">{{ $entry['message'] }}</span>
                        @if ($entry['extraLines'] > 0)
                            <span class="text-gray-600">(+{{ $entry['extraLines'] }} ligne(s))</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
