    <!-- RECEIVE MODAL -->
    <div id="receiveModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeReceiveModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-2 text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck-loading text-green-500"></i> Terima Bahan
            </h2>
            <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded mb-4">
                <p class="text-sm text-green-800">
                    Bahan: <strong id="receiveBahanName"></strong>
                </p>
                <p class="text-xs text-green-700 mt-1">
                    Stok saat ini: <span id="receiveBahanStock"></span>
                    <span id="receiveBahanUnit"></span>
                </p>
            </div>

            <form method="post" action="{{ route('receiveinvent') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="invent_id" id="receiveInventId">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Diterima</label>
                    <input type="number" name="quantity" min="1"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan
                        <span class="text-gray-400 text-xs">(opsional, mis: nama supplier)</span>
                    </label>
                    <input type="text" name="notes" maxlength="255"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-500 text-white font-bold rounded-lg shadow-md hover:bg-green-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Catat Penerimaan
                </button>
            </form>
        </div>
    </div>
