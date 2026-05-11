<!-- END MODAL -->
<div id="endModal"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
        <button id="closeEndModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-times text-xl"></i>
        </button>
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-tags text-yellow-500"></i> End Settlement
        </h2>

        <form id="endForm" method="post" action="{{ route('posttotal') }}" class="space-y-5">
            @csrf @method('post')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Total Uang di Laci (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_amount" min="0" step="1"
                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                    placeholder="0"
                    required>
            </div>

            <button type="submit"
                class="w-full py-3 bg-yellow-500 text-white font-bold rounded-lg shadow-md hover:bg-yellow-600 transition flex justify-center items-center gap-2">
                <i class="fas fa-check"></i> End Settlement
            </button>
        </form>
    </div>
</div>
