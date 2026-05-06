<!DOCTYPE html>
<html lang="en">

<head>
    <title>Settlement</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Override DataTables Style */
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i>Settlement Management
                    </h1>
                    <p class="text-sm text-gray-500">Organize system settlements</p>
                </div>
                <div class="flex gap-2">

                    <button id="startBtn"
                        class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Start
                    </button>
                    <button id="endBtn"
                        class="px-10 py-3 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-stop"></i> End
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                <th class="p-4 font-bold text-center" width="20%">Created at</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Start</th>
                                <th class="p-4 font-bold">End</th>
                                <th class="p-4 font-bold">Start Amount</th>
                                <th class="p-4 font-bold">End Amount</th>
                                <th class="p-4 font-bold">Expected</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($settlements as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                    <td class="p-4 font-medium text-center">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->user->name }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') : '-' }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->end_time ? \Carbon\Carbon::parse($item->end_time)->format('d M Y H:i') : '-' }}
                                        </span>
                                    </td>


                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            Rp. {{ number_format($item->start_amount, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            Rp. {{ number_format($item->total_amount, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            Rp. {{ number_format($item->expected, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">

                                            <a href="{{ route('showsettlement', ['id' => $item->id]) }}"
                                                class="w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form method="post" action="{{ route('delsettlement', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- START MODAL -->
    <div id="startModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeStartModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-play text-red-500"></i> Buka Shift
            </h2>

            <form id="startForm" method="post" action="{{ route('poststart') }}" class="space-y-5">
                @csrf @method('post')

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Modal Awal (Rp)</label>
                        <input type="number" name="start_amount" min="0" step="1"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500"
                            placeholder="0">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-red-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Buka Shift
                </button>
            </form>
        </div>
    </div>

    <!-- END MODAL -->
    <div id="endModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeEndModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-stop text-yellow-500"></i> Tutup Shift
            </h2>

            <form id="endForm" method="post" action="{{ route('posttotal') }}" class="space-y-5">
                @csrf @method('post')

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Uang di Laci (Rp)</label>
                        <input type="number" name="total_amount" min="0" step="1"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                            placeholder="0">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-yellow-500 text-white font-bold rounded-lg shadow-md hover:bg-yellow-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Tutup Shift
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            // Init DataTable
            new DataTable('#myTable', {});

            // Modal Logic
            const startModal = $('#startModal');
            const endModal = $('#endModal');

            $('#startBtn').click(() => startModal.removeClass('hidden'));
            $('#closeStartModal').click(() => startModal.addClass('hidden'));

            $('#endBtn').click(() => endModal.removeClass('hidden'));
            $('#closeEndModal').click(() => endModal.addClass('hidden'));

            $(window).click((e) => {
                if (e.target === startModal[0]) startModal.addClass('hidden');
                if (e.target === endModal[0]) endModal.addClass('hidden');
            });

            // Delete confirmation
            $(document).on('click', '.delete-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus?',
                    text: "Data akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });

            });
        });
    </script>

    @include('sweetalert::alert')

    @include('layout.loading')

</body>

</html>