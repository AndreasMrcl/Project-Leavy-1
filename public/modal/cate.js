$(document).ready(function () {
    // ========== DataTable Initialization ==========
    $('#myTable').each(function () {
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
        $('#editDesc').val(btn.data('desc'));
        $('#editForm').attr('action', `/category/${btn.data('id')}/update`);
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

    // Close modals on backdrop click
    $(window).click(e => {
        if (e.target.id === 'addModal') $('#addModal').addClass('hidden');
        if (e.target.id === 'editModal') $('#editModal').addClass('hidden');
    });

    // Close on Escape
    $(document).on('keydown', e => {
        if (e.key === 'Escape') {
            $('#addModal, #editModal').addClass('hidden');
        }
    });

    // Character count
    $('#addDesc, #editDesc').on('input', function () {
        const countEl = $(this).attr('id') === 'addDesc' ? '#addDescCount' : '#editDescCount';
        $(countEl).text($(this).val().length);
    });

});