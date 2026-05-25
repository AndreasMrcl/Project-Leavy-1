<!DOCTYPE html>
<html lang="en">

<head>
    <title>Users</title>
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
                        <i class="fas fa-users text-indigo-600"></i> User Management
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Organize and manage your system users</p>
                </div>
            </div>

            @if ($users->isEmpty())
                <!-- Empty State -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4 border border-indigo-100">
                                <i class="fas fa-users text-4xl text-indigo-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No users yet</h3>
                            <p class="text-sm text-gray-500 mt-1">User accounts will appear here once registered.</p>
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
                                    <th class="p-4 font-bold">Name</th>
                                    <th class="p-4 font-bold">Level</th>
                                    <th class="p-4 font-bold">Email</th>
                                    <th class="p-4 font-bold rounded-tr-lg" width="15%">
                                        <div class="flex items-center justify-center">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                                @php $no = 1; @endphp
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                        <td class="p-4 text-center">
                                            <span
                                                class="inline-flex items-center bg-gray-50 text-gray-700 text-xs font-bold px-2 py-1 rounded border border-gray-200">
                                                <i class="far fa-calendar mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                                            </span>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-sm font-bold uppercase shadow-sm shrink-0">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </td>

                                        <td class="p-4">
                                            @php
                                                $levelLower = strtolower($user->level ?? '');
                                                $levelColor = match (true) {
                                                    str_contains($levelLower, 'admin') => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    str_contains($levelLower, 'kasir') => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    str_contains($levelLower, 'user') => 'bg-gray-100 text-gray-700 border-gray-200',
                                                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                                                };
                                            @endphp
                                            <span
                                                class="{{ $levelColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                                {{ $user->level }}
                                            </span>
                                        </td>

                                        <td class="p-4 text-gray-700 text-xs">{{ $user->email }}</td>

                                        <td class="p-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <form method="post"
                                                    action="{{ route('deluser', ['id' => $user->id]) }}"
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
