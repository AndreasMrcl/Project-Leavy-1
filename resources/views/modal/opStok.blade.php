    <!-- OPNAME MODAL -->
    <div id="opnameModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeOpnameModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-2 text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-blue-500"></i> Stock Opname
            </h2>
            <p class="text-xs text-gray-500 mb-4">Sesuaikan stok di sistem dengan hasil hitung fisik. Selisih akan
                otomatis tercatat di stock movements.</p>

            <form method="post" action="{{ route('opnameinvent') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan</label>
                    <select name="invent_id" id="opnameInventSelect"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled selected>Pilih Bahan</option>
                        @foreach ($invents as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}">
                                {{ $item->name }} (sistem: {{ $item->stock }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Aktual (hasil hitung
                        fisik)</label>
                    <input type="number" name="actual_stock" id="opnameActualStock" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1" id="opnameDeltaPreview"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Keterangan</label>
                    <input type="text" name="reason" maxlength="255"
                        placeholder="Mis: Bahan rusak, hilang, atau koreksi hitung"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white font-bold rounded-lg shadow-md hover:bg-blue-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Sesuaikan Stok
                </button>
            </form>
        </div>
    </div>
