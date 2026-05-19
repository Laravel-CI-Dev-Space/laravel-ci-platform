@props(['icon', 'value', 'description'])

<div class="bg-white rounded-xl p-4 flex items-center gap-3 border border-gray-100 shadow-sm">
    <span class="material-icons text-2xl text-[#F97316]">{{ $icon }}</span>
    <div>
        <div class="text-xl font-bold text-gray-900 leading-tight font-[Nunito]">{{ $value }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $description }}</div>
    </div>
</div>
