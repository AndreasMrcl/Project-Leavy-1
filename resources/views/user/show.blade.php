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
            <form action="{{ route('user-postcart') }}" method="post">
                @csrf
                <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                    <div class="p-6 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                        <div class="flex items-center">
                            <a href="{{ route('user-product') }}" class="p-2 -ml-2 text-gray-700 hover:text-black">
                                <span class="material-icons">arrow_back</span>
                            </a>
                            <div class="mx-auto">
                                <h1 class="text-center text-xl font-extralight">Detail</h1>
                            </div>
                            <a href="{{ route('user-home') }}" class="p-2 -mr-2 text-gray-700 hover:text-black">
                                <span class="material-icons">home</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="h-16">

                </div>
                {{-- BODY --}}
                <div class="p-4 space-y-4">
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    <input type="hidden" name="quantity" id="quantityInput" value="1">
                    <div class="flex flex-col justify-end items-end">
                        <div class="p-2 border-red-800 rounded-md border-2 w-fit shadow-xl">
                            <div class="flex gap-2 items-center">
                                <span class="material-icons text-red-800">payments</span>
                                <h1 class="text-black text-2xl font-bold">Rp.
                                    {{ number_format($menu->price, 0, ',', '.') }}
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="w-36 h-72 mx-auto my-auto">
                        <img src="{{ asset('storage/img/' . basename($menu->img)) }}" alt="Product Image"
                            class='mx-auto my-auto w-full h-full' />
                    </div>
                    <div class="">
                        <h1 class="text-black text-4xl font-extrabold">{{ $menu->name }}</h1>
                    </div>
                    <div class="">
                        <h1 class="text-black text-sm font-light">{{ $menu->description }}</h1>
                    </div>
                    @if ($menu->has_variety && ! empty($menu->varieties))
                        <div class="space-y-2">
                            <h1 class='text-black text-base font-light'>*Variety</h1>
                            <input type="hidden" name="variety" id="varietyInput" value="{{ $menu->varieties[0] }}">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($menu->varieties as $i => $v)
                                    <button type="button"
                                        data-variety="{{ $v }}"
                                        class="varietyChip px-4 py-2 rounded-full border text-sm font-semibold transition {{ $i === 0 ? 'bg-red-800 text-white border-red-800' : 'bg-gray-50 text-gray-700 border-gray-300' }}">
                                        {{ ucwords(str_replace('_', ' ', $v)) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <h1 class='text-black text-base font-light'>*Discount</h1>
                        <select name="discount_id" class='border p-2 w-full bg-gray-50 rounded-xl'>
                            <option value="">No Discount</option>
                            @foreach ($discount as $discount)
                                <option value="{{ $discount->id }}">{{ $discount->name }}
                                    ({{ $discount->percentage }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-black text-base font-light">*Notes</h1>
                        <textarea class="border p-2 w-full bg-gray-50 rounded-xl" name="notes" id="notes" cols="2" rows="2"
                            placeholder="Add notes here">{{ $menu->notes }}</textarea>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="h-20">

</div>

<div class="w-full fixed bottom-4 max-w-sm mx-auto p-1">
                    <div class='grid grid-cols-2 w-full'>
                        <div class=''>
                            <div class='flex items-center justify-center gap-4'>
                                <button type="button"
                                    class='bg-black bg-opacity-90 text-white font-bold text-base rounded-lg w-12 h-12 flex items-center justify-center'
                                    onclick="decrement()">
                                    -
                                </button>
                                <div class='text-black text-center font-base' id="quantityDisplay">1</div>
                                <button type="button"
                                    class='bg-black bg-opacity-90 text-white font-bold text-base rounded-lg w-12 h-12 flex items-center justify-center'
                                    onclick="increment()">
                                    +
                                </button>
                            </div>
                        </div>
                        <button type="submit"
                            class='bg-black bg-opacity-90 text-white font-bold text-base rounded-full  text-center'>
                            Add To Cart
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

        document.querySelectorAll('.varietyChip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.varietyChip').forEach(function (b) {
                    b.classList.remove('bg-red-800', 'text-white', 'border-red-800');
                    b.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-300');
                });
                btn.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-300');
                btn.classList.add('bg-red-800', 'text-white', 'border-red-800');
                document.getElementById('varietyInput').value = btn.dataset.variety;
            });
        });
    </script>
</body>


</html>
