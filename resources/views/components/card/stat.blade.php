@props(['icon', 'value', 'label'])

<div class="bg-white rounded-2xl p-6 flex flex-col items-center gap-3 text-center border border-gray-100 shadow-sm">
    <span class="material-icons text-4xl text-[#E3342F]">{{ $icon }}</span>
    <span class="text-3xl font-bold text-gray-900 font-[Nunito]">{{ $value }}</span>
    <span class="text-sm text-gray-500">{{ $label }}</span>
</div>
