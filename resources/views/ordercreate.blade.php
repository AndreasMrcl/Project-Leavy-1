<!DOCTYPE html>
<html lang="en">

<head>
    <title>Buat Order</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    @if ($mode === 'append')
                        <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                            <i class="fas fa-utensils text-amber-500"></i> Add to Bill
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">Mode: add items to <span class="font-bold text-amber-600">Table {{ $cart->chair->name ?? '-' }}</span>'s bill. Click <strong>Save to Bill</strong> when done.</p>
                    @else
                        <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                            <i class="fas fa-cash-register text-blue-500"></i> Create New Order
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">Select a menu, set details, then click Payment.</p>
                    @endif
                </div>
                <a href="{{ route('order') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if ($mode === 'append')
                <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-info-circle text-amber-600 text-xl"></i>
                    <p class="text-sm text-amber-800 font-bold">
                        Items added will go into Table {{ $cart->chair->name ?? '-' }}'s <strong>open bill</strong>.
                        Stock is not deducted until the bill is settled.
                    </p>
                </div>
            @endif

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <!-- LEFT: Menu Grid -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-gray-700">Select Product</h3>
                        <input type="text" id="menuSearch" placeholder="Search menu..."
                            class="flex-1 rounded-lg border-gray-300 shadow-sm p-2 border text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div id="menuGrid"
                        class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 max-h-[60vh] overflow-y-auto pr-1">
                        @foreach ($menus as $menu)
                            <button type="button"
                                class="menuCard bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-blue-400 active:scale-95 transition p-2 text-left"
                                data-id="{{ $menu->id }}"
                                data-name="{{ $menu->name }}"
                                data-name-lower="{{ strtolower($menu->name) }}"
                                data-price="{{ $menu->price }}"
                                data-has-variety="{{ $menu->has_variety ? '1' : '0' }}"
                                data-varieties='@json($menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'])'
                                data-img="{{ $menu->img ? asset('storage/img/' . basename($menu->img)) : '' }}">
                                <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden mb-2">
                                    @if ($menu->img)
                                        <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                            alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-utensils text-3xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</div>
                                <div class="text-xs text-blue-600 font-bold mt-0.5">
                                    Rp{{ number_format($menu->price, 0, ',', '.') }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- RIGHT: Cart Panel -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
                    <h3 class="font-bold text-gray-700">Cart</h3>
                    <div class="bg-gray-50 rounded-xl p-3 space-y-2 max-h-[50vh] overflow-y-auto min-h-[12rem]">
                        @forelse ($cart->cartMenus as $item)
                            <div class="flex items-start gap-2 bg-white rounded-lg p-2 shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1">
                                        <span class="font-bold text-gray-700 text-sm">{{ $item->quantity }}x</span>
                                        <span class="text-sm text-gray-800 truncate">{{ $item->menu->name }}</span>
                                    </div>
                                    @if ($item->variety && $item->variety !== 'normal')
                                        <p class="text-xs text-purple-600 font-medium">{{ ucwords(str_replace('_', ' ', $item->variety)) }}</p>
                                    @endif
                                    @if ($item->discount)
                                        <p class="text-xs text-orange-600">{{ $item->discount->name }} ({{ $item->discount->percentage }}%)</p>
                                    @endif
                                    @if ($item->notes)
                                        <p class="text-xs text-gray-500 truncate">— {{ $item->notes }}</p>
                                    @endif
                                    <p class="text-xs font-semibold text-gray-700 mt-0.5">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                                <form method="post" action="{{ route('removecart', ['id' => $item->id]) }}">
                                    @csrf
                                    @method('delete')
                                    <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center bg-red-500 text-white rounded-md hover:bg-red-600 transition text-xs">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-sm">
                                <i class="fas fa-shopping-cart text-3xl block mb-2"></i>
                                Cart is empty
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t pt-3 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-700">Total</span>
                            <span class="font-mono font-bold text-2xl text-gray-900">
                                Rp{{ number_format($cart->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($mode === 'append')
                            <a href="{{ route('order') }}"
                                class="w-full py-3 bg-amber-500 text-white font-bold rounded-lg shadow-md hover:bg-amber-600 transition flex justify-center items-center gap-2">
                                <i class="fas fa-save"></i> Save to Bill
                            </a>
                        @else
                            <button type="button" id="paymentBtn"
                                class="w-full py-3 bg-emerald-500 text-white font-bold rounded-lg shadow-md hover:bg-emerald-600 transition flex justify-center items-center gap-2 {{ $cart->cartMenus->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $cart->cartMenus->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-money-bill-wave"></i> Payment
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ITEM DETAIL MODAL -->
    <div id="itemModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
            <button class="closeItemModal absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>

            <form method="post" action="{{ route('postcart') }}" class="space-y-4">
                @csrf
                @if ($mode === 'append')
                    <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                @endif
                <input type="hidden" name="menu_id" id="itemMenuId">

                <div class="flex items-center gap-4">
                    <div id="itemImageWrap" class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center text-gray-300 shrink-0">
                        <i class="fas fa-utensils text-3xl"></i>
                    </div>
                    <div>
                        <h2 id="itemName" class="text-xl font-bold text-gray-800">-</h2>
                        <p id="itemPrice" class="text-blue-600 font-bold">Rp0</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Quantity</label>
                    <div class="flex items-center justify-between gap-2 bg-gray-50 rounded-lg p-2">
                        <button type="button" id="qtyDecr"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 active:scale-95 transition">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span id="qtyDisplay" class="flex-1 text-center font-bold text-xl">1</span>
                        <input type="hidden" name="quantity" id="qtyInput" value="1">
                        <button type="button" id="qtyIncr"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 active:scale-95 transition">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div id="varietyWrap" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Variety</label>
                    <select name="variety" id="varietySelect"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Discount
                        <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <select name="discount_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                        <option value="">No Discount</option>
                        @foreach ($discounts as $disc)
                            <option value="{{ $disc->id }}">{{ $disc->name }} ({{ $disc->percentage }}%)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Notes
                        <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="text" name="notes" maxlength="255"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g.: extra hot, no straw">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white font-bold rounded-lg shadow-md hover:bg-blue-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </form>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal"
        class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white rounded-xl shadow-xl relative w-full max-w-2xl flex flex-col h-[640px] max-h-[92vh] border border-gray-200">
            <button id="closePaymentModal"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 transition z-10">
                <i class="fas fa-times"></i>
            </button>

            <!-- Header -->
            <div class="px-8 pt-7 pb-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider">Payment</h2>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="text-3xl font-bold text-gray-900 tabular-nums">Rp{{ number_format($cart->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 px-8">
                <button type="button" data-tab="cash"
                    class="paymentTab flex-1 py-3 text-sm font-semibold border-b-2 border-gray-900 text-gray-900 transition">
                    Cash
                </button>
                <button type="button" data-tab="cashless"
                    class="paymentTab flex-1 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition">
                    Cashless
                </button>
                <button type="button" data-tab="openbill"
                    class="paymentTab flex-1 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition">
                    Open Bill
                </button>
            </div>

            <!-- Tab content area -->
            <div class="flex-1 overflow-y-auto px-8 py-6">
                <!-- Cash -->
                <div id="cashTab" class="paymentTabContent h-full">
                    <form method="post" action="{{ route('checkout') }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="payment_method" value="cash">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Cash Received</label>
                                <input type="number" name="cash_received" id="cashReceived" min="0" required
                                    class="w-full rounded-lg border border-gray-300 p-4 text-2xl font-semibold text-center tabular-nums focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"
                                    placeholder="0">
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Change</span>
                                    <span id="changeDisplay" class="font-bold text-xl text-gray-900 tabular-nums">Rp0</span>
                                </div>
                                <p id="cashWarning" class="text-xs text-red-600 mt-2 hidden">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Cash received is less than the total.
                                </p>
                            </div>
                        </div>

                        <div class="flex-1"></div>

                        <button type="submit" id="cashSubmitBtn"
                            class="w-full py-3.5 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                            disabled>
                            Pay
                        </button>
                    </form>
                </div>

                <!-- Cashless -->
                <div id="cashlessTab" class="paymentTabContent h-full hidden">
                    <form method="post" action="{{ route('checkout') }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="payment_method" id="cashlessMethod" value="edc">

                        <!-- EDC / QRIS sub-toggle -->
                        <div class="grid grid-cols-2 gap-1 bg-gray-100 rounded-lg p-1 mb-5">
                            <button type="button" data-method="edc"
                                class="cashlessSubTab py-2 rounded-md text-sm font-semibold bg-white shadow-sm text-gray-900 transition">
                                EDC
                            </button>
                            <button type="button" data-method="online"
                                class="cashlessSubTab py-2 rounded-md text-sm font-semibold text-gray-500 hover:text-gray-700 transition">
                                QRIS
                            </button>
                        </div>

                        <!-- EDC fields -->
                        <div id="edcFields" class="cashlessFields space-y-4">
                            <div class="rounded-lg border border-gray-200 p-4">
                                <p class="text-sm text-gray-600">
                                    Customer swipes/taps card on EDC machine. Enter reference number from receipt for audit (optional).
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    EDC Reference Number <span class="text-gray-400 normal-case font-normal">(optional)</span>
                                </label>
                                <input type="text" name="payment_reference" maxlength="255"
                                    class="w-full rounded-lg border border-gray-300 p-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"
                                    placeholder="Example: 123456789">
                            </div>
                        </div>

                        <!-- QRIS info -->
                        <div id="qrisFields" class="cashlessFields hidden">
                            <div class="rounded-lg border border-gray-200 p-5">
                                <p class="text-sm text-gray-600">
                                    QRIS from Midtrans will appear after clicking <strong>Pay</strong>. Customer scans the code from the POS tablet with an e-wallet / m-banking app.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-1.5 text-xs text-gray-500">
                                    <span class="px-2 py-0.5 border border-gray-200 rounded">GoPay</span>
                                    <span class="px-2 py-0.5 border border-gray-200 rounded">OVO</span>
                                    <span class="px-2 py-0.5 border border-gray-200 rounded">DANA</span>
                                    <span class="px-2 py-0.5 border border-gray-200 rounded">ShopeePay</span>
                                    <span class="px-2 py-0.5 border border-gray-200 rounded">m-Banking</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1"></div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition">
                            Pay
                        </button>
                    </form>
                </div>

                <!-- Open Bill -->
                <div id="openbillTab" class="paymentTabContent h-full hidden">
                    <form method="post" action="{{ route('open-bill') }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="cart_id" value="{{ $cart->id }}">

                        <div class="rounded-lg border border-gray-200 p-4 mb-5">
                            <p class="text-sm text-gray-600">
                                Bill is tagged to the selected table. Customer can keep ordering, cashier settles when leaving. Stock is deducted at settlement.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Table</label>
                            @if ($availableChairs->isEmpty())
                                <div class="rounded-lg border border-gray-200 p-4 text-sm text-gray-500">
                                    No tables available. All tables have open bills, or no chair is registered yet.
                                </div>
                            @else
                                <select name="chair_id" required
                                    class="w-full rounded-lg border border-gray-300 p-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">
                                    <option value="">-- select table --</option>
                                    @foreach ($availableChairs as $chair)
                                        <option value="{{ $chair->id }}">{{ $chair->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="flex-1"></div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                            {{ $availableChairs->isEmpty() ? 'disabled' : '' }}>
                            Open Bill
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            const itemModal = $('#itemModal');
            const paymentModal = $('#paymentModal');

            const formatRp = (n) => 'Rp' + Number(n).toLocaleString('id-ID');

            const labelize = (slug) => String(slug).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

            // Open Item Detail Modal
            $('.menuCard').click(function () {
                const card = $(this);
                const id = card.data('id');
                const name = card.data('name');
                const price = card.data('price');
                const img = card.data('img');
                const hasVariety = String(card.data('has-variety')) === '1';
                const varieties = card.data('varieties') || ['normal'];

                $('#itemMenuId').val(id);
                $('#itemName').text(name);
                $('#itemPrice').text(formatRp(price));
                $('#qtyDisplay').text(1);
                $('#qtyInput').val(1);

                if (img) {
                    $('#itemImageWrap').html(`<img src="${img}" alt="${name}" class="w-full h-full object-cover">`);
                } else {
                    $('#itemImageWrap').html('<i class="fas fa-utensils text-3xl"></i>');
                }

                // Build variety dropdown
                const $varSelect = $('#varietySelect').empty();
                if (hasVariety) {
                    varieties.forEach(v => {
                        $varSelect.append(`<option value="${v}">${labelize(v)}</option>`);
                    });
                    $('#varietyWrap').removeClass('hidden');
                } else {
                    $varSelect.append('<option value="normal">Normal</option>');
                    $('#varietyWrap').addClass('hidden');
                }

                // Reset other fields
                $('#itemModal select[name="discount_id"]').val('');
                $('#itemModal input[name="notes"]').val('');

                itemModal.removeClass('hidden');
            });

            $('.closeItemModal').click(() => itemModal.addClass('hidden'));

            $('#qtyIncr').click(function () {
                let q = parseInt($('#qtyDisplay').text()) + 1;
                if (q > 99) q = 99;
                $('#qtyDisplay').text(q);
                $('#qtyInput').val(q);
            });

            $('#qtyDecr').click(function () {
                let q = parseInt($('#qtyDisplay').text()) - 1;
                if (q < 1) q = 1;
                $('#qtyDisplay').text(q);
                $('#qtyInput').val(q);
            });

            // Live menu filter
            $('#menuSearch').on('input', function () {
                const q = $(this).val().toLowerCase();
                $('.menuCard').each(function () {
                    const n = $(this).data('name-lower');
                    $(this).toggle(String(n).includes(q));
                });
            });

            // Payment Modal trigger
            $('#paymentBtn').click(function () {
                if ($(this).is(':disabled')) return;
                paymentModal.removeClass('hidden');
            });
            $('#closePaymentModal').click(() => paymentModal.addClass('hidden'));

            // Tab switching
            $('.paymentTab').click(function () {
                const tab = $(this).data('tab');
                $('.paymentTab')
                    .removeClass('border-gray-900 text-gray-900')
                    .addClass('border-transparent text-gray-400');
                $('.paymentTabContent').addClass('hidden');

                $(this).removeClass('border-transparent text-gray-400').addClass('border-gray-900 text-gray-900');

                if (tab === 'cash') {
                    $('#cashTab').removeClass('hidden');
                } else if (tab === 'cashless') {
                    $('#cashlessTab').removeClass('hidden');
                } else if (tab === 'openbill') {
                    $('#openbillTab').removeClass('hidden');
                }
            });

            // Cashless sub-toggle (EDC vs QRIS)
            $('.cashlessSubTab').click(function () {
                const method = $(this).data('method');
                $('.cashlessSubTab')
                    .removeClass('bg-white shadow-sm text-gray-900')
                    .addClass('text-gray-500 hover:text-gray-700');
                $(this).removeClass('text-gray-500 hover:text-gray-700').addClass('bg-white shadow-sm text-gray-900');

                $('#cashlessMethod').val(method);
                $('.cashlessFields').addClass('hidden');
                if (method === 'edc') {
                    $('#edcFields').removeClass('hidden');
                } else if (method === 'online') {
                    $('#qrisFields').removeClass('hidden');
                }
            });

            // Cash live calc kembalian
            const totalAmount = {{ $cart->total_amount }};
            $('#cashReceived').on('input', function () {
                const received = parseInt($(this).val()) || 0;
                const change = received - totalAmount;

                if (change >= 0) {
                    $('#changeDisplay').text(formatRp(change)).removeClass('text-red-600').addClass('text-gray-900');
                    $('#cashWarning').addClass('hidden');
                    $('#cashSubmitBtn').prop('disabled', false);
                } else {
                    $('#changeDisplay').text(formatRp(change)).removeClass('text-gray-900').addClass('text-red-600');
                    $('#cashWarning').removeClass('hidden');
                    $('#cashSubmitBtn').prop('disabled', true);
                }
            });

            // Click outside to close modals
            $(window).click((e) => {
                if (e.target === itemModal[0]) itemModal.addClass('hidden');
                if (e.target === paymentModal[0]) paymentModal.addClass('hidden');
            });
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
