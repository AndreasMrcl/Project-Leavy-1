<!DOCTYPE html>
<html lang="en">

<head>
    <title>Products</title>
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
                        <i class="fas fa-tags text-red-500"></i>Product Management
                    </h1>
                    <p class="text-sm text-gray-500">Manage system products</p>
                </div>
                <div class="flex gap-2">
                    <button id="addBtn"
                        class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <a href="{{ route('ingridient') }}"
                        class="px-10 py-3 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 transition font-semibold flex items-center gap-2">
                        <i class="fa fa-wrench"></i> Set Ingridients
                    </a>
                </div>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-x-auto">

                    @if ($category->isEmpty())
                                <!-- Empty State -->
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div class="text-gray-400 mb-4">
                                        <i class="fas fa-inbox text-6xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No products available</h3>
                                    <p class="text-gray-500 mb-6">Add products to start managing your catalog.</p>
                                    <button id="emptyAddBtn" aria-label="Add first product"
                                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </div>

                            </div>
                        </div>
                    @else

                @foreach ($category as $cat)
                    <!-- Table Section -->
                    <div class=" mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-folder text-red-500"></i> {{ $cat->name }}
                            <span class="text-sm font-normal text-gray-500">({{ $cat->menus->count() }} items)</span>
                        </h2>
                        <div class="overflow-auto">
                            <table class="categoryTable w-full text-left" data-category-id="{{ $cat->id }}">

                                <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                                    <tr>
                                        <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                        <th class="p-4 font-bold text-center" width="20%">Created at</th>
                                        <th class="p-4 font-bold">Name</th>
                                        <th class="p-4 font-bold">Price</th>
                                        <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="text-gray-700 text-sm">
                                    @php $no = 1; @endphp

                                    @forelse ($cat->menus as $menu)

                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                            <td class="p-4 font-medium text-center">
                                                {{ \Carbon\Carbon::parse($menu->created_at)->format('d M Y') }}
                                            </td>

                                            <td class="p-4">
                                                <span class="font-semibold text-gray-800">
                                                    {{ $menu->name }}
                                                </span>
                                                @if ($menu->has_variety && ! empty($menu->varieties))
                                                    <span class="ml-1 inline-block text-[10px] font-semibold uppercase text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full"
                                                        title="{{ implode(', ', array_map(fn ($v) => ucwords(str_replace('_', ' ', $v)), $menu->varieties)) }}">
                                                        {{ count($menu->varieties) }} variety
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="p-4">
                                                <span class="font-semibold text-gray-800">
                                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                                </span>
                                            </td>

                                            <td class="p-4">
                                                <div class="flex justify-center items-center gap-2">
                                                    <button
                                                        class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                        data-id="{{ $menu->id }}" data-name="{{ $menu->name }}"
                                                        data-price="{{ (int) $menu->price }}" data-category_id="{{ $menu->category_id }}"
                                                        data-desc="{{ $menu->description }}"
                                                        data-has_variety="{{ $menu->has_variety ? 1 : 0 }}"
                                                        data-varieties='@json($menu->varieties ?? [])'
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <form method="post" action="{{ route('delproduct', ['id' => $menu->id]) }}"
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
                                    @empty
                                        <tr>
                                            <td class="p-4 text-center text-gray-500"></td>
                                            <td class="p-4 text-center text-gray-500"></td>
                                            <td class="p-4 text-center text-gray-500">No menu in this category</td>
                                            <td class="p-4 text-center text-gray-500"></td>
                                            <td class="p-4 text-center text-gray-500"></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script src="{{ asset('modal/prod.js') }}"></script>

    <!-- Modals -->
    @include('modal.addProd')

    @include('modal.editProd')

    @include('sweetalert::alert')

    @include('layout.loading')

</body>

</html>