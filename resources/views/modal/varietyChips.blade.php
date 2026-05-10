@php
    $showRecipeWarning = $showRecipeWarning ?? false;
    $ringClass = $accent === 'blue' ? 'focus-within:ring-blue-500' : 'focus-within:ring-red-500';
@endphp

<div id="{{ $prefix }}VarietySection" class="hidden">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Variety List <span class="text-red-500">*</span>
    </label>
    <div id="{{ $prefix }}VarietyChips"
        class="min-h-11 w-full rounded-lg border border-gray-300 shadow-sm p-2 flex flex-wrap gap-1.5 items-center focus-within:ring-2 focus-within:border-transparent transition {{ $ringClass }}">
        <input type="text" id="{{ $prefix }}VarietyInput"
            class="varietyChipInput flex-1 min-w-30 outline-none border-0 p-1 text-sm"
            placeholder="Type variety, press Enter">
    </div>
    <p class="text-xs text-gray-500 mt-1">
        Min 2 variety. Press Enter or comma to add.@if ($showRecipeWarning) Variety that is deleted will also remove its recipe.@endif
    </p>
    <div class="error-message hidden" id="{{ $prefix }}VarietyError"></div>
</div>
