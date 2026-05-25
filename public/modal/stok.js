$(document).ready(function () {
        // ========== DataTable Initialization ==========

    new DataTable('#myTable', {});

    const receiveModal = $('#receiveModal');

    $(document).on('click', '.receiveBtn', function () {
        const btn = $(this);
        $('#receiveInventId').val(btn.data('id'));
        $('#receiveBahanName').text(btn.data('name'));
        $('#receiveBahanStock').text(btn.data('stock'));
        $('#receiveBahanUnit').text(btn.data('unit'));
        receiveModal.removeClass('hidden');
    });

    $('#closeReceiveModal').click(() => receiveModal.addClass('hidden'));

    $(window).click((e) => {
        if (e.target === receiveModal[0]) receiveModal.addClass('hidden');
    });

        // Delete confirmation
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
