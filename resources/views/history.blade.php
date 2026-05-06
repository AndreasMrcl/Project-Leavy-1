<!DOCTYPE html>
<html lang="en">

<head>
    <title>History</title>
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
                        <i class="fas fa-tags text-red-500"></i>History
                    </h1>
                    <p class="text-sm text-gray-500">Organize system history</p>
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
                                <th class="p-4 font-bold">Order Id</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Chair</th>
                                <th class="p-4 font-bold">Order</th>
                                <th class="p-4 font-bold">Payment</th>
                                <th class="p-4 font-bold">Total</th>
                                <th class="p-4 font-bold">Status</th>

                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($history as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                    <td class="p-4 font-medium text-center">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->no_order }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->name }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->chair }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                             @php
                                                $orders = explode(' - ', $item->order);
                                            @endphp
                                            @foreach ($orders as $order)
                                                {{ $order }}
                                                <br />
                                            @endforeach
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->payment_type }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                           Rp. {{ number_format($item->total_amount, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800 @if ($item->status == 'settlement') bg-green-500 @else @endif">
                                            {{ $item->status }}
                                        </span>
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
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});
        });
    </script>

    @include('sweetalert::alert')

    @include('layout.loading')

</body>

</html>
