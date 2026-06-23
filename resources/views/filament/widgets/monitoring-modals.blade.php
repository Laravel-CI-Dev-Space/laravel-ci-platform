<div>
    {{-- Modal backdrop + panel --}}
    <div
        x-data="{ show: @entangle('open') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 pt-16"
    >
        {{-- Backdrop --}}
        <div
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            x-on:click="show = false; $wire.close()"
        ></div>

        {{-- Panel --}}
        <div
            class="relative w-full max-w-5xl rounded-2xl bg-gray-900 shadow-2xl ring-1 ring-white/10 flex flex-col max-h-[80vh]"
            x-on:click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10 flex-shrink-0">
                <h2 class="text-base font-semibold text-white">{{ $this->heading }}</h2>
                <button
                    wire:click="close"
                    class="text-gray-400 hover:text-white transition-colors rounded-lg p-1 hover:bg-white/10"
                >
                    <x-heroicon-m-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto px-6 py-4 flex-1">

                {{-- JOBS --}}
                @if($type === 'jobs')
                    @php $data = $this->rows; @endphp

                    {{-- Pending --}}
                    <h3 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                        En attente
                        <span class="rounded-full bg-primary-500/20 text-primary-400 text-xs px-2 py-0.5">{{ count($data['pending']) }}</span>
                    </h3>
                    @if(empty($data['pending']))
                        <p class="text-sm text-gray-500 mb-6">Aucun job en attente ✓</p>
                    @else
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-xs text-left">
                                <thead><tr class="text-gray-500 border-b border-white/5">
                                    <th class="pb-2 pr-4 font-medium">Classe</th>
                                    <th class="pb-2 pr-4 font-medium">Queue</th>
                                    <th class="pb-2 pr-4 font-medium text-center">Essais</th>
                                    <th class="pb-2 font-medium">Créé</th>
                                </tr></thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($data['pending'] as $job)
                                    <tr>
                                        <td class="py-2 pr-4 text-gray-200 font-medium">{{ class_basename($job['class']) }}</td>
                                        <td class="py-2 pr-4"><span class="rounded-full bg-blue-500/20 text-blue-300 px-2 py-0.5">{{ $job['queue'] }}</span></td>
                                        <td class="py-2 pr-4 text-center {{ $job['attempts'] > 0 ? 'text-warning-400 font-semibold' : 'text-gray-500' }}">{{ $job['attempts'] }}</td>
                                        <td class="py-2 text-gray-500">{{ $job['created_at'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Failed --}}
                    <h3 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                        Échoués
                        <span class="rounded-full {{ count($data['failed']) > 0 ? 'bg-danger-500/20 text-danger-400' : 'bg-success-500/20 text-success-400' }} text-xs px-2 py-0.5">{{ count($data['failed']) }}</span>
                        @if(count($data['failed']) > 0)
                            <div class="ml-auto flex gap-2">
                                <button wire:click="retryAllFailed" wire:confirm="Relancer tous les jobs échoués ?" class="rounded-lg bg-warning-600 text-white text-xs px-3 py-1 hover:bg-warning-700 transition-colors">Relancer tout</button>
                                <button wire:click="flushFailed" wire:confirm="Supprimer tous les jobs échoués ?" class="rounded-lg bg-danger-600 text-white text-xs px-3 py-1 hover:bg-danger-700 transition-colors">Vider</button>
                            </div>
                        @endif
                    </h3>
                    @if(empty($data['failed']))
                        <p class="text-sm text-gray-500">Aucun job échoué ✓</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead><tr class="text-gray-500 border-b border-white/5">
                                    <th class="pb-2 pr-4 font-medium">Classe</th>
                                    <th class="pb-2 pr-4 font-medium">Queue</th>
                                    <th class="pb-2 pr-4 font-medium">Erreur</th>
                                    <th class="pb-2 font-medium">Échoué</th>
                                </tr></thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($data['failed'] as $job)
                                    <tr>
                                        <td class="py-2 pr-4 text-gray-200 font-medium">
                                            {{ class_basename($job['class']) }}
                                            <span class="block font-mono text-gray-500">{{ substr($job['uuid'], 0, 8) }}…</span>
                                        </td>
                                        <td class="py-2 pr-4"><span class="rounded-full bg-blue-500/20 text-blue-300 px-2 py-0.5">{{ $job['queue'] }}</span></td>
                                        <td class="py-2 pr-4 text-danger-400 font-mono max-w-xs truncate" title="{{ $job['error'] }}">{{ $job['error'] }}</td>
                                        <td class="py-2 text-gray-500 whitespace-nowrap">{{ $job['failed_at'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                {{-- REQUESTS (slow / errors) --}}
                @elseif(in_array($type, ['slow', 'errors']))
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead><tr class="text-gray-500 border-b border-white/5">
                                <th class="pb-2 pr-3 font-medium">Méthode</th>
                                <th class="pb-2 pr-4 font-medium">Path</th>
                                <th class="pb-2 pr-4 text-center font-medium">Statut</th>
                                <th class="pb-2 pr-4 text-right font-medium">Durée</th>
                                <th class="pb-2 font-medium">Quand</th>
                            </tr></thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($this->rows as $row)
                                @php $ms = (int)$row->duration_ms; $sc = (int)$row->status_code; @endphp
                                <tr>
                                    <td class="py-2 pr-3">
                                        <span class="rounded-full px-2 py-0.5 font-bold text-xs {{ match($row->method) { 'GET' => 'bg-blue-500/20 text-blue-300', 'POST' => 'bg-green-500/20 text-green-300', 'DELETE' => 'bg-red-500/20 text-red-300', default => 'bg-gray-500/20 text-gray-300' } }}">{{ $row->method }}</span>
                                    </td>
                                    <td class="py-2 pr-4 font-mono text-gray-300 max-w-xs truncate" title="{{ $row->path }}">{{ $row->path }}</td>
                                    <td class="py-2 pr-4 text-center">
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $sc >= 500 ? 'bg-danger-500/20 text-danger-400' : ($sc >= 400 ? 'bg-warning-500/20 text-warning-400' : 'bg-success-500/20 text-success-400') }}">{{ $sc }}</span>
                                    </td>
                                    <td class="py-2 pr-4 text-right font-semibold {{ $ms > 2000 ? 'text-danger-400' : ($ms > 800 ? 'text-warning-400' : 'text-gray-300') }}">{{ number_format($ms) }} ms</td>
                                    <td class="py-2 text-gray-500">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                {{-- ROUTES --}}
                @elseif($type === 'routes')
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead><tr class="text-gray-500 border-b border-white/5">
                                <th class="pb-2 pr-4 font-medium">Route / Path</th>
                                <th class="pb-2 pr-4 text-right font-medium">Requêtes</th>
                                <th class="pb-2 pr-4 text-right font-medium">Moy.</th>
                                <th class="pb-2 pr-4 text-right font-medium">Max</th>
                                <th class="pb-2 text-right font-medium">Erreurs</th>
                            </tr></thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($this->rows as $row)
                                @php $avg = (int)$row->avg_ms; @endphp
                                <tr>
                                    <td class="py-2 pr-4 font-mono text-gray-300">{{ $row->label }}</td>
                                    <td class="py-2 pr-4 text-right text-gray-400">{{ number_format($row->hits) }}</td>
                                    <td class="py-2 pr-4 text-right font-semibold {{ $avg > 1000 ? 'text-danger-400' : ($avg > 500 ? 'text-warning-400' : 'text-success-400') }}">{{ number_format($avg) }} ms</td>
                                    <td class="py-2 pr-4 text-right text-gray-500">{{ number_format($row->max_ms) }} ms</td>
                                    <td class="py-2 text-right">
                                        @if($row->errors > 0)
                                            <span class="rounded-full bg-danger-500/20 text-danger-400 px-2 py-0.5">{{ $row->errors }}</span>
                                        @else
                                            <span class="text-gray-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>{{-- /body --}}
        </div>{{-- /panel --}}
    </div>{{-- /modal --}}
</div>
