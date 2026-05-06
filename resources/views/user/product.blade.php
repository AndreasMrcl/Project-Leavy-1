<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex items-center">
                        <a href="{{ route('user-home') }}" class="p-2 -ml-2 text-gray-700 hover:text-black">
                            <span class="material-icons">arrow_back</span>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Our Products</h1>
                        </div>
                        <a href="{{ route('user-cart') }}" class="p-2 -mr-2 text-gray-700 hover:text-black relative">
                            <span class="material-icons">shopping_cart</span>
                            @if ($cart && $cart->cartMenus->count() > 0)
                                <span class="absolute top-0 right-0 bg-red-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ $cart->cartMenus->count() }}</span>
                            @endif
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
                                    <h1 class="text-sm font-bold">Product</h1>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('user-cart') }}">
                            <div class="flex space-x-1">
                                <div class="bg-black p-1 rounded-md">
                                    <h1 class="text-xs font-light text-white px-1">2</h1>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-sm font-light">Cart</h1>
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
            <div class="h-32">

            </div>
            {{-- BODY --}}
            <div class="p-2 space-y-2">
                @foreach ($category as $item)
                    <div>
                        <h1 class="text-black text-xl font-bold">{{ $item->name }}</h1>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($item->menus as $menu)
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
                @endforeach
            </div>

            {{-- FOOTER --}}
            <div class="h-20">

            </div>
            <div class="flex flex-col items-center justify-center">
            <div class="fixed bottom-4 right-0 left-0 max-w-xs p-1 bg-white rounded-md mx-auto">
                <div class="grid grid-cols-2">
                    <div class="mx-auto">
                        <h1 class="text-lg font-light">Total</h1>
                        <h1 class="font-extrabold text-xl">Rp.{{ number_format($cart->total_amount, 0, ',', '.') }}
                        </h1>
                    </div>
                    <div class="my-auto">
                        @if ($cart->total_amount > 0)
                            <a href="{{ route('user-cart') }}">
                                <h1
                                    class="bg-black font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center">
                                    Cart >
                                </h1>
                            </a>
                        @else
                            <div
                                class="bg-gray-400 font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center cursor-not-allowed">
                                Cart >
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
