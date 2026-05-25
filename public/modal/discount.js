$(document).ready(function () {
    // ========== DataTable Initialization ==========
    new DataTable('#myTable', {});

    const addModal = $('#addModal');
    const editModal = $('#editModal');

    // ========== Modal open/close ==========
    $('#addBtn, #emptyAddBtn').click(() => addModal.removeClass('hidden'));
    $('#closeAddModal').click(() => addModal.addClass('hidden'));
    $('#closeModal').click(() => editModal.addClass('hidden'));

    $(document).on('click', '.editBtn', function () {
        const btn = $(this);
        $('#editName').val(btn.data('name'));
        $('#editPercentage').val(btn.data('percentage'));
        $('#editForm').attr('action', `/discount/${btn.data('id')}/update`);
        editModal.removeClass('hidden');
    });

    // Close on backdrop click
    $(window).click(e => {
        if (e.target.id === 'addModal') addModal.addClass('hidden');
        if (e.target.id === 'editModal') editModal.addClass('hidden');
    });

    // Close on Escape
    $(document).on('keydown', e => {
        if (e.key === 'Escape') $('#addModal, #editModal').addClass('hidden');
    });

    // ========== Delete confirmation ==========
    $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Delete?',
            text: 'Data will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(result => result.isConfirmed && form.submit());
    });
});
