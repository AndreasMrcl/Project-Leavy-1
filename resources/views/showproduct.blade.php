<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product Details</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Product Details
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Review the product and add it to your cart</p>
                </div>
                <a href="{{ url()->previous() }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <!-- Product Card -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-6">
                    <form action="{{ route('postcart') }}" method="post" class="space-y-6">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <input type="hidden" name="quantity" id="quantityInput" value="1">

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Product Image -->
                            <div class="flex justify-center items-start">
                                <div
                                    class="w-full max-w-xs aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                                    @if ($menu->img)
                                        <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                            alt="{{ $menu->name }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-utensils text-6xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="space-y-5">
                                <div>
                                    <h2 class="font-extrabold text-3xl text-gray-900">{{ $menu->name }}</h2>
                                    <p class="text-base text-gray-600 mt-2 leading-relaxed">
                                        {{ $menu->description }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg border border-emerald-200">
                                        <i class="fas fa-tag"></i>
                                        <span class="font-mono font-bold text-xl">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </span>
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">
                                        Discount <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <select name="discount_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500">
                                        <option value="">No Discount</option>
                                        @foreach ($discount as $disc)
                                            <option value="{{ $disc->id }}">{{ $disc->name }}
                                                ({{ $disc->percentage }}%)</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">
                                        Notes <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <textarea name="notes" rows="3"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-red-500"
                                        placeholder="Add notes here">{{ $menu->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="border-t border-gray-100 pt-5 flex flex-col sm:flex-row gap-3 items-center">
                            <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2">
                                <button type="button" onclick="decrement()"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 active:scale-95 transition">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantityDisplay"
                                    class="px-6 text-center font-bold text-xl text-gray-900">1</span>
                                <button type="button" onclick="increment()"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 active:scale-95 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <button type="submit"
                                class="flex-1 w-full sm:w-auto px-6 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 hover:scale-105 transition font-bold flex items-center justify-center gap-2 text-sm">
                                <i class="fas fa-cart-plus"></i> Add To Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function increment() {
            var quantityDisplay = document.getElementById('quantityDisplay');
            var quantityInput = document.getElementById('quantityInput');
            var quantity = parseInt(quantityDisplay.innerText);
            quantity++;
            quantityDisplay.innerText = quantity;
            quantityInput.value = quantity;
        }

        function decrement() {
            var quantityDisplay = document.getElementById('quantityDisplay');
            var quantityInput = document.getElementById('quantityInput');
            var quantity = parseInt(quantityDisplay.innerText);
            if (quantity > 1) {
                quantity--;
                quantityDisplay.innerText = quantity;
                quantityInput.value = quantity;
            }
        }
    </script>

    @include('sweetalert::alert')
</body>

</html>
