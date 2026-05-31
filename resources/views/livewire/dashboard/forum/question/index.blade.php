<div>
    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="max-sm:w-full sm:flex-1">
            <h1 class="text-2xl/8 font-semibold text-zinc-950 sm:text-xl/8">
                {{ __('Forum communautaire') }}
            </h1>

            <div class="mt-4 flex max-w-xl gap-4">
                {{-- Recherche --}}
                <div class="flex-1">
                    <span data-slot="control"
                        class="relative isolate block sm:has-[[data-slot=icon]:first-child]:[&_input]:pl-8 *:data-[slot=icon]:pointer-events-none *:data-[slot=icon]:absolute *:data-[slot=icon]:z-10 *:data-[slot=icon]:size-4 sm:*:data-[slot=icon]:top-2.5 [&>[data-slot=icon]:first-child]:left-3 sm:[&>[data-slot=icon]:first-child]:left-2.5 *:data-[slot=icon]:text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                            aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd"
                                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span data-slot="control"
                            class="relative block w-full before:absolute before:inset-px before:rounded-[calc(var(--radius-lg)-1px)] before:bg-white before:shadow-sm after:pointer-events-none after:absolute after:inset-0 after:rounded-lg sm:focus-within:after:ring-2 sm:focus-within:after:ring-blue-500 has-data-disabled:opacity-50 has-data-disabled:before:bg-zinc-950/5">
                            <input wire:model.live.debounce.300ms="search"
                                placeholder="{{ __('Rechercher une question…') }}"
                                class="relative block w-full appearance-none rounded-lg px-[calc(--spacing(3.5)-1px)] py-[calc(--spacing(2.5)-1px)] sm:px-[calc(--spacing(3)-1px)] sm:py-[calc(--spacing(1.5)-1px)] text-base/6 text-zinc-950 placeholder:text-zinc-500 sm:text-sm/6 border border-zinc-950/10 hover:border-zinc-950/20 bg-white focus:outline-hidden data-invalid:border-red-500 data-disabled:border-zinc-950/20">
                        </span>
                    </span>
                </div>

                {{-- Tri --}}
                <div>
                    <span data-slot="control"
                        class="group relative block w-full before:absolute before:inset-px before:rounded-[calc(var(--radius-lg)-1px)] before:bg-white before:shadow-sm after:pointer-events-none after:absolute after:inset-0 after:rounded-lg has-data-disabled:before:shadow-none">
                        <select wire:model.live="sort"
                            class="relative block w-full appearance-none rounded-lg py-1.5 sm:py-1.5 pr-8 pl-2.5 sm:pr-8 sm:pl-2 text-base/6 text-zinc-950 placeholder:text-zinc-500 sm:text-sm/6 border border-zinc-950/10 hover:border-zinc-950/20 bg-white focus:outline-hidden data-disabled:border-zinc-950/20 data-disabled:opacity-100">
                            <option value="recent">{{ __('Plus récents') }}</option>
                            <option value="popular">{{ __('Plus populaires') }}</option>
                            <option value="unanswered">{{ __('Sans réponse') }}</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="size-5 stroke-zinc-500 group-has-data-disabled:stroke-zinc-600 sm:size-4 forced-colors:stroke-[CanvasText]"
                                viewBox="0 0 16 16" aria-hidden="true" fill="none">
                                <path d="M5.75 10.75L8 13L10.25 10.75" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M10.25 5.25L8 3L5.75 5.25" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Bouton ouvre le drawer --}}
        <button type="button" wire:click="openCreateDrawer"
            class="inline-flex items-center gap-x-2 rounded-lg border text-white text-base/6 font-semibold px-4 py-1.5 sm:text-sm/6 border-transparent bg-black hover:bg-zinc-800 transition-colors cursor-pointer">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('Poser une question') }}
        </button>
    </div>

    {{-- ── Liste ───────────────────────────────────────────────── --}}
    <div class="mt-8 space-y-3">
        @forelse($questions as $question)
            <x-card.forum-thread :thread="[
                'title' => $question->title,
                'excerpt' => $question->excerpt,
                'url' => '#',
                'tags' => [],
                'author' => [
                    'name' => $question->author->name,
                    'avatar' => $question->author->avatar ?? '',
                ],
                'votes' => 0,
                'replies' => 0,
            ]" />
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

    {{-- ── Pagination ──────────────────────────────────────────── --}}
    @if ($questions->hasPages())
        <div class="mt-6">
            {{ $questions->links() }}
        </div>
    @endif

    {{-- ── Drawer ──────────────────────────────────────────────── --}}
    <livewire:dashboard.forum.question.create-drawer />
</div>
