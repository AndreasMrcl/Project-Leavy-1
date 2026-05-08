<!DOCTYPE html>
<html lang="en">

<head>
    <title>Category</title>
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

        /* Form Validation */
        .form-input.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-feedback {
            color: #16a34a;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
                        <i class="fas fa-tags text-red-500"></i>Category Management
                    </h1>
                    <p class="text-sm text-gray-500">Organize and manage your menu categories</p>
                </div>

                <button id="addBtn" aria-label="Add new category"
                    class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 active:scale-95 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-x-auto">

                    @if ($category->isEmpty())

                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-inbox text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No categories yet</h3>
                            <p class="text-gray-500 mb-6">Get started by creating your first category</p>
                            <button id="emptyAddBtn" aria-label="Add first category"
                                class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold flex items-center gap-2">
                                <i class="fas fa-plus"></i> Create Category
                            </button>
                        </div>

                    @else

                        <table id="myTable" class="w-full text-left" role="grid" aria-label="Categories table">
                            <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                                <tr role="row">
                                    <th class="p-4 font-bold text-center rounded-tl-lg" width="5%" role="columnheader">No
                                    </th>
                                    <th class="p-4 font-bold text-center" width="20%" role="columnheader">Created at</th>
                                    <th class="p-4 font-bold" role="columnheader">Name</th>
                                    <th class="p-4 font-bold" role="columnheader">Description</th>
                                    <th class="p-4 font-bold text-center rounded-tr-lg" width="15%" role="columnheader">
                                        Action</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-700 text-sm">
                                @php $no = 1; @endphp
                                @foreach ($category as $item)
                                    <tr class="hover:bg-gray-50 transition duration-150" role="row">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4 font-medium text-center">
                                            <time datetime="{{ $item->created_at->toIso8601String() }}">
                                                {{ $item->created_at->format('d M Y') }}
                                            </time>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-semibold text-gray-800">
                                                {{ $item->name }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <span class="font-semibold text-gray-800 line-clamp-2">
                                                {{ $item->desc }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <button
                                                    class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 active:scale-95 transition"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-desc="{{ $item->desc }}" aria-label="Edit category: {{ $item->name }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form method="post" action="{{ route('delcategory', ['id' => $item->id]) }}"
                                                    class="inline deleteForm">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button"
                                                        class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 active:scale-95 transition"
                                                        aria-label="Delete category: {{ $item->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script src="{{ asset('modal/cate.js') }}"></script>

    <!-- Modals -->
    @include('modal.addCate')

    @include('modal.editCate')

    @include('sweetalert::alert')

    @include('layout.loading')

</body>

</html>