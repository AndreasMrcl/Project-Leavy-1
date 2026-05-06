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
        }

        .slider-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 20px;
            /* This will space the slides out */
        }

        .slider-slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .slider-slide img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Automatic sliding effect using keyframes */
        @keyframes slideAnimation {
            0% {
                transform: translateX(0);
            }

            33% {
                transform: translateX(-100%);
            }

            66% {
                transform: translateX(-200%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .slider-wrapper {
            animation: slideAnimation 10s infinite;
        }
    </style>
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto '>
        @if (! empty($pendingCart))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-2 mt-2 rounded-lg">
                <div class="flex items-start gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-800">Ada pesanan sebelumnya di meja ini</p>
                        <p class="text-sm text-yellow-700">
                            {{ $pendingCart->cartMenus->count() }} item &middot;
                            Rp {{ number_format($pendingCart->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('user-cart-acknowledge') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-semibold text-sm">
                            Lanjutkan
                        </button>
                    </form>
                    <form action="{{ route('user-cart-reset') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-white border border-yellow-500 text-yellow-700 rounded-md hover:bg-yellow-50 font-semibold text-sm">
                            Mulai Baru
                        </button>
                    </form>
                </div>
            </div>
        @endif
        <div class='sm:max-w-sm'>
            <div class="space-y-2">
                @foreach ($profil as $item)
                    <div class="grid grid-cols-4 bg-red-900 p-4 shadow-xl rounded-b-[20px]">
                        <div class="space-y-2 col-span-3">
                            <div class="flex gap-2">
                                <div class="my-auto">
                                    <h1 class="text-2xl text-white font-bold">{{ $item->store }}</h1>
                                </div>
                            </div>
                            <div>
                                <div class="my-auto">
                                    <h1 class="text-sm text-white font-light">{{ $item->location }}</h1>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                @auth
                                    <div class="space-y-2">
                                        <h1 class="text-2xl font-base text-white">Hi, {{ auth()->user()->name }}</h1>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <div>
                                            <h1 class="text-2xl font-base text-white">
                                                Welcome!
                                            </h1>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="p-1 border border-white rounded-md">
                                                <a href="{{ route('login') }}" class="">
                                                    <h1 class="text-white text-sm px-2 text-center">Login</h1>
                                                </a>
                                            </div>
                                            <div class="p-1 border border-white rounded-md">
                                                <a href="{{ route('register') }}" class="">
                                                    <h1 class="text-white text-sm px-2 text-center">Register</h1>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        </div>
                        @auth
                            <div class="flex flex-col items-end">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-white p-2 rounded-md text-red-900 hover:bg-gray-100">
                                        <span class="material-icons">logout</span>
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                @endforeach
                <div class="p-4 space-y-6">
                    <!-- CSS-based Slider -->
                    <div class="slider-container">
                        <div class="slider-wrapper">
                            @foreach ($showcase as $item)
                                <div class="slider-slide">
                                    <div class="w-full">
                                        <img src="{{ asset('storage/img/' . basename($item->img)) }}"
                                            alt="Showcase Image" class="w-full h-auto rounded-md">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @auth
                        <div class="grid grid-cols-3 gap-4">
                            <a href="{{ route('user-product') }}">
                                <div class="p-3 border rounded-xl bg-red-900 shadow-xl flex justify-center">
                                    <span class="material-icons text-white text-3xl">restaurant_menu</span>
                                </div>
                                <div>
                                    <h1 class="text-base font-light text-black text-center mt-1">Menu</h1>
                                </div>
                            </a>
                            <a href="{{ route('user-antrian') }}">
                                <div class="p-3 border rounded-xl bg-red-900 shadow-xl flex justify-center">
                                    <span class="material-icons text-white text-3xl">receipt_long</span>
                                </div>
                                <div>
                                    <h1 class="text-base font-light text-black text-center mt-1">Pesanan</h1>
                                </div>
                            </a>
                            <a href="{{ route('user-akun') }}">
                                <div class="p-3 border rounded-xl bg-red-900 shadow-xl flex justify-center">
                                    <span class="material-icons text-white text-3xl">table_restaurant</span>
                                </div>
                                <div>
                                    <h1 class="text-base font-light text-black text-center mt-1">Meja</h1>
                                </div>
                            </a>
                        </div>
                        @endauth
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($menus as $menu)
                                <a href="{{ route('user-show', ['id' => $menu->id]) }}">
                                    <div class="bg-red-800 p-2 rounded-md space-y-1">
                                        <div class="p-2 bg-white rounded-md">
                                            <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                                alt="Product Image" class='mx-auto my-auto w-14 h-17 rounded-xl relative' />
                                        </div>
                                        <div>
                                            <h1 class="text-white text-sm font-bold">{{ $menu->name }}</h1>
                                            <h1 class="text-white text-sm font-bold">Rp.
                                                {{ number_format($menu->price, 0, ',', '.') }}
                                            </h1>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
