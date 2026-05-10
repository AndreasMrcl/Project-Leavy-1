@php
    $checkboxClass = $accent === 'blue'
        ? 'text-blue-500 focus:ring-blue-500'
        : 'text-red-500 focus:ring-red-500';
@endphp

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Variety</label>
    <label for="{{ $prefix }}HasVariety"
        class="flex items-center gap-2 p-2.5 rounded-lg cursor-pointer hover:bg-gray-50 transition select-none">
        <input type="checkbox" id="{{ $prefix }}HasVariety" name="has_variety" value="1"
            class="w-4 h-4 rounded {{ $checkboxClass }}">
        <span class="text-sm text-gray-700">Variety (size/level/etc.)</span>
    </label>
</div>
