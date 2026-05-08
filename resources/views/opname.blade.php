<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stock Opname</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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

        .filter-chip.active {
            background-color: #2563eb;
            color: #fff;
            border-color: #2563eb;
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
                        <i class="fas fa-clipboard-check text-blue-500"></i> Stock Opname
                    </h1>
                    <p class="text-sm text-gray-500">Sesuaikan stok di sistem dengan hasil hitung fisik untuk seluruh
                        bahan. Hanya item yang diisi &amp; ada selisih yang akan dicatat di stock movements.</p>
                </div>
                <a href="{{ route('stock') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 transition font-semibold flex items-center gap-2 justify-center">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <form id="opnameForm" method="post" action="{{ route('opnameinvent') }}" class="space-y-6">
                @csrf

                <!-- Reason & Summary Card -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Keterangan Opname</label>
                        <input type="text" name="reason" maxlength="255"
                            placeholder="Mis: Audit bulanan Mei 2026, koreksi awal shift, dll."
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Berlaku untuk semua bahan yang Anda sesuaikan di sesi ini.
                        </p>
                    </div>

                    <!-- Filter Chips -->
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                        <button type="button"
                            class="filter-chip active px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                            data-filter="all">Semua</button>
                        <button type="button"
                            class="filter-chip px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                            data-filter="empty">Belum Diisi</button>
                        <button type="button"
                            class="filter-chip px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                            data-filter="filled">Sudah Diisi</button>
                        <button type="button"
                            class="filter-chip px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                            data-filter="delta">Ada Selisih</button>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-5 overflow-auto">
                        <table id="myTable" class="w-full text-left">
                            <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                                <tr>
                                    <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold text-center">Stok Sistem</th>
                                    <th class="p-4 font-bold text-center" width="20%">Stok Riil</th>
                                    <th class="p-4 font-bold text-center rounded-tr-lg" width="20%">Selisih</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-700 text-sm">
                                @php $no = 1; @endphp
                                @foreach ($invents as $idx => $item)
                                    <tr class="opname-row hover:bg-gray-50 transition duration-150 {{ $item->isLowStock() ? 'bg-yellow-50' : '' }}"
                                        data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4">
                                            <span class="font-semibold text-gray-800">{{ $item->name }}</span>
                                            @if ($item->isLowStock())
                                                <i class="fas fa-exclamation-triangle text-yellow-500 ml-1"
                                                    title="Stok rendah"></i>
                                            @endif
                                        </td>

                                        <td class="p-4 text-center">
                                            <span class="font-semibold text-gray-800">{{ $item->stock }}</span>
                                            <span class="text-gray-500 text-xs">{{ $item->unit }}</span>
                                        </td>

                                        <td class="p-4">
                                            <input type="hidden" name="items[{{ $idx }}][invent_id]"
                                                value="{{ $item->id }}">
                                            <input type="number" name="items[{{ $idx }}][actual_stock]" min="0"
                                                placeholder="kosongkan jika skip"
                                                class="actual-stock w-full rounded-lg border-gray-300 shadow-sm p-2 border focus:ring-2 focus:ring-blue-500 text-center">
                                        </td>

                                        <td class="p-4 text-center">
                                            <span class="delta-preview text-gray-400 text-sm">—</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Bar -->
                <div
                    class="sticky bottom-4 bg-white p-4 rounded-xl shadow-lg border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-3">
                    <p class="text-sm text-gray-600">
                        <span id="actionSummary">Belum ada perubahan.</span>
                    </p>
                    <div class="flex gap-2 w-full md:w-auto">
                        <a href="{{ route('stock') }}"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold flex-1 md:flex-none text-center">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="px-8 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition font-semibold flex items-center gap-2 justify-center flex-1 md:flex-none disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fas fa-check"></i> Simpan Opname
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            const table = new DataTable('#myTable', {
                ordering: false,
                pageLength: 25,
            });

            let currentFilter = 'all';

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'myTable') return true;
                const row = table.row(dataIndex).node();
                const $input = $(row).find('.actual-stock');
                const val = $input.val();
                const sysStock = parseInt($(row).data('stock'));

                if (currentFilter === 'all') return true;
                if (currentFilter === 'empty') return val === '' || val === null;
                if (currentFilter === 'filled') return val !== '' && val !== null;
                if (currentFilter === 'delta') {
                    if (val === '' || val === null) return false;
                    return parseInt(val) !== sysStock;
                }
                return true;
            });

            function recalcRow($row) {
                const sysStock = parseInt($row.data('stock'));
                const unit = $row.data('unit') || '';
                const $input = $row.find('.actual-stock');
                const $preview = $row.find('.delta-preview');
                const raw = $input.val();

                if (raw === '' || raw === null) {
                    $preview.html('<span class="text-gray-400">—</span>');
                    return { filled: false, delta: 0 };
                }

                const actual = parseInt(raw);
                if (isNaN(actual)) {
                    $preview.html('<span class="text-gray-400">—</span>');
                    return { filled: false, delta: 0 };
                }

                const delta = actual - sysStock;
                if (delta === 0) {
                    $preview.html('<span class="text-gray-500">Sama (0 ' + unit + ')</span>');
                } else if (delta > 0) {
                    $preview.html('<span class="font-semibold text-green-600">+' + delta + ' ' + unit + '</span>');
                } else {
                    $preview.html('<span class="font-semibold text-red-600">' + delta + ' ' + unit + '</span>');
                }
                return { filled: true, delta: delta };
            }

            function recalcSummary() {
                let changed = 0;

                $('.opname-row').each(function () {
                    const r = recalcRow($(this));
                    if (r.filled && r.delta !== 0) changed++;
                });

                if (changed === 0) {
                    $('#actionSummary').text('Belum ada perubahan.');
                    $('#submitBtn').prop('disabled', true);
                } else {
                    $('#actionSummary').html('<strong>' + changed + '</strong> bahan akan disesuaikan.');
                    $('#submitBtn').prop('disabled', false);
                }
            }

            $(document).on('input change', '.actual-stock', function () {
                recalcRow($(this).closest('.opname-row'));
                recalcSummary();
                if (currentFilter !== 'all') table.draw();
            });

            $('.filter-chip').on('click', function () {
                $('.filter-chip').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                table.draw();
            });

            $('#opnameForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: 'Simpan opname?',
                    text: 'Selisih akan tercatat permanen di stock movements.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Simpan',
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