$(document).ready(function () {
    // ========== DataTable Initialization ==========
    $('.categoryTable').each(function () {
        new DataTable($(this), {});
    });

    // Open add modal
    $('#addBtn, #emptyAddBtn').click(() => $('#addModal').removeClass('hidden'));
    $('#closeAddModal').click(() => $('#addModal').addClass('hidden'));
    $('#closeModal').click(() => $('#editModal').addClass('hidden'));

    // Open edit modal
    $(document).on('click', '.editBtn', function () {
        const btn = $(this);
        $('#editName').val(btn.data('name'));
        $('#editPriceInput').val(formatRupiah(btn.data('price')));
        $('#editCategory').val(btn.data('category_id'));
        $('#editDesc').val(btn.data('desc'));
        $('#editForm').attr('action', `/product/${btn.data('id')}/update`);
        $('#editModal').removeClass('hidden');
    });

    // Delete confirmation
    $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(result => result.isConfirmed && form.submit());
    });

        // ========== Rupiah Formatting ==========
    function formatRupiah(value) {
        const number = parseInt(value.replace(/[^0-9]/g, '')) || 0;
        return number.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
    }

    function parseRupiah(value) {
        return value.replace(/[^0-9]/g, '');
    }

    // Add modal price input
    $('#addPrice').on('input', function () {
        const cursorPos = this.selectionStart;
        const oldValue = $(this).val();
        const numericValue = parseRupiah(oldValue);
        $(this).val(formatRupiah(numericValue));
        // Adjust cursor position
        const newPos = cursorPos + ($(this).val().length - oldValue.length);
        this.setSelectionRange(newPos, newPos);
    });

    // Edit modal price input
    $('#editPriceInput').on('input', function () {
        const cursorPos = this.selectionStart;
        const oldValue = $(this).val();
        const numericValue = parseRupiah(oldValue);
        $(this).val(formatRupiah(numericValue));
        const newPos = cursorPos + ($(this).val().length - oldValue.length);
        this.setSelectionRange(newPos, newPos);
    });

    // ========== Form Submit - Clean Rupiah Format ==========
    $('#addForm').on('submit', function () {
        $('#addPrice').val(parseRupiah($('#addPrice').val()));
    });

    $('#editForm').on('submit', function () {
        $('#editPriceInput').val(parseRupiah($('#editPriceInput').val()));
    });

});