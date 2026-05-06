<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search Result</title>
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
            <div class='w-full bg-white rounded-xl h-fit mx-auto'>
                <div class="p-3 text-center">
                    <h1 class="font-extrabold text-3xl">Search Result</h1>
                </div>
                <div class="p-6">
                    <div class="space-y-8">
                        <div class="space-y-2">
                            <div>
                                <h1 class="font-extrabold text-3xl">Order</h1>
                            </div>
                            <div>
                                <div class="p-2">
                                    <div class="overflow-auto">
                                        <table id="Tableorder" class="bg-gray-50 border-2">
                                            <thead class="w-full">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Order Id</th>
                                                <th>Nama</th>
                                                <th>Kursi</th>
                                                <th>Order</th>
                                                <th>Payment</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($orderResults as $order)
                                                <tr class="border-2">
                                                    <td>{{ $order->id }}</td>
                                                    <td>{{ $order->created_at }}</td>
                                                    <td>{{ $order->no_order }}</td>
                                                    <td>{{ $order->atas_nama }}</td>
                                                    <td>{{ $order->cart->user->name }}</td>
                                                    <td>
                                                        @foreach ($order->cart->cartMenus as $cartMenu)
                                                        {{ $cartMenu->menu->name }} - {{ $cartMenu->quantity }} -
                                                        {{ $cartMenu->notes }} <br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $order->payment_type }}</td>
                                                    <td>{{ $order->cart->total_amount }}</td>
                                                    <td>
                                                        <h1 class="p-2 w-full text-white rounded-xl text-center 
                                        @if (isset($statuses[$order->no_order]) && property_exists($statuses[$order->no_order], 'transaction_status')) {{ $statuses[$order->no_order]->transaction_status == 'settlement' ? 'bg-green-500' : 'bg-red-500' }}
                                        @else
                                            bg-gray-500 @endif">
                                                            @if (isset($statuses[$order->no_order]) && property_exists($statuses[$order->no_order], 'transaction_status'))
                                                            {{ $statuses[$order->no_order]->transaction_status }}
                                                            @else
                                                            Status Not Available
                                                            @endif
                                                        </h1>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('archive', ['orderId' => $order->id]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="p-2 w-full text-white hover:text-black bg-blue-500 rounded-xl text-center">
                                                                Done
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <h1 class="font-extrabold text-3xl">Cashier</h1>
                            </div>
                            <div>
                                <div class="p-2">
                                    <div class="overflow-auto">
                                        <table id="Tableemployee" class="bg-gray-50 border-2">
                                            <thead class="w-full">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Nama</th>
                                                <th>Level</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                $no = 1;
                                                @endphp
                                                @foreach ($employeeResults as $user)
                                                <tr class="border-2">
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $user->created_at }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->level }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td class="flex gap-2">
                                                        <a href="{{ route('login-qr', ['id' => $user->id]) }}">
                                                            <h1 class="p-2 w-full text-white hover:text-black bg-blue-500 rounded-xl text-center">Barcode</h1>
                                                        </a>
                                                        <form class="p-1 w-full text-white hover:text-black bg-red-500 rounded-xl text-center" method="post" action="{{ route('rmusers', ['id' => $user->id]) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <h1 class="font-extrabold text-3xl">Chair</h1>
                            </div>
                            <div>
                                <div class="p-2">
                                    <div class="overflow-auto">
                                        <table id="Tablechair" class="bg-gray-50 border-2">
                                            <thead class="w-full">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Kursi</th>
                                                <th>Level</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                $no = 1;
                                                @endphp
                                                @foreach ($chairResults as $user)
                                                <tr class="border-2">
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $user->created_at }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->level }}</td>
                                                    <td class="flex gap-2">
                                                        <a href="{{ route('login-qr', ['id' => $user->id]) }}">
                                                            <h1 class="p-2 w-full text-white hover:text-black bg-blue-500 rounded-xl text-center">Barcode</h1>
                                                        </a>
                                                        <form class="p-1 w-full text-white hover:text-black bg-red-500 rounded-xl text-center" method="post" action="{{ route('deluser', ['id' => $user->id]) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <h1 class="font-extrabold text-3xl">Discount</h1>
                            </div>
                            <div>
                                <div class="p-2">
                                    <div class="overflow-auto">
                                        <table id="Tablediscount" class="bg-gray-50 border-2">
                                            <thead class="w-full">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Percentage</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($discountResults as $discount)
                                                        <tr class="border-2">
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $discount->created_at }}</td>
                                                            <td>{{ $discount->name }}</td>
                                                            <td>{{ $discount->percentage }} %</td>
                                                            <td class="">
                                                                <form class="p-1 w-full text-white hover:text-black bg-red-500 rounded-xl text-center"
                                                                method="post" action="{{ route('deldiscount', ['id' => $discount->id]) }}">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit">Delete</button>
                                                            </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <h1 class="font-extrabold text-3xl">History</h1>
                            </div>
                            <div>
                                <div class="p-2">
                                    <div class="overflow-auto">
                                        <table id="Tablehistory" class="bg-gray-50 border-2">
                                            <thead class="w-full">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Order Id</th>
                                                <th>Nama</th>
                                                <th>Chair</th>
                                                <th>Order</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($historyResults as $history)
                                                <tr class="border-2">
                                                    <td>{{ $history->id }}</td>
                                                    <td>{{ $history->created_at }}</td>
                                                    <td>{{ $history->no_order }}</td>
                                                    <td>{{ $history->name }}</td>
                                                    <td>{{ $history->akun }}</td>
                                                    <td>
                                                        @php $orders = explode(' - ', $history->order);
                                                        @endphp
                                                        @foreach ($orders as $order) {{ $order }}
                                                        <br />
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $history->total_amount }}</td>
                                                    <td>
                                                        <h1 class="p-2 w-full text-white rounded-xl text-center @if($history->status == 'settlement') bg-green-500 @else @endif">
                                                            {{ $history->status }}
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
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#Tableorder', {
                columnDefs: [{
                    targets: 1, // Index of the 'Date' column
                    render: function(data, type, row) {
                        // Assuming the date is in 'YYYY-MM-DD HH:MM:SS' format
                        var date = new Date(data);
                        return date.toLocaleDateString(); // Format the date as needed
                    },
                }, ],
            });
        });
        $(document).ready(function() {
            let table = new DataTable('#Tableemployee', {
                columnDefs: [{
                    targets: 1, // Index of the 'Date' column
                    render: function(data, type, row) {
                        // Assuming the date is in 'YYYY-MM-DD HH:MM:SS' format
                        var date = new Date(data);
                        return date.toLocaleDateString(); // Format the date as needed
                    },
                }, ],
            });
        });
        $(document).ready(function() {
            let table = new DataTable('#Tablechair', {
                columnDefs: [{
                    targets: 1, // Index of the 'Date' column
                    render: function(data, type, row) {
                        // Assuming the date is in 'YYYY-MM-DD HH:MM:SS' format
                        var date = new Date(data);
                        return date.toLocaleDateString(); // Format the date as needed
                    },
                }, ],
            });
        });
        $(document).ready(function() {
            let table = new DataTable('#Tablediscount', {
                columnDefs: [{
                    targets: 1, // Index of the 'Date' column
                    render: function(data, type, row) {
                        // Assuming the date is in 'YYYY-MM-DD HH:MM:SS' format
                        var date = new Date(data);
                        return date.toLocaleDateString(); // Format the date as needed
                    },
                }, ],
            });
        });
        $(document).ready(function() {
            let table = new DataTable('#Tablehistory', {
                columnDefs: [{
                    targets: 1, // Index of the 'Date' column
                    render: function(data, type, row) {
                        // Assuming the date is in 'YYYY-MM-DD HH:MM:SS' format
                        var date = new Date(data);
                        return date.toLocaleDateString(); // Format the date as needed
                    },
                }, ],
            });
        });
    </script>
</body>
</html>