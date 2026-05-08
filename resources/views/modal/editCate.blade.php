<!-- EDIT MODAL -->
<div id="editModal" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
        <button id="closeModal" aria-label="Close dialog" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 id="editModalTitle" class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-blue-600"></i> Edit Category
        </h2>

        <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5" novalidate>
            @csrf
            @method('put')

            <div>
                <label for="editName" class="block text-sm font-semibold text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="editName" 
                    name="name"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter category name"
                    required
                    minlength="3"
                    maxlength="100"
                    aria-required="true">
                <div class="error-message hidden" id="editNameError"></div>
            </div>

            <div>
                <label for="editDesc" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="editDesc" 
                    name="desc"
                    class="form-input w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                    placeholder="Enter category description"
                    rows="4"
                    required
                    minlength="5"
                    maxlength="500"
                    aria-required="true"></textarea>
                <div class="text-gray-500 text-xs mt-1">
                    <span id="editDescCount">0</span>/500 characters
                </div>
                <div class="error-message hidden" id="editDescError"></div>
            </div>

            <button 
                type="submit"
                class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 active:scale-95 transition flex justify-center items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-save"></i> Update Category
            </button>
        </form>
    </div>
</div>
