<!DOCTYPE html>
<html lang="en">

<head>
    <title>Detail Settlement</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->
    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        <!-- Navbar -->
        @include('layout.navbar')
        <!-- end Navbar -->
        <div class="p-5">
            <div class='w-full rounded-xl bg-white h-fit mx-auto'>
                <div class="p-3 space-y-4">
                    <div class="">
                        <h1 class="font-extrabold text-3xl">Detail Settlement</h1>
                    </div>
                    <div class="md:flex justify-between">
                        <h1 class="font-light text-base md:text-xl">Time:</h1>
                        <h1 class="font-bold text-base md:text-xl">{{ $settlement->start_time }} -
                            {{ $settlement->end_time }}</h1>
                    </div>
                    <div class="md:flex justify-between">
                        <h1 class="font-light text-base md:text-xl">Start Amount:</h1>
                        <h1 class="font-bold text-base md:text-xl">Rp.
                            {{ number_format($settlement->start_amount, 0, ',', '.') }}</h1>
                    </div>
                    <div class="md:flex justify-between">
                        <h1 class="font-light text-base md:text-xl">Total Amount:</h1>
                        <h1 class="font-bold text-base md:text-xl">Rp.
                            {{ number_format($settlement->total_amount, 0, ',', '.') }}</h1>
                    </div>
                    <div class="md:flex justify-between">
                        <h1 class="font-light text-base md:text-xl">Expected:</h1>
                        <h1 class="font-bold text-base md:text-xl">Rp.
                            {{ number_format($settlement->expected, 0, ',', '.') }}</h1>
                    </div>
                </div>
                <div class="p-2">
                    <div class="overflow-auto">
                        <table id="myTable" class="bg-gray-50 border-2">
                            <thead class="w-full">
                                <th>No</th>
                                <th>Name</th>
                                <th>Order Id</th>
                                <th>Order</th>
                                <th>Payment</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($settlement->histories as $item)
                                    <tr class="border-2">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->no_order }}</td>
                                        <td>@php
                                            $orders = explode(' - ', $item->order);
                                        @endphp
                                            @foreach ($orders as $order)
                                                {{ $order }}
                                                <br />
                                            @endforeach
                                        </td>
                                        <td>{{ $item->payment_type }}</td>
                                        <td>Rp. {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <h1
                                                class="p-2 w-full text-white rounded-xl text-center @if ($item->status == 'settlement') bg-green-500 @else @endif">
                                                {{ $item->status }}
                                            </h1>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#myTable', {

            });
        });
    </script>
    @include('sweetalert::alert')
</body>

</html>
