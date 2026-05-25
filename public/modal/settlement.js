$(document).ready(function () {
    // ========== DataTable Initialization ==========
    new DataTable('#myTable', {});

    const startModal = $('#startModal');
    const endModal = $('#endModal');

    // ========== Modal open/close ==========
    $('#startBtn, #emptyStartBtn').click(() => startModal.removeClass('hidden'));
    $('#closeStartModal').click(() => startModal.addClass('hidden'));

    $('#endBtn').click(() => endModal.removeClass('hidden'));
    $('#closeEndModal').click(() => endModal.addClass('hidden'));

    // Close on backdrop click
    $(window).click(e => {
        if (e.target.id === 'startModal') startModal.addClass('hidden');
        if (e.target.id === 'endModal') endModal.addClass('hidden');
    });

    // Close on Escape
    $(document).on('keydown', e => {
        if (e.key === 'Escape') $('#startModal, #endModal').addClass('hidden');
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
