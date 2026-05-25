<!DOCTYPE html>
<html lang="en">

<head>
    <title>History</title>
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
                        <i class="fas fa-tags text-red-500"></i> History
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">View your archived order history</p>
                </div>
            </div>

            @if ($history->isEmpty())
                <!-- Empty State -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                <i class="fas fa-inbox text-4xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No history yet</h3>
                            <p class="text-sm text-gray-500 mt-1">Archived orders will appear here after a shift is
                                closed.</p>
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
                                    <th class="p-4 font-bold" width="15%">
                                        <div class="flex items-center justify-center">Created At</div>
                                    </th>
                                    <th class="p-4 font-bold">Order ID</th>
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold">Chair</th>
                                    <th class="p-4 font-bold">Order</th>
                                    <th class="p-4 font-bold">Payment</th>
                                    <th class="p-4 font-bold">Total</th>
                                    <th class="p-4 font-bold rounded-tr-lg">
                                        <div class="flex items-center justify-center">Status</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                                @php $no = 1; @endphp
                                @foreach ($history as $item)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4 text-center">
                                            <span
                                                class="inline-flex items-center bg-gray-50 text-gray-700 text-xs font-bold px-2 py-1 rounded border border-gray-200">
                                                <i class="far fa-calendar mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                            </span>
                                        </td>

                                        <td class="p-4 font-mono text-xs text-gray-700">
                                            {{ $item->no_order }}
                                        </td>

                                        <td class="p-4">
                                            <div class="font-bold text-gray-900">{{ $item->name }}</div>
                                        </td>

                                        <td class="p-4 text-gray-700">{{ $item->chair }}</td>

                                        <td class="p-4 text-xs text-gray-600">
                                            @php $orders = explode(' - ', $item->order); @endphp
                                            @foreach ($orders as $order)
                                                <div>{{ $order }}</div>
                                            @endforeach
                                        </td>

                                        <td class="p-4">
                                            <span
                                                class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full font-bold border border-gray-200 uppercase">
                                                {{ $item->payment_type }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-mono font-bold text-gray-800">
                                                Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td class="p-4 text-center">
                                            @php
                                                $statusColor = $item->status == 'settlement'
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                    : 'bg-gray-100 text-gray-600 border-gray-200';
                                            @endphp
                                            <span
                                                class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                                {{ $item->status }}
                                            </span>
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
    <script>
        $(document).ready(function() {
            if ($('#myTable').length) {
                new DataTable('#myTable', {});
            }
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>
