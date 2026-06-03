<div>
    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="max-sm:w-full sm:flex-1">
            <h1 class="text-2xl/8 font-semibold text-zinc-950 sm:text-xl/8">
                {{ __('Forum communautaire') }}
            </h1>

            <div class="mt-4 flex max-w-xl gap-2">
                <label for="search" class="sr-only">
                    {{ __('Rechercher') }}
                </label>
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                        class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                            clip-rule="evenodd" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search"
                        placeholder="{{ __('Rechercher une question…') }}"
                        class="w-full sm:w-72 rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3.5 text-sm text-zinc-900 placeholder:text-zinc-400 hover:border-zinc-300 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>

                <div class="relative">
                    <label for="sort" class="sr-only">
                        {{ __('Trier par') }}
                    </label>
                    <select wire:model.live="sort" id="sort"
                        class="w-full sm:w-40 appearance-none rounded-lg border border-zinc-200 bg-white py-2 pl-3 pr-8 text-sm text-zinc-900 hover:border-zinc-300 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-black/10">
                        <option value="recent">{{ __('Plus récents') }}</option>
                        <option value="popular">{{ __('Plus populaires') }}</option>
                        <option value="unanswered">{{ __('Sans réponse') }}</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <svg class="size-4 text-zinc-400" viewBox="0 0 16 16" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="1.5">
                            <path d="M5.75 10.75L8 13L10.25 10.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10.25 5.25L8 3L5.75 5.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <button type="button" wire:click="openCreateDrawer"
            class="inline-flex items-center gap-x-2 rounded-lg border text-white text-base/6 font-semibold px-4 py-1.5 sm:text-sm/6 border-transparent bg-black hover:bg-zinc-800 transition-colors cursor-pointer">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('Poser une question') }}
        </button>
    </div>

    <div class="mt-8 space-y-3">
        @forelse($questions as $question)
            <x-card.forum-thread :question="$question" />
        @empty
            <div class="py-16 text-center">
                <svg class="mx-auto size-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('Aucune question pour l\'instant.') }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ __('Sois le premier à poser une question !') }}</p>
            </div>
        @endforelse
    </div>

    @if ($questions->hasPages())
        <div class="mt-6">
            {{ $questions->links() }}
        </div>
    @endif

    <livewire:dashboard.forum.question.create-drawer />
</div>