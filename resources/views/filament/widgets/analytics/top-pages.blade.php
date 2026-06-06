<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Top 15 pages visitées
        </x-slot>

        @if ($pages->isEmpty())
            <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Aucune visite enregistrée pour cette période.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="pb-3 text-left font-medium text-gray-500 dark:text-gray-400">Page</th>
                            <th class="pb-3 text-right font-medium text-gray-500 dark:text-gray-400">Vues</th>
                            <th class="pb-3 text-right font-medium text-gray-500 dark:text-gray-400">Visiteurs uniques</th>
                            <th class="pb-3 pr-2 text-left font-medium text-gray-500 dark:text-gray-400">Popularité</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($pages as $i => $page)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2.5 pr-4 text-gray-400">{{ $i + 1 }}</td>
                                <td class="py-2.5 pr-6 font-mono text-xs text-gray-700 dark:text-gray-300">
                                    {{ $page->path }}
                                </td>
                                <td class="py-2.5 pr-6 text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($page->views) }}
                                </td>
                                <td class="py-2.5 pr-6 text-right text-gray-500">
                                    {{ number_format($page->unique_visitors) }}
                                </td>
                                <td class="py-2.5 pr-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                            <div
                                                class="h-2 rounded-full bg-primary-500"
                                                style="width: {{ round($page->views / $maxViews * 100) }}%"
                                            ></div>
                                        </div>
                                        <span class="w-10 text-right text-xs text-gray-400">
                                            {{ round($page->views / $maxViews * 100) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
