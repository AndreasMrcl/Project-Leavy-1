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
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i> Dashboard
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan operasi —
                    <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </p>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <!-- Pemasukan Hari Ini -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full -mr-10 -mt-10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pemasukan Hari Ini</span>
                            <div class="bg-green-500 w-9 h-9 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-extrabold text-gray-900 leading-tight">
                            Rp{{ number_format($todayRevenue, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $todayOrderCount }} transaksi</p>
                    </div>
                </div>

                <!-- Order Hari Ini -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-10 -mt-10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Order Hari Ini</span>
                            <div class="bg-blue-500 w-9 h-9 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-extrabold text-gray-900 leading-tight">
                            {{ $todayOrderCount }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">selesai hari ini</p>
                    </div>
                </div>

                <!-- Order Aktif -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-10 -mt-10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Order Aktif</span>
                            <div class="bg-amber-500 w-9 h-9 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-extrabold text-gray-900 leading-tight">
                            {{ $activeOrderCount }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">belum di-archive</p>
                    </div>
                </div>

                <!-- Stok Menipis -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 {{ $lowStockCount > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-full -mr-10 -mt-10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok Menipis</span>
                            <div class="{{ $lowStockCount > 0 ? 'bg-red-500' : 'bg-gray-400' }} w-9 h-9 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-900' }} leading-tight">
                            {{ $lowStockCount }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">item perlu restock</p>
                    </div>
                </div>
            </div>

            <!-- Row: Chart + Top Sellers -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

                <!-- Revenue Chart -->
                <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-bold text-lg text-gray-800">Pemasukan 7 Hari Terakhir</h2>
                            <p class="text-xs text-gray-500">Total dari order settled + history</p>
                        </div>
                        <div class="bg-blue-50 px-3 py-1 rounded-full">
                            <span class="text-xs font-semibold text-blue-600">
                                <i class="fas fa-chart-bar mr-1"></i> Trend
                            </span>
                        </div>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Top Sellers -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-bold text-lg text-gray-800">Top Menu</h2>
                            <p class="text-xs text-gray-500">Bulan {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}</p>
                        </div>
                        <i class="fas fa-trophy text-yellow-500"></i>
                    </div>
                    @if ($topSellers->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-400">Belum ada data penjualan bulan ini.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($topSellers as $name => $qty)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-semibold text-gray-700 truncate pr-2">
                                            {{ $loop->iteration }}. {{ $name }}
                                        </span>
                                        <span class="text-xs font-bold text-blue-600 whitespace-nowrap">{{ $qty }}x</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full"
                                            style="width: {{ ($qty / $topSellerMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row: Recent Orders + Low Stock List -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="fas fa-list text-blue-500"></i> Order Terbaru
                        </h2>
                        <a href="{{ route('order') }}" class="text-xs font-semibold text-blue-500 hover:text-blue-700">
                            Lihat semua →
                        </a>
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fas fa-receipt text-gray-300 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-400">Belum ada order aktif.</p>
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
                                    $statusLabel = $order->status ?? 'menunggu';
                                @endphp
                                <div class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-mono text-xs text-gray-500 truncate">{{ $order->no_order ?? '-' }}</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            Rp{{ number_format($order->cart->total_amount ?? 0, 0, ',', '.') }}
                                            <span class="text-xs text-gray-400 font-normal ml-1">·
                                                {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}
                                            </span>
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }} whitespace-nowrap">
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
                            <i class="fas fa-boxes-stacked text-red-500"></i> Stok Perlu Restock
                        </h2>
                        <a href="{{ route('stock') }}" class="text-xs font-semibold text-blue-500 hover:text-blue-700">
                            Kelola stok →
                        </a>
                    </div>
                    @if ($lowStock->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fas fa-circle-check text-green-400 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-500 font-semibold">Semua stok aman.</p>
                            <p class="text-xs text-gray-400">Tidak ada bahan di bawah minimum.</p>
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
                                <div class="flex justify-between items-center p-3 rounded-lg border {{ $rowBg }}">
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
                    label: 'Pemasukan',
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
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function (ctx) {
                                return 'Rp' + Number(ctx.parsed.y).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 },
                            callback: function (v) {
                                if (v >= 1_000_000) return 'Rp' + (v / 1_000_000).toFixed(1) + 'jt';
                                if (v >= 1_000) return 'Rp' + (v / 1_000).toFixed(0) + 'rb';
                                return 'Rp' + v;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
