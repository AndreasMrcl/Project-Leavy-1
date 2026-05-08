    <!-- EDIT MODAL -->
<div id="editModal" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
        <button id="closeModal" aria-label="Close dialog"
            class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 id="editModalTitle" class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-blue-600"></i> Edit Master
        </h2>
        <p class="text-xs text-gray-500 mb-4">Untuk koreksi jumlah stok, gunakan menu <strong>Stok Bahan</strong>
            &rarr; Stock Opname.</p>

        <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5" novalidate>
            @csrf
            @method('put')

            <div>
                <label for="editName" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Bahan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editName" name="name"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required minlength="1" maxlength="255" aria-required="true">
                <div class="error-message hidden" id="editNameError"></div>
            </div>

            <div>
                <label for="editUnit" class="block text-sm font-semibold text-gray-700 mb-2">
                    Unit <span class="text-red-500">*</span>
                </label>
                <select id="editUnit" name="unit"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required aria-required="true">
                    <option value="" disabled>Pilih Unit</option>
                    <option value="pcs">Pcs</option>
                    <option value="kg">Kg</option>
                    <option value="g">Gram</option>
                    <option value="mg">Miligram</option>
                    <option value="liter">Liter</option>
                    <option value="ml">Mililiter</option>
                </select>
                <div class="error-message hidden" id="editUnitError"></div>
            </div>

            <div>
                <label for="editMinStock" class="block text-sm font-semibold text-gray-700 mb-2">
                    Min Stock <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <input type="number" id="editMinStock" name="min_stock" min="0"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <div class="error-message hidden" id="editMinStockError"></div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 active:scale-95 transition flex justify-center items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-save"></i> Update Master
            </button>
        </form>
    </div>
</div>
