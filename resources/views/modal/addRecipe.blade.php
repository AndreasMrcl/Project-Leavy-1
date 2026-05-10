    <!-- ADD INGREDIENT MODAL -->
    <div id="ingredientModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm">
        <div class="min-h-screen flex items-center justify-center px-4 py-6">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
                <button id="closeIngredientModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <h2 class="text-xl font-bold mb-1 text-gray-800 flex items-center gap-2">
                    <i class="fas fa-plus text-red-500"></i> Tambah Bahan
                </h2>
                <p class="text-xs text-gray-500 mb-4" id="modalVarietyHint"></p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan</label>
                        <select id="modalBahanSelect"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500">
                            <option value="" disabled selected>-- pilih bahan --</option>
                            @foreach ($invents as $invent)
                                <option value="{{ $invent->id }}" data-unit="{{ $invent->unit }}"
                                    data-name="{{ $invent->name }}">
                                    {{ $invent->name }} ({{ $invent->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah</label>
                        <div class="relative">
                            <input type="number" id="modalQty" min="0.01" step="0.01" placeholder="0"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 pr-14 border focus:ring-2 focus:ring-red-500">
                            <span id="modalUnit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 uppercase pointer-events-none"></span>
                        </div>
                    </div>

                    <button type="button" id="modalAddBtn"
                        class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-red-600 transition flex justify-center items-center gap-2">
                        <i class="fas fa-check"></i> Tambah ke Resep
                    </button>
                </div>
            </div>
        </div>
    </div>
