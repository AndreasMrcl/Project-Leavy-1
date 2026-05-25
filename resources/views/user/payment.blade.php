<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pembayaran</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>
        <form action="{{ route('user-postorder') }}" method="POST">
            @csrf

            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="bg-white shadow-lg rounded-b-[22px]">
                    <div class="px-4 pt-4 pb-3 flex items-center gap-2">
                        <a href="{{ route('user-cart') }}" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                            <span class="material-icons text-lg">arrow_back</span>
                        </a>
                        <div class="flex-1 text-center">
                            <h1 class="text-base font-semibold text-gray-900">Pembayaran</h1>
                            <p class="text-[10px] text-gray-400">Meja {{ auth()->user()->name ?? '-' }}</p>
                        </div>
                        <a href="{{ route('user-home') }}" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                            <span class="material-icons text-lg">home</span>
                        </a>
                    </div>

                    {{-- Step indicator --}}
                    <div class="px-5 pb-3 flex items-center gap-2">
                        @php
                            $steps = [
                                ['n' => 1, 'label' => 'Menu',  'active' => false, 'done' => true],
                                ['n' => 2, 'label' => 'Cart',  'active' => false, 'done' => true],
                                ['n' => 3, 'label' => 'Bayar', 'active' => true,  'done' => false],
                            ];
                        @endphp
                        @foreach ($steps as $i => $s)
                            <div class="flex items-center gap-1.5 shrink-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold
                                    {{ $s['active'] || $s['done'] ? 'bg-red-800 text-white' : 'bg-gray-200 text-gray-400' }}">
                                    @if ($s['done'])
                                        <span class="material-icons text-sm">check</span>
                                    @else
                                        {{ $s['n'] }}
                                    @endif
                                </div>
                                <span class="text-xs {{ $s['active'] ? 'font-bold text-gray-900' : 'font-semibold text-gray-400' }}">{{ $s['label'] }}</span>
                            </div>
                            @if ($i < count($steps) - 1)
                                <div class="flex-1 h-0.5 rounded-full {{ $s['done'] ? 'bg-red-800' : 'bg-gray-200' }}"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="h-[120px]"></div>

            {{-- BODY --}}
            <div class="px-4 pb-32 space-y-5">
                <div>
                    <h2 class="font-bold text-gray-900">Data Pemesan</h2>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Data digunakan untuk konfirmasi pesanan. Pastikan nomor aktif.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-gray-600"><span class="text-red-500">*</span> Nama Lengkap</label>
                    <input class="w-full h-12 border border-gray-200 bg-white rounded-xl px-4 text-sm outline-none focus:border-red-800"
                        placeholder="Mis: Andi Wijaya" id="atas_nama" type="text" name="atas_nama" required>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-gray-600"><span class="text-red-500">*</span> Nomor HP / WhatsApp</label>
                    <input class="w-full h-12 border border-gray-200 bg-white rounded-xl px-4 text-sm outline-none focus:border-red-800"
                        placeholder="08xxxxxxxxxx" id="no_telpon" name="no_telpon" inputmode="numeric" required>
                </div>

                {{-- Service / meja info --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Servis</p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-icons text-red-800 text-base">table_restaurant</span>
                            <span class="text-sm font-bold text-gray-900">Dine In</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Meja</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ auth()->user()->name ?? '-' }}</p>
                    </div>
                </div>

                {{-- Order summary --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Ringkasan Pesanan</h3>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 space-y-2">
                        @foreach ($cart->cartMenus as $item)
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 shrink-0 rounded-lg bg-gray-50 overflow-hidden">
                                    <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}" alt="" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">
                                        <span class="font-bold">{{ $item->quantity }}×</span> {{ $item->menu->name }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        @if ($item->variety && $item->variety !== 'normal')
                                            {{ ucwords(str_replace('_', ' ', $item->variety)) }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                    @if ($item->notes)
                                        <p class="text-[10px] text-gray-500 italic truncate">"{{ $item->notes }}"</p>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-gray-900 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach

                        <div class="border-t border-gray-100 pt-2 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <span class="text-lg font-extrabold text-gray-900">
                                Rp {{ number_format($cart->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STICKY FOOTER --}}
            <div class="fixed bottom-0 left-0 right-0 w-full sm:max-w-sm mx-auto z-50 bg-white border-t border-gray-100 shadow-[0_-8px_18px_rgba(0,0,0,0.04)]">
                <div class="p-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Total</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($cart->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <button type="submit" class="px-5 py-3 bg-red-800 text-white font-bold rounded-xl flex items-center gap-1.5 active:bg-red-900">
                        Bayar Sekarang
                        <span class="material-icons text-base">arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
