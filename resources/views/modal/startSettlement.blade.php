<!-- START MODAL -->
<div id="startModal"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
        <button id="closeStartModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-times text-xl"></i>
        </button>
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-tags text-red-500"></i> Start Settlement
        </h2>

        <form id="startForm" method="post" action="{{ route('poststart') }}" class="space-y-5">
            @csrf @method('post')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starting Cash (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="start_amount" min="0" step="1"
                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500"
                    placeholder="0"
                    required>
            </div>

            <button type="submit"
                class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-red-600 transition flex justify-center items-center gap-2">
                <i class="fas fa-check"></i> Start Settlement
            </button>
        </form>
    </div>
</div>
