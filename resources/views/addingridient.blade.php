<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Ingredient</title>
    @include('layout.head')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i>Add Ingredient
                    </h1>
                    <p class="text-sm text-gray-500">Organize system ingredients</p>
                </div>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                <form id="ingredientForm" class="space-y-3" method="post" action="{{ route('postingridient') }}">
                    @csrf
                    @method('post')

                    <!-- Pilih Menu -->
                    <div class="space-y-2">
                        <label class="font-semibold text-black">Product:</label>
                        <select id="menu" name="menu_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($menus as $men)
                                <option value="{{ $men->id }}" data-has-variety="{{ $men->has_variety ? '1' : '0' }}"
                                    data-varieties="{{ json_encode($men->has_variety ? ($men->varieties ?? ['normal']) : ['normal']) }}">
                                    {{ $men->name }}{{ $men->has_variety ? ' (variety)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Variety Tabs -->
                    <div id="varietyTabs" class="hidden border-b border-gray-200">
                        <div id="varietyTabsList" class="flex gap-2 -mb-px"></div>
                    </div>

                    <!-- Ingredient Panels (one per variety) -->
                    <div id="varietyPanels" class="hidden"></div>

                    <p id="emptyHint" class="text-sm text-gray-400 italic text-center py-6">Pilih produk terlebih dahulu
                        untuk mengisi resep.</p>

                    <!-- Submit -->
                    <button type="submit" id="submitBtn"
                        class="bg-blue-500 text-white p-4 w-full hover:text-black rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>Submit</button>
                </form>

                </div>
            </div>
        </div>
    </main>

    <script>
        const inventOptions = @json($invents->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit])->all());
        const menuSelect = document.getElementById('menu');
        const tabsWrap = document.getElementById('varietyTabs');
        const tabsList = document.getElementById('varietyTabsList');
        const panelsWrap = document.getElementById('varietyPanels');
        const emptyHint = document.getElementById('emptyHint');
        const submitBtn = document.getElementById('submitBtn');

        function labelize(slug) {
            return slug.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }

        function buildOptions(selected) {
            return inventOptions
                .map(o => `<option value="${o.id}" ${o.id == selected ? 'selected' : ''}>${o.name} (${o.unit})</option>`)
                .join('');
        }

        function newRow(variety, index) {
            return `
                <div class="ingredient-row flex justify-between gap-2">
                    <select name="ingredients[${variety}][${index}][invent_id]"
                        class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/2" required>
                        <option value="">-- pilih bahan --</option>
                        ${buildOptions(null)}
                    </select>
                    <input type="number" step="0.01" min="0.01" name="ingredients[${variety}][${index}][quantity_used]"
                        placeholder="Quantity"
                        class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-1/3" required>
                    <button type="button"
                        class="remove-row bg-red-500 text-white p-2 px-4 rounded-xl hover:text-black">X</button>
                </div>
            `;
        }

        function renderVarieties(varieties) {
            tabsList.innerHTML = '';
            panelsWrap.innerHTML = '';
            const showTabs = varieties.length > 1;

            varieties.forEach((v, i) => {
                const active = i === 0;
                tabsList.insertAdjacentHTML('beforeend', `
                    <button type="button" data-variety="${v}"
                        class="varietyTab px-4 py-2 font-semibold text-sm border-b-2 ${active ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700'}">
                        ${labelize(v)}
                    </button>
                `);

                panelsWrap.insertAdjacentHTML('beforeend', `
                    <div class="varietyPanel space-y-3 ${active ? '' : 'hidden'}" data-variety="${v}">
                        <div class="ingredient-wrapper space-y-3">
                            ${newRow(v, 0)}
                        </div>
                        <button type="button" class="addRow bg-green-500 text-white px-4 p-2 rounded-lg hover:text-black"
                            data-variety="${v}">+ Bahan</button>
                    </div>
                `);
            });

            tabsWrap.classList.toggle('hidden', !showTabs);
            panelsWrap.classList.remove('hidden');
            emptyHint.classList.add('hidden');
            submitBtn.disabled = false;
        }

        menuSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (!opt.value) {
                tabsWrap.classList.add('hidden');
                panelsWrap.classList.add('hidden');
                emptyHint.classList.remove('hidden');
                submitBtn.disabled = true;
                return;
            }
            const varieties = JSON.parse(opt.dataset.varieties || '["normal"]');
            renderVarieties(varieties);
        });

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