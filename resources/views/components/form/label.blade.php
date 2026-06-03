@props(['label', 'id', 'required' => false])

<label for="{{ $id }}" class="block text-sm font-medium text-gray-700">
    {{ $label }}

    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>