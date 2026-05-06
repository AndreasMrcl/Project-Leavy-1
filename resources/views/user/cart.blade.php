<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cart</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex items-center">
                        <a href="{{ route('user-product') }}" class="p-2 -ml-2 text-gray-700 hover:text-black">
                            <span class="material-icons">arrow_back</span>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Keranjang</h1>
                        </div>
                        <a href="{{ route('user-home') }}" class="p-2 -mr-2 text-gray-700 hover:text-black">
                            <span class="material-icons">home</span>
                        </a>
                    </div>
                    <hr>
                    <div class="flex justify-between mx-10">
                        <a href="{{ route('user-product') }}">
                            <div class="flex space-x-1">
                                <div class="bg-black p-1 rounded-md">
                                    <h1 class="text-xs font-light text-white px-1">1</h1>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-sm font-light">Product</h1>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('user-cart') }}">
                            <div class="flex space-x-1">
                                <div class="bg-black p-1 rounded-md">
                                    <h1 class="text-xs font-light text-white px-1">2</h1>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-sm font-bold">Cart</h1>
                                </div>
                            </div>
                        </a>
                        <div class="flex space-x-1">
                            <div class="bg-black p-1 rounded-md">
                                <h1 class="text-xs font-light text-white px-1">3</h1>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-sm font-light">Payment</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-32"></div>

            {{-- BODY --}}
            <div class="p-4 space-y-4">
                @forelse ($cart->cartMenus as $item)
                    <div class="bg-white rounded-xl shadow-sm p-3 flex gap-3 items-center">
                        <div class="w-16 h-16 shrink-0">
                            <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}" alt="Product Image"
                                class='w-full h-full object-cover rounded-md' />
                        </div>
                        <div class="flex-1 space-y-1 min-w-0">
                            <h1 class="font-bold text-sm truncate">{{ $item->menu->name }}</h1>
                            @if ($item->variety && $item->variety !== 'normal')
                                <p class="text-xs text-purple-600 font-medium">{{ ucwords(str_replace('_', ' ', $item->variety)) }}</p>
                            @endif
                            @if ($item->notes)
                                <p class="text-xs text-gray-500 truncate">- {{ $item->notes }}</p>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600">{{ $item->quantity }} x</span>
                                <span class="font-semibold text-sm">Rp.{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <form method="post" action="{{ route('user-removecart', ['id' => $item->id]) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="p-2 bg-red-800 rounded-md text-white w-10 h-10 flex items-center justify-center hover:bg-red-900">
                                <span class="material-icons text-base">delete</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 space-y-3">
                        <span class="material-icons text-6xl text-gray-300">shopping_cart</span>
                        <p class="text-gray-500">Keranjang kosong</p>
                        <a href="{{ route('user-product') }}" class="inline-block px-6 py-2 bg-red-800 text-white rounded-full text-sm">
                            Pilih Menu
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- FOOTER --}}
            <div class="h-20"></div>
            <div class="flex flex-col items-center justify-center">
                <div class="fixed bottom-4 right-0 left-0 max-w-xs bg-white p-1 rounded-md mx-auto">
                    <div class="grid grid-cols-2">
                        <div class="mx-auto">
                            <h1 class="text-lg font-light">Total</h1>
                            <h1 class="font-extrabold text-xl">Rp.{{ number_format($cart->total_amount, 0, ',', '.') }}
                            </h1>
                        </div>
                        <div class="my-auto">
                            @if ($cart->total_amount > 0)
                                <a href="{{ route('user-payment') }}">
                                    <h1
                                        class="bg-black bg-opacity-90 font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center">
                                        Payment >
                                    </h1>
                                </a>
                            @else
                                <div
                                    class="bg-gray-400 font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center cursor-not-allowed">
                                    Payment >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
