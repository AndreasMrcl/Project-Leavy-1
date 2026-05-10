$(document).ready(function () {
    // ========== DataTable Initialization ==========
    new DataTable('#myTable', {});

    // ========== Modal open/close ==========
    const addModal = $('#addModal');

    $('#addBtn').click(() => addModal.removeClass('hidden'));
    $('#closeAddModal').click(() => addModal.addClass('hidden'));

    // Close on backdrop click
    $(window).click(e => {
        if (e.target.id === 'addModal') addModal.addClass('hidden');
    });

    // Close on Escape
    $(document).on('keydown', e => {
        if (e.key === 'Escape') addModal.addClass('hidden');
    });

    // ========== Delete confirmation ==========
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
});
