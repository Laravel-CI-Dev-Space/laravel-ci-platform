<x-filament-panels::page>

    {{-- ── Pending Jobs ────────────────────────────────── --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                Jobs en attente
                <span class="ml-2 inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                    {{ count($this->pendingJobs) }}
                </span>
            </h2>
        </div>

        @if(count($this->pendingJobs) === 0)
            <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">Aucun job en attente ✓</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-4">ID</th>
                            <th class="pb-3 pr-4">Classe</th>
                            <th class="pb-3 pr-4">Queue</th>
                            <th class="pb-3 pr-4">Tentatives</th>
                            <th class="pb-3 pr-4">Disponible</th>
                            <th class="pb-3">Créé</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($this->pendingJobs as $job)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2.5 pr-4 font-mono text-xs text-gray-500">{{ $job['id'] }}</td>
                                <td class="py-2.5 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ class_basename($job['class']) }}
                                    @if(str_contains($job['class'], '\\'))
                                        <span class="block text-xs text-gray-400 font-normal">{{ $job['class'] }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 pr-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $job['queue'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4">
                                    <span class="{{ $job['attempts'] > 0 ? 'text-warning-600 dark:text-warning-400 font-semibold' : 'text-gray-500' }}">
                                        {{ $job['attempts'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4 text-gray-500 text-xs">{{ $job['available_at'] }}</td>
                                <td class="py-2.5 text-gray-500 text-xs">{{ $job['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Failed Jobs ─────────────────────────────────── --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                Jobs échoués
                <span class="ml-2 inline-flex items-center rounded-full {{ count($this->failedJobs) > 0 ? 'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300' : 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300' }} px-2 py-0.5 text-xs font-medium">
                    {{ count($this->failedJobs) }}
                </span>
            </h2>
            @if(count($this->failedJobs) > 0)
                <div class="flex gap-2">
                    <button
                        wire:click="retryAll"
                        wire:confirm="Relancer tous les jobs échoués ?"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-warning-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-warning-700 transition-colors">
                        <x-heroicon-m-arrow-path class="w-3.5 h-3.5" />
                        Relancer tout
                    </button>
                    <button
                        wire:click="flushFailed"
                        wire:confirm="Supprimer définitivement tous les jobs échoués ?"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-danger-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-danger-700 transition-colors">
                        <x-heroicon-m-trash class="w-3.5 h-3.5" />
                        Vider
                    </button>
                </div>
            @endif
        </div>

        @if(count($this->failedJobs) === 0)
            <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">Aucun job échoué ✓</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-4">Classe</th>
                            <th class="pb-3 pr-4">Queue</th>
                            <th class="pb-3 pr-4">Connexion</th>
                            <th class="pb-3 pr-4">Erreur</th>
                            <th class="pb-3">Échoué</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($this->failedJobs as $job)
                            <tr class="hover:bg-danger-50/30 dark:hover:bg-danger-900/10">
                                <td class="py-2.5 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ class_basename($job['class']) }}
                                    <span class="block font-mono text-xs text-gray-400 font-normal">{{ $job['uuid'] }}</span>
                                </td>
                                <td class="py-2.5 pr-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $job['queue'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4 text-gray-500 text-xs">{{ $job['connection'] }}</td>
                                <td class="py-2.5 pr-4 text-danger-600 dark:text-danger-400 text-xs font-mono max-w-xs truncate" title="{{ $job['error'] }}">
                                    {{ $job['error'] }}
                                </td>
                                <td class="py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ $job['failed_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-filament-panels::page>
