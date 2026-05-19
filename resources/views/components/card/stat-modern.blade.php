@props([
    'value',
    'label',
    'users' => [],
    'iconBg' => 'bg-indigo-50',
    'iconColor' => 'text-indigo-600',
    'blobColor' => 'bg-indigo-100/50',
])

<div class="relative overflow-hidden bg-white rounded-xl border border-gray-100 shadow-2xs">
    <div class="absolute -top-12 -left-12 size-48 {{ $blobColor }} rounded-full blur-3xl pointer-events-none"
        aria-hidden="true"></div>

    <div class="relative p-5">
        <div class="flex items-center justify-between mb-5">
            <div class="flex size-10 items-center justify-center rounded-lg bg-white {{ $iconColor }} shadow-xs">
                {{ $icon }}
            </div>

            <button type="button"
                class="flex size-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-50 transition-colors"
                aria-label="Options">
                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="5" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="12" cy="19" r="1.5" />
                </svg>
            </button>
        </div>

        <p class="text-sm text-gray-500 mb-1">{{ $label }}</p>

        <div class="flex items-end justify-between gap-3">
            <span class="text-3xl font-bold text-gray-900">{{ $value }}</span>

            @if (!empty($users))
                <div class="flex items-center -space-x-2 shrink-0">
                    @foreach ($users as $user)
                        @if (!empty($user['avatar']))
                            <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" title="{{ $user['name'] }}"
                                class="size-8 rounded-full border-2 border-white object-cover">
                        @else
                            <div class="flex size-8 items-center justify-center rounded-full border-2 border-white bg-gray-200 text-gray-600 text-xs font-semibold"
                                title="{{ $user['name'] }}">
                                {{ strtoupper(substr($user['name'], 0, 1)) }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
