<!DOCTYPE html>
<html lang="en">

<head>
    <title>Keranjang</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>

        {{-- NAVBAR --}}
        <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
            <div class="bg-white shadow-lg rounded-b-[22px]">
                <div class="px-4 pt-4 pb-3 flex items-center gap-2">
                    <a href="{{ route('user-product') }}" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                        <span class="material-icons text-lg">arrow_back</span>
                    </a>
                    <div class="flex-1 text-center">
                        <h1 class="text-base font-semibold text-gray-900">Keranjang</h1>
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
                            ['n' => 2, 'label' => 'Cart',  'active' => true,  'done' => false],
                            ['n' => 3, 'label' => 'Bayar', 'active' => false, 'done' => false],
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
        <div class="px-3 pb-40 space-y-2.5">
            @forelse ($cart->cartMenus as $item)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 flex gap-3 items-center">
                    <div class="w-16 h-16 shrink-0 rounded-xl bg-gray-50 overflow-hidden">
                        <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}" alt="{{ $item->menu->name }}"
                            class='w-full h-full object-cover' />
                    </div>
                    <div class="flex-1 min-w-0 space-y-0.5">
                        <h3 class="font-bold text-sm text-gray-900 truncate">{{ $item->menu->name }}</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @if ($item->variety && $item->variety !== 'normal')
                                <span class="text-[10px] text-red-800 font-semibold">{{ ucwords(str_replace('_', ' ', $item->variety)) }}</span>
                            @endif
                            @if ($item->notes)
                                <span class="text-[10px] text-gray-400 italic truncate">"{{ $item->notes }}"</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center pt-0.5">
                            <span class="text-xs text-gray-500"><span class="font-bold text-gray-900">{{ $item->quantity }}×</span></span>
                            <span class="font-bold text-sm text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <form method="post" action="{{ route('user-removecart', ['id' => $item->id]) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-800 flex items-center justify-center hover:bg-red-100">
                            <span class="material-icons text-base">delete_outline</span>
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-16 space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center">
                        <span class="material-icons text-3xl text-gray-300">shopping_cart</span>
                    </div>
                    <p class="font-semibold text-gray-600">Keranjang masih kosong</p>
                    <p class="text-xs text-gray-400">Pilih menu untuk mulai memesan</p>
                    <a href="{{ route('user-product') }}" class="inline-block px-6 py-2.5 bg-red-800 text-white rounded-xl text-sm font-bold">
                        Pilih Menu
                    </a>
                </div>
            @endforelse

            {{-- Totals --}}
            @if ($cart->cartMenus->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-2 mt-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($cart->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Biaya layanan</span>
                        <span class="text-gray-400">Rp 0</span>
                    </div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-gray-900">Total</span>
                        <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($cart->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- STICKY FOOTER --}}
        <div class="fixed bottom-0 left-0 right-0 w-full sm:max-w-sm mx-auto z-50 bg-white border-t border-gray-100 shadow-[0_-8px_18px_rgba(0,0,0,0.04)]">
            <div class="p-3 flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Total</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($cart->total_amount, 0, ',', '.') }}</p>
                </div>
                @if ($cart->total_amount > 0)
                    <a href="{{ route('user-payment') }}" class="px-5 py-3 bg-red-800 text-white font-bold rounded-xl flex items-center gap-1.5">
                        Ke Pembayaran
                        <span class="material-icons text-base">arrow_forward</span>
                    </a>
                @else
                    <div class="px-5 py-3 bg-gray-300 text-white font-bold rounded-xl flex items-center gap-1.5 cursor-not-allowed">
                        Ke Pembayaran
                        <span class="material-icons text-base">arrow_forward</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
