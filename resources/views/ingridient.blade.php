<!DOCTYPE html>
<html lang="en">

<head>
    <title>Resep Bahan</title>
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
                        <i class="fas fa-tags text-red-500"></i></i>Ingridient Management
                    </h1>
                    <p class="text-sm text-gray-500">Manage system ingridients</p>
                </div>
                <a href="{{ route('addingridient') }}"
                    class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add
                </a>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                <th class="p-4 font-bold text-center" width="15%">Created at</th>
                                <th class="p-4 font-bold">Product</th>
                                <th class="p-4 font-bold">Bahan</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($menus as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                    <td class="p-4 font-medium text-center">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">{{ $item->name }}</span>
                                        @if ($item->has_variety)
                                            <span class="ml-1 inline-block text-[10px] font-semibold uppercase text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">variety</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-xs">
                                        @php
                                            $grouped = $item->invents->groupBy(fn ($i) => $i->pivot->variety ?? 'normal');
                                        @endphp
                                        @if ($grouped->isEmpty())
                                            <span class="text-gray-400 italic">Belum ada bahan</span>
                                        @else
                                            @foreach ($grouped as $variety => $invents)
                                                <div class="mb-1.5">
                                                    @if ($item->has_variety)
                                                        <div class="font-semibold text-purple-600 mb-0.5">{{ ucwords(str_replace('_', ' ', $variety)) }}</div>
                                                    @endif
                                                    @foreach ($invents as $invent)
                                                        <div class="pl-2">
                                                            <span class="text-gray-500">•</span>
                                                            <span class="font-semibold">{{ $invent->name }}</span>
                                                            <span class="text-gray-500">
                                                                ({{ rtrim(rtrim(number_format($invent->pivot->quantity_used, 2, '.', ''), '0'), '.') }} {{ $invent->unit }})
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>

                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('editingridient', ['id' => $item->id]) }}"
                                                class="w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post"
                                                action="{{ route('delingridient', ['id' => $item->id]) }}"
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

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            new DataTable('#myTable', {
                columnDefs: [{
                    targets: 1,
                    render: function (data) {
                        return new Date(data).toLocaleDateString();
                    },
                }],
            });

            $(document).on('click', '.delete-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus resep ini?',
                    text: 'Komposisi bahan untuk produk ini akan dihapus.',
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
</body>

</html>
