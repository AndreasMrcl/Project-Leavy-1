<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hasil Pencarian</title>
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

            <!-- Header -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-magnifying-glass text-slate-600"></i> Result for "{{ request('search') }}"
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Results based on your search keywords across the entire system
                    </p>
                </div>
            </div>

            <!-- PRODUCT / MENU -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Product
                    </h1>
                    <p class="text-sm text-gray-500">Manage products</p>
                </div>
                <a href="{{ route('product') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="menuTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Product Name</th>
                                <th class="p-4 font-bold">Category</th>
                                <th class="p-4 font-bold text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($menus as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4">
                                        <span
                                            class="bg-indigo-100 text-indigo-700 text-xs px-3 py-1 rounded-full font-bold border border-indigo-200 uppercase">
                                            {{ $item->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-slate-600 text-right">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CATEGORY -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Category
                    </h1>
                    <p class="text-sm text-gray-500">Manage product categories</p>
                </div>
                <a href="{{ route('category') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="categoryTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Category Name</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($categories as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- INVENT (Ingredients) -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Stock Ingridient
                    </h1>
                    <p class="text-sm text-gray-500">Stock of ingredients</p>
                </div>
                <a href="{{ route('invent') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="inventTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Stock Name</th>
                                <th class="p-4 font-bold text-center">Stock</th>
                                <th class="p-4 font-bold text-center">Min. Stock</th>
                                <th class="p-4 font-bold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($invents as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4 text-center font-mono">{{ $item->stock }}</td>
                                    <td class="p-4 text-center font-mono text-gray-500">{{ $item->min_stock }}</td>
                                    <td class="p-4 text-center">
                                        @if ($item->stock <= $item->min_stock)
                                            <span
                                                class="bg-red-50 text-red-700 text-xs px-3 py-1 rounded-full font-bold border border-red-200 uppercase">
                                                <i class="fas fa-exclamation-triangle"></i> Menipis
                                            </span>
                                        @else
                                            <span
                                                class="bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold border border-emerald-200 uppercase">
                                                <i class="fas fa-check-circle"></i> Aman
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ORDER -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Active Order
                    </h1>
                    <p class="text-sm text-gray-500">Order that has not been archived</p>
                </div>
                <a href="{{ route('order') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="orderTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Customer Name</th>
                                <th class="p-4 font-bold">Phone Number</th>
                                <th class="p-4 font-bold text-center">Status</th>
                                <th class="p-4 font-bold text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($orders as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-medium">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->atas_nama ?? '-' }}</td>
                                    <td class="p-4 text-xs text-gray-500">{{ $item->no_telpon ?? '-' }}</td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full font-bold border border-yellow-200 uppercase">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-right text-slate-600">
                                        Rp {{ number_format($item->cart->total_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- HISTORY -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Order History
                    </h1>
                    <p class="text-sm text-gray-500">Orders that have been completed / archived</p>
                </div>
                <a href="{{ route('history') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="historyTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Customer Name</th>
                                <th class="p-4 font-bold">Account</th>
                                <th class="p-4 font-bold text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($histories as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-medium">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name ?? '-' }}</td>
                                    <td class="p-4 text-xs text-gray-500">{{ $item->akun ?? '-' }}</td>
                                    <td class="p-4 font-mono text-right text-slate-600">
                                        Rp {{ number_format($item->total_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DISCOUNT -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Discount
                    </h1>
                    <p class="text-sm text-gray-500">Manage discount and promo</p>
                </div>
                <a href="{{ route('discount') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="discountTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Discount Name</th>
                                <th class="p-4 font-bold text-right">Amount / Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($discounts as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4 font-mono text-right text-slate-600">
                                        {{ $item->nominal ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SHOWCASE -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Showcase
                    </h1>
                    <p class="text-sm text-gray-500">Showcase / product package</p>
                </div>
                <a href="{{ route('showcase') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="showcaseTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Showcase Name</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($showcases as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CHAIR (Customer) -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Customer / Chair
                    </h1>
                    <p class="text-sm text-gray-500">List of chairs / customers with QR codes</p>
                </div>
                <a href="{{ route('chair') }}"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fa fa-external-link"></i> Go to Page
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="chairTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Customer Name</th>
                                <th class="p-4 font-bold">Email</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($chairs as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4 text-xs text-gray-500">{{ $item->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#menuTable', {});
            new DataTable('#categoryTable', {});
            new DataTable('#inventTable', {});
            new DataTable('#orderTable', {});
            new DataTable('#historyTable', {});
            new DataTable('#discountTable', {});
            new DataTable('#showcaseTable', {});
            new DataTable('#chairTable', {});
        });
    </script>
    @include('layout.loading')
</body>

</html>