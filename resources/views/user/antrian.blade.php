<!DOCTYPE html>
<html lang="en">

<head>
    <title>Antrian</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl rounded-b-[20px]">
                    <div class="flex items-center">
                        <a href="{{ route('user-home') }}" class="p-2 -ml-2 text-gray-700 hover:text-black">
                            <span class="material-icons">arrow_back</span>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Antrian</h1>
                        </div>
                        <div class="w-10"></div>
                    </div>
                </div>
            </div>
            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 space-y-3">
                @forelse ($orders as $order)
                    @php
                        $statusInfo = $statuses[$order->no_order] ?? null;
                        $statusText = $statusInfo->status ?? ($order->status ?? 'pending');
                        $statusClass =
                            $statusInfo->bg_color ??
                            'text-white text-center bg-gray-500 w-fit rounded-xl';
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-gray-500 text-base">person</span>
                                    <h2 class="font-bold text-gray-800 truncate">
                                        {{ $order->atas_nama ?? '-' }}
                                    </h2>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $order->no_order ?? '-' }}</p>
                            </div>
                            <span class="{{ $statusClass }} px-3 py-1 text-xs font-semibold shrink-0">
                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 pt-2 space-y-1">
                            @foreach ($order->cart->cartMenus as $cartMenu)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="font-bold text-gray-700">{{ $cartMenu->quantity }}x</span>
                                    <span class="text-gray-700 truncate">{{ $cartMenu->menu->name }}</span>
                                    @if ($cartMenu->notes)
                                        <span class="text-xs text-gray-400 truncate">— {{ $cartMenu->notes }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 space-y-3">
                        <span class="material-icons text-6xl text-gray-300">receipt_long</span>
                        <p class="text-gray-500">Belum ada antrian</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</body>

</html>
