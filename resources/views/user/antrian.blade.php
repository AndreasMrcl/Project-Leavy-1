<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pesanan Saya</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>

        {{-- NAVBAR --}}
        <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
            <div class="bg-white shadow-lg rounded-b-[22px] px-4 py-4 flex items-center gap-2">
                <a href="{{ route('user-home') }}" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                    <span class="material-icons text-lg">arrow_back</span>
                </a>
                <div class="flex-1 text-center">
                    <h1 class="text-base font-semibold text-gray-900">Pesanan Saya</h1>
                </div>
                <button onclick="window.location.reload()" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                    <span class="material-icons text-lg">refresh</span>
                </button>
            </div>
        </div>

        <div class="h-20"></div>

        {{-- BODY --}}
        <div class="px-4 pb-8 space-y-3">
            @forelse ($orders as $order)
                @php
                    $statusInfo = $statuses[$order->no_order] ?? null;
                    $statusText = $statusInfo->status ?? ($order->status ?? 'pending');
                    $key = strtolower(trim($statusText));

                    // Indonesian workflow keywords + Midtrans transaction statuses
                    $isReady   = str_contains($key, 'siap') || str_contains($key, 'ready');
                    $isCooking = str_contains($key, 'masak') || str_contains($key, 'proses') || str_contains($key, 'cook')
                                  || $key === 'pending';
                    $isDone    = str_contains($key, 'selesai') || str_contains($key, 'done')
                                  || in_array($key, ['settlement', 'capture', 'success'], true);
                    $isCancel  = str_contains($key, 'batal') || str_contains($key, 'cancel')
                                  || in_array($key, ['deny', 'expire', 'failure'], true)
                                  || str_starts_with($key, 'error');

                    // Friendly label override for raw Midtrans codes
                    $statusLabel = match (true) {
                        $key === 'pending'    => 'Menunggu Pembayaran',
                        $key === 'settlement' => 'Lunas',
                        $key === 'capture'    => 'Lunas',
                        $key === 'deny'       => 'Ditolak',
                        $key === 'expire'     => 'Kedaluwarsa',
                        $key === 'failure'    => 'Gagal',
                        $key === 'cancel'     => 'Dibatalkan',
                        default               => $statusText,
                    };
                @endphp

                @if ($isReady)
                    {{-- Highlighted "Siap diambil" card --}}
                    <div class="relative overflow-hidden rounded-2xl p-4 text-white shadow-lg"
                         style="background: linear-gradient(135deg, #B91C1C 0%, #7F1D1D 100%);">
                        <div class="absolute -top-5 -right-5 w-28 h-28 rounded-full bg-white opacity-10"></div>

                        <div class="relative flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-white bg-opacity-20 flex items-center justify-center">
                                <span class="material-icons">notifications_active</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] uppercase tracking-widest font-bold opacity-80">Siap diambil</p>
                                <p class="text-base font-extrabold leading-tight">Pesanan Anda sudah siap!</p>
                            </div>
                        </div>
                        <div class="relative mt-3 pt-3 border-t border-white border-opacity-20 flex justify-between items-center">
                            <span class="font-mono text-xs opacity-90">{{ $order->no_order }}</span>
                            <span class="text-sm font-bold">{{ $order->cart->cartMenus->count() }} item</span>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-mono text-[11px] text-gray-400 font-semibold">{{ $order->no_order ?? '-' }}</p>
                                <h2 class="font-bold text-sm text-gray-900 truncate mt-0.5">
                                    {{ $order->atas_nama ?? '-' }}
                                </h2>
                            </div>
                            @php
                                $badgeClass = match (true) {
                                    $isCooking => 'bg-amber-50 text-amber-700',
                                    $isDone    => 'bg-green-50 text-green-700',
                                    $isCancel  => 'bg-red-50 text-red-700',
                                    default    => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="{{ $badgeClass }} px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Progress dots (only for cooking) --}}
                        @if ($isCooking)
                            <div>
                                <div class="flex items-center gap-1">
                                    @php
                                        $stages = [
                                            ['label' => 'Diterima', 'state' => 'done'],
                                            ['label' => 'Diproses', 'state' => 'current'],
                                            ['label' => 'Siap',     'state' => 'todo'],
                                        ];
                                    @endphp
                                    @foreach ($stages as $i => $st)
                                        <div class="flex flex-col items-center shrink-0" style="min-width: 50px;">
                                            <div class="w-5 h-5 rounded-full flex items-center justify-center
                                                {{ $st['state'] !== 'todo' ? 'bg-red-800 text-white' : 'bg-gray-200' }}
                                                {{ $st['state'] === 'current' ? 'ring-4 ring-red-100' : '' }}">
                                                @if ($st['state'] === 'done')
                                                    <span class="material-icons" style="font-size:12px;">check</span>
                                                @elseif ($st['state'] === 'current')
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                @endif
                                            </div>
                                            <span class="text-[9px] font-semibold mt-1 {{ $st['state'] !== 'todo' ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $st['label'] }}
                                            </span>
                                        </div>
                                        @if ($i < count($stages) - 1)
                                            <div class="flex-1 h-0.5 {{ $st['state'] === 'done' ? 'bg-red-800' : 'bg-gray-200' }}"></div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="mt-2 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-semibold flex items-center gap-1.5">
                                    <span class="material-icons text-sm">schedule</span>
                                    Estimasi siap dalam ±5 menit
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-gray-100 pt-2 space-y-1">
                            @foreach ($order->cart->cartMenus as $cartMenu)
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-bold text-gray-900">{{ $cartMenu->quantity }}×</span>
                                    <span class="text-gray-700 truncate flex-1">{{ $cartMenu->menu->name }}</span>
                                    @if ($cartMenu->variety && $cartMenu->variety !== 'normal')
                                        <span class="text-[10px] text-gray-400">{{ ucwords(str_replace('_', ' ', $cartMenu->variety)) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($isDone)
                            <a href="{{ route('user-product') }}"
                               class="block w-full text-center py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 active:bg-gray-50">
                                Pesan lagi
                            </a>
                        @endif
                    </div>
                @endif
            @empty
                <div class="text-center py-16 space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center">
                        <span class="material-icons text-3xl text-gray-300">receipt_long</span>
                    </div>
                    <p class="font-semibold text-gray-600">Belum ada pesanan</p>
                    <p class="text-xs text-gray-400">Pesanan Anda akan muncul di sini</p>
                    <a href="{{ route('user-product') }}" class="inline-block px-6 py-2.5 bg-red-800 text-white rounded-xl text-sm font-bold">
                        Pesan Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</body>

</html>
