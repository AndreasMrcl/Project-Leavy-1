<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stok Bahan</title>
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
                        <i class="fas fa-tags text-red-500"></i> Stock Ingridient
                    </h1>
                    <p class="text-sm text-gray-500">Pantau jumlah stok &amp; lakukan penerimaan / opname.
                        Untuk tambah / edit jenis bahan, buka menu <strong>Master Bahan</strong>.</p>
                </div>
                <button id="opnameBtn"
                    class="px-10 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-clipboard-check"></i> Stock Opname
                </button>
            </div>

            @php $lowStockCount = $invents->filter(fn($i) => $i->isLowStock())->count(); @endphp
            @if ($lowStockCount > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    <span class="text-yellow-800 font-semibold">
                        {{ $lowStockCount }} bahan stoknya rendah, segera lakukan restock!
                    </span>
                </div>
            @endif

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Stock</th>
                                <th class="p-4 font-bold">Min Stock</th>
                                <th class="p-4 font-bold">Unit</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($invents as $item)
                                <tr class="hover:bg-gray-50 transition duration-150 {{ $item->isLowStock() ? 'bg-yellow-50' : '' }}">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">{{ $item->name }}</span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $item->stock }}
                                            @if ($item->isLowStock())
                                                <i class="fas fa-exclamation-triangle text-yellow-500 ml-1"
                                                    title="Stok rendah"></i>
                                            @endif
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="text-gray-600">
                                            {{ $item->min_stock > 0 ? $item->min_stock : '-' }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">{{ $item->unit }}</span>
                                    </td>

                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button
                                                class="receiveBtn w-9 h-9 flex items-center justify-center bg-green-500 text-white rounded-lg shadow hover:bg-green-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}"
                                                title="Terima Bahan">
                                                <i class="fas fa-truck-loading"></i> 
                                            </button>
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

    <!-- RECEIVE MODAL -->
    <div id="receiveModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeReceiveModal"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-2 text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck-loading text-green-500"></i> Terima Bahan
            </h2>
            <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded mb-4">
                <p class="text-sm text-green-800">
                    Bahan: <strong id="receiveBahanName"></strong>
                </p>
                <p class="text-xs text-green-700 mt-1">
                    Stok saat ini: <span id="receiveBahanStock"></span>
                    <span id="receiveBahanUnit"></span>
                </p>
            </div>

            <form method="post" action="{{ route('receiveinvent') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="invent_id" id="receiveInventId">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Diterima</label>
                    <input type="number" name="quantity" min="1"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan
                        <span class="text-gray-400 text-xs">(opsional, mis: nama supplier)</span>
                    </label>
                    <input type="text" name="notes" maxlength="255"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-500 text-white font-bold rounded-lg shadow-md hover:bg-green-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Catat Penerimaan
                </button>
            </form>
        </div>
    </div>

    <!-- OPNAME MODAL -->
    <div id="opnameModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeOpnameModal"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-2 text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-blue-500"></i> Stock Opname
            </h2>
            <p class="text-xs text-gray-500 mb-4">Sesuaikan stok di sistem dengan hasil hitung fisik. Selisih akan
                otomatis tercatat di stock movements.</p>

            <form method="post" action="{{ route('opnameinvent') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan</label>
                    <select name="invent_id" id="opnameInventSelect"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled selected>Pilih Bahan</option>
                        @foreach ($invents as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}"
                                data-unit="{{ $item->unit }}">
                                {{ $item->name }} (sistem: {{ $item->stock }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Aktual (hasil hitung
                        fisik)</label>
                    <input type="number" name="actual_stock" id="opnameActualStock" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1" id="opnameDeltaPreview"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Keterangan</label>
                    <input type="text" name="reason" maxlength="255"
                        placeholder="Mis: Bahan rusak, hilang, atau koreksi hitung"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white font-bold rounded-lg shadow-md hover:bg-blue-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Sesuaikan Stok
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
            new DataTable('#myTable', {});

            const receiveModal = $('#receiveModal');
            const opnameModal = $('#opnameModal');

            $(document).on('click', '.receiveBtn', function () {
                const btn = $(this);
                $('#receiveInventId').val(btn.data('id'));
                $('#receiveBahanName').text(btn.data('name'));
                $('#receiveBahanStock').text(btn.data('stock'));
                $('#receiveBahanUnit').text(btn.data('unit'));
                receiveModal.removeClass('hidden');
            });
            $('#closeReceiveModal').click(() => receiveModal.addClass('hidden'));

            $('#opnameBtn').click(() => opnameModal.removeClass('hidden'));
            $('#closeOpnameModal').click(() => opnameModal.addClass('hidden'));

            $(window).click((e) => {
                if (e.target === receiveModal[0]) receiveModal.addClass('hidden');
                if (e.target === opnameModal[0]) opnameModal.addClass('hidden');
            });

            // Live preview delta in opname form
            function updateOpnameDelta() {
                const opt = $('#opnameInventSelect').find(':selected');
                const sysStock = parseInt(opt.data('stock'));
                const unit = opt.data('unit') || '';
                const actual = parseInt($('#opnameActualStock').val());

                if (isNaN(sysStock) || isNaN(actual)) {
                    $('#opnameDeltaPreview').text('');
                    return;
                }

                const delta = actual - sysStock;
                if (delta === 0) {
                    $('#opnameDeltaPreview').html('<span class="text-gray-500">Tidak ada perubahan stok.</span>');
                } else if (delta > 0) {
                    $('#opnameDeltaPreview').html(`<span class="text-green-600">Penyesuaian naik: +${delta} ${unit}</span>`);
                } else {
                    $('#opnameDeltaPreview').html(`<span class="text-red-600">Penyesuaian turun: ${delta} ${unit}</span>`);
                }
            }

            $('#opnameInventSelect, #opnameActualStock').on('change input', updateOpnameDelta);
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>

</html>
