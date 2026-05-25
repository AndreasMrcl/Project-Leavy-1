<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home</title>
    @include('user.layout.head')
    <style>
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 18px;
        }

        .slider-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slider-slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .slider-slide img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 18px;
        }

        @keyframes slideAnimation {
            0%   { transform: translateX(0); }
            33%  { transform: translateX(-100%); }
            66%  { transform: translateX(-200%); }
            100% { transform: translateX(0); }
        }

        .slider-wrapper {
            animation: slideAnimation 12s infinite;
        }

        /* Hide scrollbar but allow horizontal scroll */
        .hscroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .hscroll::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen pb-6'>

        {{-- BRAND HEADER (gradient + meja + greeting) --}}
        @foreach ($profil as $item)
            <div class="relative overflow-hidden rounded-b-[26px] shadow-xl"
                 style="background: linear-gradient(155deg, #7F1D1D 0%, #B91C1C 95%);">
                {{-- Decorative blobs --}}
                <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-10"></div>
                <div class="absolute -bottom-12 -left-8 w-32 h-32 rounded-full bg-white opacity-5"></div>

                <div class="relative p-5 pt-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] text-white uppercase tracking-widest font-semibold opacity-80">Welcome to</p>
                            <h1 class="text-2xl text-white font-bold leading-tight mt-1 truncate">{{ $item->store }}</h1>
                            <div class="flex items-center gap-1 text-white opacity-80 mt-1">
                                <span class="material-icons text-sm">place</span>
                                <p class="text-xs font-light truncate">{{ $item->location }}</p>
                            </div>
                        </div>

                        @auth
                            <div class="text-center px-3 py-2 rounded-2xl border border-white border-opacity-20"
                                 style="background: rgba(255,255,255,0.18);">
                                <p class="text-[9px] text-white uppercase tracking-wider font-semibold opacity-90">Meja</p>
                                <h2 class="text-lg text-white font-extrabold leading-tight">{{ auth()->user()->name }}</h2>
                            </div>
                        @endauth
                    </div>

                    {{-- Greeting / login row --}}
                    <div class="mt-5 backdrop-blur rounded-2xl px-3 py-2.5 flex items-center gap-3"
                         style="background: rgba(255,255,255,0.13);">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.22);">
                            <span class="material-icons text-white text-base">person</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            @auth
                                <p class="text-[10px] text-white opacity-75">Halo,</p>
                                <p class="text-sm text-white font-semibold truncate">Tamu Meja {{ auth()->user()->name }}</p>
                            @else
                                <p class="text-sm text-white font-semibold">Belum masuk</p>
                                <p class="text-[10px] text-white opacity-75">Login untuk mulai memesan</p>
                            @endauth
                        </div>
                        @guest
                            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg border border-white border-opacity-30 text-white text-xs font-semibold">
                                Login
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        @endforeach

        {{-- PENDING CART BANNER --}}
        @if (! empty($pendingCart))
            <div class="mx-3 mt-3 bg-yellow-50 border border-yellow-300 rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-icons text-white text-lg">priority_high</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-yellow-800 text-sm">Ada order belum selesai</p>
                    <p class="text-xs text-yellow-700">
                        {{ $pendingCart->cartMenus->count() }} item ·
                        Rp {{ number_format($pendingCart->total_amount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <form action="{{ route('user-cart-acknowledge') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-lg hover:bg-yellow-600 whitespace-nowrap">
                            Lanjutkan
                        </button>
                    </form>
                    <form action="{{ route('user-cart-reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-white border border-yellow-400 text-yellow-700 text-[11px] font-semibold rounded-lg hover:bg-yellow-50 whitespace-nowrap">
                            Mulai Baru
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- SHOWCASE CAROUSEL --}}
        <div class="p-4 pb-2">
            <div class="slider-container shadow-md">
                <div class="slider-wrapper">
                    @foreach ($showcase as $item)
                        <div class="slider-slide">
                            <img src="{{ asset('storage/img/' . basename($item->img)) }}" alt="Showcase">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @auth
            {{-- QUICK ACTIONS --}}
            <div class="px-4 grid grid-cols-3 gap-2.5">
                <a href="{{ route('user-product') }}" class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm flex flex-col items-center gap-1.5 active:scale-95 transition">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <span class="material-icons text-red-800 text-xl">restaurant_menu</span>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Menu</p>
                    <p class="text-[10px] text-gray-400">{{ $menus->count() ?? 0 }} item</p>
                </a>
                <a href="{{ route('user-antrian') }}" class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm flex flex-col items-center gap-1.5 active:scale-95 transition">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <span class="material-icons text-red-800 text-xl">receipt_long</span>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Pesanan</p>
                    <p class="text-[10px] text-gray-400">Status</p>
                </a>
                <a href="{{ route('user-akun') }}" class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm flex flex-col items-center gap-1.5 active:scale-95 transition">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <span class="material-icons text-red-800 text-xl">table_restaurant</span>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Meja</p>
                    <p class="text-[10px] text-gray-400">{{ auth()->user()->name }}</p>
                </a>
            </div>
        @endauth

        {{-- POPULAR MENU --}}
        <div class="px-4 pt-5 pb-4">
            <div class="flex items-baseline justify-between mb-2">
                <h2 class="text-base font-bold text-gray-900">Paling Dicari</h2>
                <a href="{{ route('user-product') }}" class="text-xs font-semibold text-red-800 flex items-center gap-0.5">
                    Lihat semua
                    <span class="material-icons text-sm">chevron_right</span>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach ($menus as $menu)
                    <a href="{{ route('user-show', ['id' => $menu->id]) }}" class="bg-white border border-gray-100 rounded-2xl p-2 shadow-sm active:scale-95 transition">
                        <div class="aspect-square w-full rounded-xl bg-gray-50 overflow-hidden mb-2">
                            <img src="{{ asset('storage/img/' . basename($menu->img)) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="px-1 pb-1">
                            <h3 class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $menu->name }}</h3>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm font-bold text-red-800">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <span class="w-6 h-6 rounded-lg bg-red-800 text-white flex items-center justify-center">
                                    <span class="material-icons text-sm">add</span>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
