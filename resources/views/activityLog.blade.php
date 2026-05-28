<!DOCTYPE html>
<html lang="en">

<head>
    <title>Log Aktivitas</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Override DataTables agar seragam dengan Tailwind */
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
                        <i class="fas fa-history text-indigo-600"></i> Log Activity
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">View user and system activity logs</p>
                </div>
            </div>

            @if ($logs->isEmpty())
                <!-- Empty State -->
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4 border border-indigo-100">
                                <i class="fas fa-history text-4xl text-indigo-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No Activity Logs Found</h3>
                            <p class="text-sm text-gray-500 mt-1">System activities will be recorded here
                                automatically.</p>
                        </div>
                    </div>
                </div>
            @else
            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left border-collapse stripe hover">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal font-bold">
                            <tr>
                                <th class="p-4 rounded-tl-lg w-1/4">Time / Account</th>
                                <th class="p-4 w-1/5">Action Type</th>
                                <th class="p-4 rounded-tr-lg">Description</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50 transition duration-150 align-top">

                                    <!-- 1. Waktu & Aktor -->
                                    <td class="p-4">
                                        <div class="flex flex-col gap-1 mb-3">
                                            <span class="font-bold text-gray-800 text-sm">
                                                {{ $log->created_at_formatted }}
                                            </span>
                                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ $log->created_at_diff }}
                                            </span>
                                        </div>
                                        @php
                                            $actor = $log->staff ?? $log->user;
                                            $name = $actor?->name ?? 'System';
                                            $roleLabel = $log->staff_id ? 'Staff' : ($log->user_id ? 'Admin' : 'System');
                                            $actorId = $log->staff_id ?? $log->user_id ?? '-';
                                            $avatarClass = $log->staff_id
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-indigo-100 text-indigo-600';
                                        @endphp

                                        <div
                                            class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-100 hover:bg-white hover:shadow-sm transition">
                                            <div
                                                class="w-8 h-8 rounded-full {{ $avatarClass }} flex items-center justify-center text-xs font-bold shrink-0 uppercase shadow-sm">
                                                {{ strtoupper(substr($name, 0, 1)) }}
                                            </div>

                                            <div class="overflow-hidden">
                                                <p class="font-bold text-xs text-gray-700 truncate"
                                                    title="{{ $name }}">
                                                    {{ $name }}
                                                </p>

                                                <p class="text-[10px] text-gray-400 truncate">
                                                    {{ $roleLabel }} #{{ $actorId }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Action (Badge Warna-warni) -->
                                    <td class="p-4 align-middle">
                                        @php
                                            $actLower = strtolower($log->activity_type);
                                            $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                            $icon = 'fa-info-circle';

                                            // Logika Warna Badge berdasarkan Kata Kunci
                                            if (str_contains($actLower, 'create') || str_contains($actLower, 'add')) {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                $icon = 'fa-plus-circle';
                                            } elseif (
                                                str_contains($actLower, 'update') ||
                                                str_contains($actLower, 'edit')
                                            ) {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $icon = 'fa-edit';
                                            } elseif (
                                                str_contains($actLower, 'delete') ||
                                                str_contains($actLower, 'remove') ||
                                                str_contains($actLower, 'destroy')
                                            ) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $icon = 'fa-trash-alt';
                                            } elseif (
                                                str_contains($actLower, 'login') ||
                                                str_contains($actLower, 'logout')
                                            ) {
                                                $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                                $icon = 'fa-key';
                                            }
                                        @endphp
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase flex items-center gap-2 w-fit border shadow-sm {{ $badgeClass }}">
                                            <i class="fas {{ $icon }}"></i>
                                            {{ $log->activity_type }}
                                        </span>
                                    </td>

                                    <!-- 3. Description -->
                                    <td class="p-4 align-middle">
                                        <div class="bg-white border border-gray-100 p-4 rounded-lg shadow-sm">
                                            <p class="text-gray-700 font-medium text-sm leading-relaxed">
                                                {{ $log->description }}
                                            </p>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#myTable', {
                order: []
            });
        });
    </script>
</body>

</html>