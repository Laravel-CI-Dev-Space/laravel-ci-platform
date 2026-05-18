@props(['thread'])

<div
    class="group relative bg-white border border-gray-200 rounded-xl p-5 divide-y divide-gray-100 space-y-4 hover:border-primary transition-colors">
    <div class="pb-4 space-y-3">
        <div class="flex items-center gap-x-2">
            @if (!empty($thread['tags']))
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($thread['tags'] as $tag)
                        <x-badge :label="$tag" />
                    @endforeach
                </div>
            @endif
            <span class="text-[10px] text-gray-700">•</span>
            <span class="text-xs text-gray-500">Posté il y a 5 heures</span>
        </div>

        <h3 class="text-base font-semibold text-gray-900 leading-snug">
            <a href="{{ $thread['url'] }}" class="hover:text-primary transition-colors">
                <span class="absolute inset-0"></span>
                {{ $thread['title'] }}
            </a>
        </h3>

        <p class="text-sm text-gray-500 line-clamp-2">
            {{ $thread['excerpt'] }}
        </p>
    </div>

    <div class="flex items-center justify-between">
        <x-avatar :name="$thread['author']['name']" :src="$thread['author']['avatar']" />

        <div class="flex items-center gap-3 text-xs text-gray-400">
            @if ($thread['votes'] > 0)
                <div class="flex items-center gap-1">
                    <svg class="size-4 shrink-0" data-slot="icon" fill="none" stroke-width="1.5"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z">
                        </path>
                    </svg>
                    <span>{{ $thread['votes'] }}</span>
                </div>
            @endif

            @if ($thread['replies'] > 0)
                <div class="flex items-center gap-1">
                    <svg class="size-4 shrink-0" fill="none" stroke-width="1.5" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>{{ $thread['replies'] }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
