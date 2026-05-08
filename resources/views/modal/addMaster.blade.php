    <!-- ADD MODAL -->
<div id="addModal" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
        <button id="closeAddModal" aria-label="Close dialog"
            class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition focus:outline-none focus:ring-2 focus:ring-red-500 rounded">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 id="addModalTitle" class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-tags text-red-500"></i> Add Master
        </h2>

        <form id="addForm" method="post" action="{{ route('postinvent') }}" enctype="multipart/form-data"
            class="space-y-5" novalidate>
            @csrf
            @method('post')

            <div>
                <label for="addName" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Bahan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="addName" name="name" placeholder="Mis: Susu UHT 1L"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                    required minlength="1" maxlength="255" aria-required="true">
                <div class="error-message hidden" id="addNameError"></div>
            </div>

            <div>
                <label for="addUnit" class="block text-sm font-semibold text-gray-700 mb-2">
                    Unit <span class="text-red-500">*</span>
                </label>
                <select id="addUnit" name="unit"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                    required aria-required="true">
                    <option value="" disabled selected>Pilih Unit</option>
                    <option value="pcs">Pcs</option>
                    <option value="kg">Kg</option>
                    <option value="g">Gram</option>
                    <option value="mg">Miligram</option>
                    <option value="liter">Liter</option>
                    <option value="ml">Mililiter</option>
                </select>
                <div class="error-message hidden" id="addUnitError"></div>
            </div>

            <div>
                <label for="addMinStock" class="block text-sm font-semibold text-gray-700 mb-2">
                    Min Stock <span class="text-gray-400 text-xs">(opsional, alert kalau stok di bawah ini)</span>
                </label>
                <input type="number" id="addMinStock" name="min_stock" min="0" value="0"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <div class="error-message hidden" id="addMinStockError"></div>
            </div>

            <div>
                <label for="addInitialStock" class="block text-sm font-semibold text-gray-700 mb-2">
                    Initial Stock <span class="text-gray-400 text-xs">(opsional, kalau diisi akan tercatat sebagai
                        penerimaan pertama)</span>
                </label>
                <input type="number" id="addInitialStock" name="initial_stock" min="0" value="0"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                <div class="error-message hidden" id="addInitialStockError"></div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-red-600 active:scale-95 transition flex justify-center items-center gap-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <i class="fas fa-check"></i> Save Master
            </button>
        </form>
    </div>
</div>
