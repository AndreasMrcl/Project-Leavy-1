<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Ingredient</title>
    @include('layout.head')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-gray-50">

    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')

        <div class="p-5">
            <div class='w-full bg-white rounded-xl h-fit mx-auto'>
                <div class="p-3 text-center">
                    <h1 class="font-extrabold text-3xl">Edit Ingredient</h1>
                    <p class="text-sm text-gray-500">{{ $menu->name }}</p>
                </div>
                <div class="p-6">
                    @php
                        $varieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];
                        $rowsByVariety = [];
                        foreach ($menu->invents as $inv) {
                            $v = $inv->pivot->variety ?? 'normal';
                            $rowsByVariety[$v][] = [
                                'invent_id' => $inv->id,
                                'quantity_used' => $inv->pivot->quantity_used,
                            ];
                        }
                    @endphp

                    <form id="ingredientForm" class="space-y-3" method="post" action="{{ route('updateingridient', $menu->id) }}">
                        @csrf
                        @method('put')

                        <div class="space-y-2">
                            <label class="font-semibold text-black">Product:</label>
                            <select name="menu_id"
                                class="bg-gray-200 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" disabled>
                                <option value="{{ $menu->id }}" selected>{{ $menu->name }}</option>
                            </select>
                        </div>

                        @if (count($varieties) > 1)
                            <div id="varietyTabs" class="border-b border-gray-200">
                                <div id="varietyTabsList" class="flex gap-2 -mb-px">
                                    @foreach ($varieties as $i => $v)
                                        <button type="button" data-variety="{{ $v }}"
                                            class="varietyTab px-4 py-2 font-semibold text-sm border-b-2 {{ $i === 0 ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                            {{ ucwords(str_replace('_', ' ', $v)) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div id="varietyPanels">
                            @foreach ($varieties as $i => $v)
                                @php $rows = $rowsByVariety[$v] ?? [['invent_id' => null, 'quantity_used' => null]]; @endphp
                                <div class="varietyPanel space-y-3 {{ $i === 0 ? '' : 'hidden' }}" data-variety="{{ $v }}">
                                    <div class="ingredient-wrapper space-y-3">
                                        @foreach ($rows as $idx => $row)
                                            <div class="ingredient-row flex justify-between gap-2">
                                                <select name="ingredients[{{ $v }}][{{ $idx }}][invent_id]"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/2"
                                                    required>
                                                    <option value="">-- pilih bahan --</option>
                                                    @foreach ($invents as $invent)
                                                        <option value="{{ $invent->id }}"
                                                            {{ $invent->id == $row['invent_id'] ? 'selected' : '' }}>
                                                            {{ $invent->name }} ({{ $invent->unit }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="number" step="0.01" min="0.01"
                                                    name="ingredients[{{ $v }}][{{ $idx }}][quantity_used]"
                                                    value="{{ $row['quantity_used'] }}"
                                                    placeholder="Quantity"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/3"
                                                    required>
                                                <button type="button"
                                                    class="remove-row bg-red-500 text-white p-2 px-4 rounded-xl hover:text-black">X</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button"
                                        class="addRow bg-green-500 text-white px-4 p-2 rounded-lg hover:text-black"
                                        data-variety="{{ $v }}">+ Bahan</button>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit"
                            class="bg-blue-500 text-white p-4 w-full hover:text-black rounded-lg">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        const inventOptions = @json($invents->map(fn ($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit])->all());

        function buildOptions() {
            return inventOptions
                .map(o => `<option value="${o.id}">${o.name} (${o.unit})</option>`)
                .join('');
        }

        function newRow(variety, index) {
            return `
                <div class="ingredient-row flex justify-between gap-2">
                    <select name="ingredients[${variety}][${index}][invent_id]"
                        class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/2" required>
                        <option value="">-- pilih bahan --</option>
                        ${buildOptions()}
                    </select>
                    <input type="number" step="0.01" min="0.01" name="ingredients[${variety}][${index}][quantity_used]"
                        placeholder="Quantity"
                        class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/3" required>
                    <button type="button"
                        class="remove-row bg-red-500 text-white p-2 px-4 rounded-xl hover:text-black">X</button>
                </div>
            `;
        }

        document.addEventListener('click', function (e) {
            if (e.target.closest('.varietyTab')) {
                const btn = e.target.closest('.varietyTab');
                const variety = btn.dataset.variety;
                document.querySelectorAll('.varietyTab').forEach(t => {
                    t.classList.remove('border-orange-500', 'text-orange-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                btn.classList.add('border-orange-500', 'text-orange-600');
                btn.classList.remove('border-transparent', 'text-gray-500');
                document.querySelectorAll('.varietyPanel').forEach(p => {
                    p.classList.toggle('hidden', p.dataset.variety !== variety);
                });
            }

            if (e.target.classList.contains('addRow')) {
                const variety = e.target.dataset.variety;
                const wrap = e.target.previousElementSibling;
                const idx = wrap.querySelectorAll('.ingredient-row').length;
                wrap.insertAdjacentHTML('beforeend', newRow(variety, idx));
            }

            if (e.target.classList.contains('remove-row')) {
                const wrap = e.target.closest('.ingredient-wrapper');
                if (wrap.querySelectorAll('.ingredient-row').length > 1) {
                    e.target.closest('.ingredient-row').remove();
                }
            }
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
