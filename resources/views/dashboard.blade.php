<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-indigo-600"></i>
                        Dashboard
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Operational Summary
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, d F Y') }}
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Today's Revenue
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </h2>
                    @if ($revenueTrend !== null)
                        <p class="text-xs mt-2 flex items-center gap-1 {{ $revenueTrend >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            <i class="fa-solid fa-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }}"></i>
                            {{ $revenueTrend >= 0 ? '+' : '' }}{{ $revenueTrend }}% vs yesterday
                        </p>
                    @else
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-receipt"></i>
                            {{ $todayOrderCount }} transactions
                        </p>
                    @endif
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Today's Orders
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $todayOrderCount }}
                    </h2>
                    @if ($orderTrend !== null)
                        <p class="text-xs mt-2 flex items-center gap-1 {{ $orderTrend >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            <i class="fa-solid fa-arrow-{{ $orderTrend >= 0 ? 'up' : 'down' }}"></i>
                            {{ $orderTrend >= 0 ? '+' : '' }}{{ $orderTrend }}% vs yesterday
                        </p>
                    @else
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i>
                            completed today
                        </p>
                    @endif
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Active Orders
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $activeOrderCount }}
                    </h2>
                    <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-clock"></i>
                        not yet archived
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Low Stock
                    </p>
                    <h2 class="text-2xl font-bold {{ $lowStock->count() > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                        {{ $lowStock->count() }}
                    </h2>
                    <p class="text-xs {{ $lowStock->count() > 0 ? 'text-red-600' : 'text-gray-400' }} mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        needs restock
                    </p>
                </div>

            </div>

            <!-- SUMMARY SECTION -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Monthly Revenue -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs text-green-600 uppercase font-semibold tracking-wide">Monthly Revenue</p>
                            <h3 class="text-2xl font-bold text-green-700 mt-2 truncate">
                                Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
                            </h3>
                            <p class="text-xs text-green-600 mt-2">
                                {{ now()->format('F Y') }}
                            </p>
                        </div>
                        <div class="text-4xl text-green-300 opacity-50 shrink-0">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Orders -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs text-blue-600 uppercase font-semibold tracking-wide">Monthly Orders</p>
                            <h3 class="text-3xl font-bold text-blue-700 mt-2">{{ $monthlyOrderCount }}</h3>
                            <p class="text-xs text-blue-600 mt-2">completed transactions</p>
                        </div>
                        <div class="text-4xl text-blue-300 opacity-50 shrink-0">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expense -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs text-yellow-600 uppercase font-semibold tracking-wide">Monthly Expense</p>
                            <h3 class="text-2xl font-bold text-yellow-700 mt-2 truncate">
                                Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                            </h3>
                            <p class="text-xs text-yellow-600 mt-2">total spend</p>
                        </div>
                        <div class="text-4xl text-yellow-300 opacity-50 shrink-0">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Customers -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs text-purple-600 uppercase font-semibold tracking-wide">Monthly Customers</p>
                            <h3 class="text-3xl font-bold text-purple-700 mt-2">{{ $monthlyCustomers }}</h3>
                            <p class="text-xs text-purple-600 mt-2">unique sessions</p>
                        </div>
                        <div class="text-4xl text-purple-300 opacity-50 shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Row: Chart + Top Sellers -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

                <!-- Revenue Chart -->
                <div class="xl:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-bold text-lg text-gray-800">Revenue Last 7 Days</h2>
                            <p class="text-xs text-gray-500">Total from settled orders + history</p>
                        </div>
                        <div class="bg-blue-50 px-3 py-1 rounded-full">
                            <span class="text-xs font-semibold text-blue-600">
                                <i class="fas fa-chart-bar mr-1"></i> Trend
                            </span>
                        </div>
                    </div>
                    <div style="height: 260px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="fas fa-list text-blue-500"></i> Recent Orders
                        </h2>
                        <a href="{{ route('order') }}" class="text-xs font-semibold text-blue-500 hover:text-blue-700">
                            View all →
                        </a>
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fas fa-receipt text-gray-300 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-400">No active orders yet.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentOrders as $order)
                                @php
                                    $statusColor = match ($order->status) {
                                        'settlement', 'capture' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'expire', 'deny', 'cancel' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = $order->status ?? 'pending';
                                @endphp
                                <div
                                    class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-mono text-xs text-gray-500 truncate">
                                            {{ $order->no_order ?? '-' }}</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            Rp{{ number_format($order->cart->total_amount ?? 0, 0, ',', '.') }}
                                            <span class="text-xs text-gray-400 font-normal ml-1">·
                                                {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}
                                            </span>
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }} whitespace-nowrap">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Low Stock -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="fas fa-boxes-stacked text-red-500"></i> Stock Needs Restock
                        </h2>
                        <a href="{{ route('stock') }}" class="text-xs font-semibold text-blue-500 hover:text-blue-700">
                            Manage stock →
                        </a>
                    </div>
                    @if ($lowStock->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fas fa-circle-check text-green-400 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-500 font-semibold">All stock is safe.</p>
                            <p class="text-xs text-gray-400">No ingredient below minimum.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($lowStock as $item)
                                @php
                                    $isOut = $item->stock <= 0;
                                    $rowBg = $isOut ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200';
                                    $textColor = $isOut ? 'text-red-700' : 'text-amber-700';
                                    $iconColor = $isOut ? 'text-red-500' : 'text-amber-500';
                                @endphp
                                <div
                                    class="flex justify-between items-center p-3 rounded-lg border {{ $rowBg }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <i class="fas fa-exclamation-circle {{ $iconColor }}"></i>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 truncate">{{ $item->name }}</p>
                                            <p class="text-xs {{ $textColor }}">
                                                Min: {{ $item->min_stock }} {{ $item->unit }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right whitespace-nowrap">
                                        <p class="font-bold {{ $textColor }} text-lg">
                                            {{ $item->stock }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $item->unit }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </main>

    <script>
        const revenueLabels = {!! json_encode($chartLabels) !!};
        const revenueData = {!! json_encode($chartData) !!};

        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp' + Number(ctx.parsed.y).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(v) {
                                if (v >= 1_000_000) return 'Rp' + (v / 1_000_000).toFixed(1) + 'M';
                                if (v >= 1_000) return 'Rp' + (v / 1_000).toFixed(0) + 'K';
                                return 'Rp' + v;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>

    @include('sweetalert::alert')

</body>

</html>
