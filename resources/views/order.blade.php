<!DOCTYPE html>
<html lang="en">

<head>
    <title>Orders</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
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
                        <i class="fas fa-receipt text-blue-500"></i> Orders
                    </h1>
                    <p class="text-sm text-gray-500">List of active orders. Click <strong>Done</strong> to archive to shift history.</p>
                </div>
                <a href="{{ route('addorder') }}"
                    class="px-10 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Order
                </a>
            </div>

            <!-- Open Bills Section -->
            @if ($openBills->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-utensils text-amber-500"></i>
                        <h2 class="font-bold text-lg text-gray-800">Open Bills</h2>
                        <span class="ml-2 text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $openBills->count() }}</span>
                    </div>
                    <div class="p-5 overflow-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-amber-50 text-amber-800">
                                <tr>
                                    <th class="p-3 font-bold rounded-tl-lg">Opened</th>
                                    <th class="p-3 font-bold">Table</th>
                                    <th class="p-3 font-bold">Items</th>
                                    <th class="p-3 font-bold">Total</th>
                                    <th class="p-3 font-bold">Status</th>
                                    <th class="p-3 font-bold text-center rounded-tr-lg" width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach ($openBills as $bill)
                                    <tr class="border-t border-gray-100">
                                        <td class="p-3">{{ $bill->opened_at?->format('d M H:i') ?? '-' }}</td>
                                        <td class="p-3 font-semibold">{{ $bill->chair->name ?? '-' }}</td>
                                        <td class="p-3 text-xs">
                                            @foreach ($bill->cartMenus as $cm)
                                                <div>
                                                    <span class="font-semibold">{{ $cm->quantity }}x</span>
                                                    {{ $cm->menu->name }}
                                                    @if ($cm->variety && $cm->variety !== 'normal')
                                                        <span class="text-purple-600">({{ ucwords(str_replace('_', ' ', $cm->variety)) }})</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="p-3 font-bold">
                                            Rp{{ number_format($bill->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3">
                                            <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-amber-500 rounded-xl">open</span>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex justify-center items-center gap-2">
                                                <a href="{{ route('addorder', ['cart_id' => $bill->id]) }}"
                                                    class="w-9 h-9 flex items-center justify-center bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 hover:scale-105 transition"
                                                    title="Tambah Item">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <button type="button"
                                                    class="openBillPay w-9 h-9 flex items-center justify-center bg-green-500 text-white rounded-lg shadow hover:bg-green-600 hover:scale-105 transition"
                                                    title="Bayar"
                                                    data-cart-id="{{ $bill->id }}"
                                                    data-total="{{ $bill->total_amount }}"
                                                    data-chair="{{ $bill->chair->name ?? '-' }}">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                                <form action="{{ route('cancel-open-bill', ['cartId' => $bill->id]) }}" method="POST" class="inline cancelBillForm">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button"
                                                        class="cancel-bill-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                        title="Batalkan Bill">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">

                    @if ($orders->isEmpty())
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-inbox text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No active orders</h3>
                            <p class="text-gray-500 mb-6">Create a new order to start serving customers</p>
                            <a href="{{ route('addorder') }}" aria-label="Create first order"
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold flex items-center gap-2">
                                <i class="fas fa-plus"></i> Create Order
                            </a>
                        </div>
                    @else

                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Order ID</th>
                                <th class="p-4 font-bold">Service</th>
                                <th class="p-4 font-bold">Chair</th>
                                <th class="p-4 font-bold">Order</th>
                                <th class="p-4 font-bold">Payment</th>
                                <th class="p-4 font-bold">Ref</th>
                                <th class="p-4 font-bold">Total</th>
                                <th class="p-4 font-bold">Status</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}
                                    </td>
                                    <td class="p-4 font-mono text-xs">{{ $order->no_order ?? '-' }}</td>
                                    <td class="p-4">{{ $order->layanan }}</td>
                                    <td class="p-4">
                                        {{ $order->cart->user->name ?? $order->cart->chair->name ?? '-' }}
                                    </td>
                                    <td class="p-4 text-xs">
                                        @foreach ($order->cart->cartMenus as $cartMenu)
                                            <div>
                                                <span class="font-semibold">{{ $cartMenu->quantity }}x</span>
                                                {{ $cartMenu->menu->name }}
                                                @if ($cartMenu->notes)
                                                    <span class="text-gray-500">— {{ $cartMenu->notes }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="p-4">
                                        @if ($order->payment_type)
                                            <span
                                                class="px-2 py-1 bg-gray-100 rounded-md text-xs font-semibold uppercase">{{ $order->payment_type }}</span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-xs">{{ $order->payment_reference ?? '-' }}</td>
                                    <td class="p-4 font-semibold">
                                        Rp{{ number_format($order->cart->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        @php
                                            $statusInfo = $statuses[$order->no_order] ?? null;
                                            $statusText = $statusInfo->status ?? ($order->status ?? 'pending');
                                            $statusClass =
                                                $statusInfo->bg_color ??
                                                'text-white text-center bg-gray-500 w-fit rounded-xl';
                                        @endphp
                                        <span class="{{ $statusClass }} px-3 py-1 text-xs font-semibold">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            @if (in_array($order->status, [null, 'pending'], true) && $order->payment_type === 'online')
                                                {{-- Pending QRIS → Resume button --}}
                                                <a href="{{ route('order-resume-online', ['id' => $order->id]) }}"
                                                    class="w-9 h-9 flex items-center justify-center bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 hover:scale-105 transition"
                                                    title="Lanjutkan Pembayaran QRIS">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                            @else
                                                {{-- Settled → Done (Archive) button --}}
                                                <form action="{{ route('archive', ['orderId' => $order->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                        title="Done / Arsipkan">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="post"
                                                action="{{ route('delorder', ['id' => $order->id]) }}"
                                                class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- OPEN BILL PAYMENT MODAL -->
    <div id="openBillModal"
        class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white rounded-xl shadow-xl relative w-full max-w-2xl flex flex-col h-[640px] max-h-[92vh] border border-gray-200">
            <button type="button" id="closeOpenBillModal"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 transition z-10">
                <i class="fas fa-times"></i>
            </button>

            <!-- Header -->
            <div class="px-8 pt-7 pb-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider">Collect Payment <span id="obChairLabel" class="text-gray-900 normal-case"></span></h2>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-sm text-gray-500">Total</span>
                    <span id="obTotalLabel" class="text-3xl font-bold text-gray-900 tabular-nums">Rp 0</span>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 px-8">
                <button type="button" data-tab="cash"
                    class="obTab flex-1 py-3 text-sm font-semibold border-b-2 border-gray-900 text-gray-900 transition">
                    Cash
                </button>
                <button type="button" data-tab="cashless"
                    class="obTab flex-1 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition">
                    Cashless
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-8 py-6">
                <!-- Tunai -->
                <div id="obCashTab" class="obTabContent h-full">
                    <form method="post" action="{{ route('checkout') }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="payment_method" value="cash">
                        <input type="hidden" name="cart_id" class="obCartIdInput">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Cash Received</label>
                                <input type="number" name="cash_received" id="obCashReceived" min="0" required
                                    class="w-full rounded-lg border border-gray-300 p-4 text-2xl font-semibold text-center tabular-nums focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"
                                    placeholder="0">
                            </div>
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Change</span>
                                    <span id="obChangeDisplay" class="font-bold text-xl text-gray-900 tabular-nums">Rp 0</span>
                                </div>
                                <p id="obCashWarning" class="text-xs text-red-600 mt-2 hidden">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Cash received is less than the total.
                                </p>
                            </div>
                        </div>

                        <div class="flex-1"></div>

                        <button type="submit" id="obCashSubmitBtn"
                            class="w-full py-3.5 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                            disabled>
                            Collect Payment
                        </button>
                    </form>
                </div>

                <!-- Non-Tunai -->
                <div id="obCashlessTab" class="obTabContent h-full hidden">
                    <form method="post" action="{{ route('checkout') }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="payment_method" id="obCashlessMethod" value="edc">
                        <input type="hidden" name="cart_id" class="obCartIdInput">

                        <!-- EDC / QRIS sub-toggle -->
                        <div class="grid grid-cols-2 gap-1 bg-gray-100 rounded-lg p-1 mb-5">
                            <button type="button" data-method="edc"
                                class="obCashlessSubTab py-2 rounded-md text-sm font-semibold bg-white shadow-sm text-gray-900 transition">
                                EDC
                            </button>
                            <button type="button" data-method="online"
                                class="obCashlessSubTab py-2 rounded-md text-sm font-semibold text-gray-500 hover:text-gray-700 transition">
                                QRIS
                            </button>
                        </div>

                        <!-- EDC fields -->
                        <div id="obEdcFields" class="obCashlessFields space-y-4">
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
                        <div id="obQrisFields" class="obCashlessFields hidden">
                            <div class="rounded-lg border border-gray-200 p-5">
                                <p class="text-sm text-gray-600">
                                    QRIS from Midtrans will appear after clicking <strong>Collect Payment</strong>. The bill will be closed after the QRIS is paid.
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
                            Collect Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('orderSuccess'))
        @php $os = session('orderSuccess'); @endphp
        <div id="orderSuccessModal"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 px-4">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl text-center space-y-4">
                <div class="bg-green-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Payment Successful</h2>
                    <p class="text-sm text-gray-500 mt-1">Order: <span class="font-mono">{{ $os['no_order'] }}</span></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-left text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total</span>
                        <span class="font-bold">Rp{{ number_format($os['total'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Method</span>
                        <span class="font-bold uppercase">{{ $os['payment_method'] }}</span>
                    </div>
                    @if (! empty($os['cash_received']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cash Received</span>
                            <span class="font-bold">Rp{{ number_format($os['cash_received'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Change</span>
                            <span class="font-bold text-green-600">Rp{{ number_format($os['change'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('order-receipt', ['id' => $os['id']]) }}" target="_blank"
                        class="flex-1 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition flex items-center justify-center gap-2">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>
                    <button type="button" id="dismissOrderSuccess"
                        class="flex-1 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            new DataTable('#myTable', {});

            $('#dismissOrderSuccess').click(function () {
                $('#orderSuccessModal').remove();
            });

            // Show message from orderonline redirect (Snap closed/error)
            const onlineMsg = sessionStorage.getItem('orderOnlineMessage');
            if (onlineMsg) {
                sessionStorage.removeItem('orderOnlineMessage');
                Swal.fire({
                    title: 'Info',
                    text: onlineMsg,
                    icon: 'info',
                    confirmButtonColor: '#6b7280',
                    confirmButtonText: 'OK'
                });
            }

            $(document).on('click', '.delete-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus order ini?',
                    text: 'Stok akan dikembalikan jika order sudah settled.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            $(document).on('click', '.cancel-bill-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Batalkan open bill?',
                    text: 'Item di bill akan dihapus. Tidak ada stok yang berubah.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            const obModal = $('#openBillModal');
            const formatRp = (n) => 'Rp' + Number(n).toLocaleString('id-ID');
            let obTotal = 0;

            $('.openBillPay').click(function () {
                const cartId = $(this).data('cart-id');
                const total = parseInt($(this).data('total')) || 0;
                const chair = $(this).data('chair');
                obTotal = total;

                $('.obCartIdInput').val(cartId);
                $('#obTotalLabel').text(formatRp(total));
                $('#obChairLabel').text('Meja ' + chair);
                $('#obCashReceived').val('');
                $('#obChangeDisplay').text('Rp0').removeClass('text-red-600').addClass('text-gray-900');
                $('#obCashWarning').addClass('hidden');
                $('#obCashSubmitBtn').prop('disabled', true);

                $('.obTab').removeClass('border-gray-900 text-gray-900').addClass('border-transparent text-gray-400');
                $('.obTab[data-tab="cash"]').removeClass('border-transparent text-gray-400').addClass('border-gray-900 text-gray-900');
                $('.obTabContent').addClass('hidden');
                $('#obCashTab').removeClass('hidden');

                // Reset cashless sub-toggle to EDC
                $('.obCashlessSubTab').removeClass('bg-white shadow-sm text-gray-900').addClass('text-gray-500 hover:text-gray-700');
                $('.obCashlessSubTab[data-method="edc"]').removeClass('text-gray-500 hover:text-gray-700').addClass('bg-white shadow-sm text-gray-900');
                $('#obCashlessMethod').val('edc');
                $('.obCashlessFields').addClass('hidden');
                $('#obEdcFields').removeClass('hidden');

                obModal.removeClass('hidden');
            });

            $('#closeOpenBillModal, #openBillModal').click(function (e) {
                if (e.target === this) obModal.addClass('hidden');
            });

            $('.obTab').click(function () {
                const tab = $(this).data('tab');
                $('.obTab').removeClass('border-gray-900 text-gray-900').addClass('border-transparent text-gray-400');
                $('.obTabContent').addClass('hidden');
                $(this).removeClass('border-transparent text-gray-400').addClass('border-gray-900 text-gray-900');
                if (tab === 'cash') {
                    $('#obCashTab').removeClass('hidden');
                } else if (tab === 'cashless') {
                    $('#obCashlessTab').removeClass('hidden');
                }
            });

            $('.obCashlessSubTab').click(function () {
                const method = $(this).data('method');
                $('.obCashlessSubTab').removeClass('bg-white shadow-sm text-gray-900').addClass('text-gray-500 hover:text-gray-700');
                $(this).removeClass('text-gray-500 hover:text-gray-700').addClass('bg-white shadow-sm text-gray-900');

                $('#obCashlessMethod').val(method);
                $('.obCashlessFields').addClass('hidden');
                if (method === 'edc') {
                    $('#obEdcFields').removeClass('hidden');
                } else if (method === 'online') {
                    $('#obQrisFields').removeClass('hidden');
                }
            });

            $('#obCashReceived').on('input', function () {
                const received = parseInt($(this).val()) || 0;
                const change = received - obTotal;
                if (change >= 0) {
                    $('#obChangeDisplay').text(formatRp(change)).removeClass('text-red-600').addClass('text-gray-900');
                    $('#obCashWarning').addClass('hidden');
                    $('#obCashSubmitBtn').prop('disabled', false);
                } else {
                    $('#obChangeDisplay').text(formatRp(change)).removeClass('text-gray-900').addClass('text-red-600');
                    $('#obCashWarning').removeClass('hidden');
                    $('#obCashSubmitBtn').prop('disabled', true);
                }
            });
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
