@props(['post'])

<div
    class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-2xs hover:border-primary/50 transition-colors">
    <div class="h-44 bg-gradient-to-br from-red-50 to-gray-100 flex items-center justify-center">
        @if (!empty($post['image']))
            <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover">
        @else
            <span class="material-icons text-6xl text-[#E3342F]/20">article</span>
        @endif
    </div>

    <div class="p-5 flex flex-col flex-1 gap-5">
        <div class="flex items-center gap-3">
            <x-badge-rounded :label="$post['category']" color="red" />

            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <svg class="size-4" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                </svg>
                {{ $post['read_time'] }} min de lecture
            </span>
        </div>

        <h3 class="text-base font-semibold text-gray-900 leading-snug">
            <a href="#" class="hover:text-primary transition-colors">
                <span class="absolute inset-0"></span>
                {{ $post['title'] }}
            </a>
        </h3>
        <p class="text-sm text-gray-500 line-clamp-3">
            {{ $post['excerpt'] }}
        </p>

        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
            <x-avatar :name="$post['author']['name']" :src="$post['author']['avatar']" />

            <span class="text-xs text-gray-400 ml-auto shrink-0">{{ $post['published_at'] }}</span>
        </div>
    </div>
</div>
