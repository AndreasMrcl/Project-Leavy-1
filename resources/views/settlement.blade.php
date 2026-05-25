<!DOCTYPE html>
<html lang="en">

<head>
    <title>Settlement</title>
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
                        <i class="fas fa-tags text-red-500"></i> Settlement Management
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your cashier shift settlements</p>
                </div>
                <div class="flex gap-2">
                    <button id="startBtn"
                        class="px-6 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                        <i class="fas fa-play"></i> Start
                    </button>
                    <button id="endBtn"
                        class="px-6 py-3 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                        <i class="fas fa-stop"></i> End
                    </button>
                </div>
            </div>

            @if ($settlements->isEmpty())
                <!-- Empty State -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4 border border-red-100">
                                <i class="fas fa-inbox text-4xl text-red-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No settlements yet</h3>
                            <p class="text-sm text-gray-500 mt-1 mb-6">Open your first shift to start tracking the cash
                                drawer.</p>
                            <button id="emptyStartBtn"
                                class="px-6 py-2.5 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                                <i class="fas fa-play"></i> Start Shift
                            </button>
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
                                    <th class="p-4 font-bold" width="12%">
                                        <div class="flex items-center justify-center">Created At</div>
                                    </th>
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold">Start</th>
                                    <th class="p-4 font-bold">End</th>
                                    <th class="p-4 font-bold">Start Amount</th>
                                    <th class="p-4 font-bold">End Amount</th>
                                    <th class="p-4 font-bold">Expected</th>
                                    <th class="p-4 font-bold rounded-tr-lg" width="15%">
                                        <div class="flex items-center justify-center">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                                @php $no = 1; @endphp
                                @foreach ($settlements as $item)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4 text-center">
                                            <span
                                                class="inline-flex items-center bg-gray-50 text-gray-700 text-xs font-bold px-2 py-1 rounded border border-gray-200">
                                                <i class="far fa-calendar mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <div class="font-bold text-gray-900">{{ $item->user->name }}</div>
                                        </td>

                                        <td class="p-4 text-xs text-gray-700">
                                            {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') : '-' }}
                                        </td>

                                        <td class="p-4 text-xs text-gray-700">
                                            {{ $item->end_time ? \Carbon\Carbon::parse($item->end_time)->format('d M Y H:i') : '-' }}
                                        </td>

                                        <td class="p-4">
                                            <span class="font-mono font-bold text-gray-800">
                                                Rp {{ number_format($item->start_amount, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-mono font-bold text-gray-800">
                                                Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-mono font-bold text-emerald-700">
                                                Rp {{ number_format($item->expected, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <a href="{{ route('showsettlement', ['id' => $item->id]) }}"
                                                    class="w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                    title="View Detail">
                                                    <i class="fas fa-eye text-lg"></i>
                                                </a>
                                                <form method="post"
                                                    action="{{ route('delsettlement', ['id' => $item->id]) }}"
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

    @include('modal.startSettlement')
    @include('modal.endSettlement')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="{{ asset('modal/settlement.js') }}"></script>

    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>
