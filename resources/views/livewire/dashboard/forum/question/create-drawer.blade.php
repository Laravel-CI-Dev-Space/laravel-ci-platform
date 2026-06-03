
<div
    x-data="{ open: @entangle('open') }"
    @open-create-drawer.window="open = true"
    @keydown.window.escape="open && $wire.closeDrawer()"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px]"
        @click="$wire.closeDrawer()"
        style="display: none"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed -m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-gray-900/10 ring-inset lg:-m-4 lg:rounded-2xl lg:p-4 top-2 lg:top-20 right-2 lg:right-5 bottom-4 lg:bottom-8 z-50 w-3xl max-w-[calc(100vw-4rem)]"
        @click.stop
        style="display: none"
    >
        <div class="flex h-full flex-col rounded-md bg-white shadow-xl ring-1 ring-gray-900/10">
            <div class="flex shrink-0 items-start justify-between border-b border-gray-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ __('Poser une question') }}
                    </h2>
                    <p class="mt-0.5 text-xs text-gray-400">
                        {{ __('Markdown supporté · les tags arrivent prochainement') }}
                    </p>
                </div>
                <button wire:click="closeDrawer" type="button"
                    class="ml-4 mt-0.5 shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-50 hover:text-gray-600">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Titre') }} <span class="text-red-400">*</span>
                        </label>
                        <input wire:model.blur="title"
                            type="text"
                            placeholder="{{ __('Ex : Comment implémenter une API REST avec Laravel ?') }}"
                            autocomplete="off"
                            class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition-colors focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 @error('title') border-red-400 focus:ring-red-100 @else border-gray-200 @enderror">
                        @error('title')
                            <p class="flex items-center gap-1 text-xs text-red-500">
                                <svg class="size-3 shrink-0" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Contenu') }} <span class="text-red-400">*</span>
                        </label>
                        <textarea wire:model.blur="body"
                            rows="12"
                            placeholder="{{ __("Décris ton problème en détail...\n\n\`\`\`php\n// Ton code ici\n\`\`\`") }}"
                            class="w-full resize-none rounded-lg border px-3.5 py-2.5 font-mono text-sm leading-relaxed text-gray-900 placeholder:text-gray-400 transition-colors focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 @error('body') border-red-400 focus:ring-red-100 @else border-gray-200 @enderror">
                        </textarea>
                        @error('body')
                            <p class="flex items-center gap-1 text-xs text-red-500">
                                <svg class="size-3 shrink-0" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-400">{{ __('Minimum 30 caractères · Markdown supporté') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2 border-t border-gray-100 px-6 py-4">
                <button wire:click="closeDrawer" type="button"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900">
                    {{ __('Annuler') }}
                </button>
                <button wire:click="save" wire:loading.attr="disabled" type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-60">
                    <svg wire:loading wire:target="save"
                        class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    {{ __('Poster la question') }}
                </button>
            </div>
        </div>
    </div>
</div>
