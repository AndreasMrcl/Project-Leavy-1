<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stock Ingridient</title>
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
                    <p class="text-sm text-gray-500 mt-1">Manage your ingredient stock levels</p>
                </div>
                <a href="{{ route('opname') }}"
                    class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fas fa-clipboard-check"></i> Stock Opname
                </a>
            </div>

            @php $lowStockCount = $invents->filter(fn($i) => $i->isLowStock())->count(); @endphp
            @if ($lowStockCount > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg flex items-center gap-3 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    <span class="text-yellow-800 font-bold">
                        {{ $lowStockCount }} ingredient(s) are low on stock. Please restock soon!
                    </span>
                </div>
            @endif

            @if ($invents->isEmpty())
                <!-- Empty State -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4 border border-red-100">
                                <i class="fas fa-inbox text-4xl text-red-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No stocks yet</h3>
                            <p class="text-sm text-gray-500 mt-1 mb-6">Add an ingredient first via <strong>Master
                                    Ingredient</strong> to track its stock here.</p>
                            <a href="{{ route('invent') }}"
                                class="px-6 py-2.5 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                                <i class="fas fa-plus"></i> Add Ingredient
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Table Section -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-5 overflow-auto">
                        <table id="myTable" class="w-full text-left">
                            <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                                <tr>
                                    <th class="p-4 font-bold rounded-tl-lg" width="5%">
                                        <div class="flex items-center justify-center">No</div>
                                    </th>
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold">Stock</th>
                                    <th class="p-4 font-bold">Min Stock</th>
                                    <th class="p-4 font-bold">Unit</th>
                                    <th class="p-4 font-bold rounded-tr-lg" width="15%">
                                        <div class="flex items-center justify-center">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                                @php $no = 1; @endphp
                                @foreach ($invents as $item)
                                    <tr
                                        class="hover:bg-gray-50 transition duration-150 {{ $item->isLowStock() ? 'bg-yellow-50' : '' }}">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4">
                                            <div class="font-bold text-gray-900 text-base">{{ $item->name }}</div>
                                        </td>

                                        <td class="p-4">
                                            <span
                                                class="font-mono font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                                                {{ $item->stock }}
                                                @if ($item->isLowStock())
                                                    <i class="fas fa-exclamation-triangle text-yellow-500 ml-1"
                                                        title="Low stock"></i>
                                                @endif
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-mono text-gray-600">
                                                {{ $item->min_stock > 0 ? $item->min_stock : '-' }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span
                                                class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full font-bold border border-gray-200 uppercase">
                                                {{ $item->unit }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <button
                                                    class="receiveBtn w-10 h-10 flex items-center justify-center bg-emerald-500 text-white rounded-lg shadow hover:bg-emerald-600 hover:scale-105 transition"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}"
                                                    title="Receive Stock">
                                                    <i class="fas fa-truck-loading text-lg"></i>
                                                </button>

                                                <form method="post"
                                                    action="{{ route('delinvent', ['id' => $item->id]) }}"
                                                    class="inline deleteForm">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button"
                                                        class="delete-confirm w-10 h-10 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                        title="Delete">
                                                        <i class="fas fa-trash text-lg"></i>
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
            @endif
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="{{ asset('modal/stok.js') }}"></script>

    <!-- Modals -->
    @include('modal.recStok')

    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>
